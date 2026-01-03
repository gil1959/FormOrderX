<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbandonedSession;
use App\Models\AppSetting;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;





class EmbedSubmitController extends Controller
{
    public function submit(Request $request, string $token)
    {
        $form = Form::where('embed_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $payload = $request->validate([
            'data'        => ['required', 'array'],

            'meta'        => ['required', 'array'],
            'meta.nonce'  => ['required', 'string', 'min:20', 'max:200'],
            'meta.hp'     => ['nullable', 'string', 'max:200'],
            'meta.ts'     => ['nullable', 'string', 'max:50'],

            'source_url'  => ['nullable', 'string', 'max:2048'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'summary'     => ['nullable', 'array'],
            'session_key' => ['nullable', 'string', 'max:191'],
        ]);

        $hp = (string) data_get($payload, 'meta.hp', '');
        $tsRaw = (string) data_get($payload, 'meta.ts', '');

        $isSpam = false;

        // ==============================
        // Anti-spam HARD GATE
        // 1) Nonce single-use: kalau gak valid -> langsung reject (tidak create Submission)
        // 2) Honeypot + timing: nyaring bot sederhana/replay
        // ==============================

       $nonce = (string) data_get($payload, 'meta.nonce', '');
$nonceHash = hash('sha256', $nonce);

$nonceRow = DB::table('embed_nonces')
    ->where('token', $token)
    ->where('nonce_hash', $nonceHash)
    ->whereNull('consumed_at')
    ->where('expires_at', '>', now())
    ->first();

if (!$nonceRow) {
    return response()->json([
        'success' => false,
        'message' => 'Permintaan tidak valid. Silakan refresh halaman dan coba lagi.',
    ], 429);
}

// consume nonce (single-use)
DB::table('embed_nonces')
    ->where('id', $nonceRow->id)
    ->update([
        'consumed_at' => now(),
        'updated_at'  => now(),
    ]);


        // Honeypot
        $hp = (string) data_get($payload, 'meta.hp', '');
        if (trim($hp) !== '') {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak valid.',
            ], 429);
        }

        // Time-to-submit (ms timestamp dari Date.now())
        $tsRaw = (string) data_get($payload, 'meta.ts', '');
        if ($tsRaw !== '') {
            $digits = preg_replace('/\D/', '', $tsRaw);
            if ($digits !== '' && ctype_digit($digits)) {
                $tsMs = (int) $digits;
                if ($tsMs > 0) {
                    try {
                        $renderAt = Carbon::createFromTimestampMs($tsMs);
                        $elapsed = $renderAt->diffInSeconds(now());

                        // terlalu cepat = bot/script
                        if ($elapsed < 3) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Permintaan tidak valid.',
                            ], 429);
                        }

                        // terlalu lama = replay/stale
                        if ($elapsed > 2 * 60 * 60) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Form sudah kedaluwarsa. Silakan refresh halaman dan coba lagi.',
                            ], 429);
                        }
                    } catch (\Throwable $e) {
                        // parsing gagal -> invalid (ketat)
                        return response()->json([
                            'success' => false,
                            'message' => 'Permintaan tidak valid.',
                        ], 429);
                    }
                }
            }
        }

        // Duplicate payload hash (blok walau IP beda)
        $payloadHash = hash('sha256', json_encode($payload['data']));
        $dupKey = 'embed_dup:' . $token . ':' . $payloadHash;
        if (Cache::has($dupKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak valid.',
            ], 429);
        }
        Cache::put($dupKey, 1, now()->addMinutes(10));




        // 1) Simpan order dulu (biar dashboard admin pasti masuk)
        $submission = Submission::create([
            'form_id'         => $form->id,
            'user_id'         => $form->user_id,
            'status'          => 'pending',
            'payment_status'  => 'unpaid',
            'total_price'     => $payload['total_price'] ?? null,
            'data'            => [
                'fields'  => $payload['data'],
                'summary' => $payload['summary'] ?? null,
            ],
            'source_url'      => $payload['source_url'] ?? null,
            'client_ip'       => $request->ip(),
            'user_agent'      => (string) $request->userAgent(),
            'is_spam' => $isSpam,
            'submitted_at'    => now(),
        ]);

        if ($isSpam) {
            return response()->json([
                'message' => 'Order diterima.',
                // jangan kasih redirect_url ke spam
            ], 200);
        }


        // 2) kalau submit sukses, mark abandoned session jadi converted
        if (!empty($payload['session_key'])) {
            AbandonedSession::where('form_id', $form->id)
                ->where('user_id', $form->user_id)
                ->where('session_key', $payload['session_key'])
                ->update([
                    'converted' => true,
                    'last_activity_at' => now(),
                ]);
        }

        // 3) Ambil setting WA dari app_settings (global settings per admin/owner form)
        $waSetting = AppSetting::where('user_id', $form->user_id)
            ->where('key', 'whatsapp')
            ->first();

        $wa = $waSetting?->value ?? [];
        $waEnabled = (bool)($wa['enabled'] ?? false);
        $waPhone = preg_replace('/[^0-9]/', '', (string)($wa['phone'] ?? ''));

        // 4) Build redirect URL ke WhatsApp (kalau enabled + phone valid)
        $redirectUrl = null;

        if ($waEnabled && $waPhone !== '') {
            // Summary text dari payload summary
            $lines = [];
            $summary = $payload['summary'] ?? [];

            if (is_array($summary)) {
                foreach ($summary as $row) {
                    $label = trim((string)($row['label'] ?? ''));
                    $value = trim((string)($row['value'] ?? ''));

                    if ($label === '' || $value === '') continue;
                    $lines[] = $label . ': ' . $value;
                }
            }

            $summaryText = count($lines) ? implode("\n", $lines) : '-';

            $totalText = isset($payload['total_price'])
                ? ('Rp ' . number_format((float)$payload['total_price'], 0, ',', '.'))
                : '-';

            // Template message dari settings (bisa kosong -> fallback)
            $tpl = (string)($wa['message_template'] ?? '');
            if (trim($tpl) === '') {
                $tpl =
                    "Hallo Admin, saya mau order:

{summary}

Total: {total}

Link: {source_url}";
            }

            $msg = str_replace(
                ['{form_name}', '{summary}', '{total}', '{source_url}', '{order_id}'],
                [$form->name, $summaryText, $totalText, (string)($payload['source_url'] ?? '-'), (string)$submission->id],
                $tpl
            );

            $redirectUrl = "https://wa.me/{$waPhone}?text=" . urlencode($msg);
        }

        // 5) Return JSON (frontend bisa redirect kalau redirect_url ada)
        return response()->json([
            'success'      => true,
            'message'      => 'Pesanan berhasil dikirim.',
            'id'           => $submission->id,
            'redirect_url' => $redirectUrl,
        ]);
    }
}
