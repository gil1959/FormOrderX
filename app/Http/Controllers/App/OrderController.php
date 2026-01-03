<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use App\Models\FollowUpLog;
use Illuminate\Support\Carbon;
use App\Models\Form;
use App\Models\AppSetting;

class OrderController extends Controller
{
    public function index(Request $request)
{
    $q = Submission::query()
        ->with('form')
        ->where('user_id', $request->user()->id)
        ->orderByDesc('submitted_at');

    // ==== filter status yang sudah ada ====
    if ($request->filled('status')) {
        $q->where('status', $request->string('status'));
    }

    if ($request->filled('payment_status')) {
        $q->where('payment_status', $request->string('payment_status'));
    }

    // ==== FILTER BARU: produk (form_id) ====
    if ($request->filled('form_id')) {
        $q->where('form_id', (int) $request->input('form_id'));
    }

    // ==== FILTER BARU: tanggal (submitted_at) ====
    // pakai startOfDay/endOfDay biar inclusive.
    if ($request->filled('date_from')) {
        $from = Carbon::parse($request->input('date_from'))->startOfDay();
        $q->where('submitted_at', '>=', $from);
    }

    if ($request->filled('date_to')) {
        $to = Carbon::parse($request->input('date_to'))->endOfDay();
        $q->where('submitted_at', '<=', $to);
    }

    $orders = $q->paginate(20)->withQueryString();

    // list produk untuk dropdown
    $forms = Form::query()
        ->where('user_id', $request->user()->id)
        ->orderBy('name')
        ->get(['id', 'name']);
$userId = $request->user()->id;

$tplSetting = AppSetting::where('user_id', $userId)
    ->where('key', 'message_templates')
    ->first();

$messageTemplates = $tplSetting?->value ?? [];
if (!is_array($messageTemplates)) $messageTemplates = [];

$orderTemplates = $messageTemplates['orders'] ?? [];
if (!is_array($orderTemplates)) $orderTemplates = [];

    return view('app.orders.index', compact('orders', 'forms', 'orderTemplates'));
}

    public function destroy(Request $request, Submission $submission)
    {
        abort_unless($submission->user_id === $request->user()->id, 403);

        $submission->delete();

        return back()->with('success', 'Order berhasil dihapus.');
    }


    public function updateStatus(Request $request, Submission $submission)
    {
        abort_unless($submission->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'action' => ['required', 'string'],
        ]);

        $action = $data['action'];

        // mapping action dropdown -> kolom
        switch ($action) {
            case 'mark_pending':
                $submission->status = 'pending';
                break;
            case 'mark_processed':
                $submission->status = 'processed';
                break;
            case 'mark_completed':
                $submission->status = 'completed';
                break;
            case 'mark_cancelled':
                $submission->status = 'cancelled';
                break;
            case 'mark_paid':
                $submission->payment_status = 'paid';
                break;
            case 'mark_unpaid':
                $submission->payment_status = 'unpaid';
                break;
            case 'mark_refunded':
                $submission->payment_status = 'refunded';
                $submission->status = 'cancelled'; // biar konsisten
                break;
            default:
                return back()->with('error', 'Aksi tidak dikenal.');
        }

        $submission->save();

        return back()->with('success', 'Status berhasil diubah.');
    }
    public function storeFollowUp(Request $request, Submission $submission)
{
    abort_unless($submission->user_id === $request->user()->id, 403);

    $data = $request->validate([
        'key' => ['required', 'string', 'max:32'],
        'channel' => ['nullable', 'string', 'max:16'], // whatsapp|sms|call
        'phone' => ['nullable', 'string', 'max:32'],
        'message' => ['nullable', 'string'],
        'sent_at' => ['nullable', 'date'],
    ]);

    $key = $data['key'];
    $channel = $data['channel'] ?? 'whatsapp';
    $sentAt = isset($data['sent_at']) ? Carbon::parse($data['sent_at']) : now();

    // 1) LOG (audit)
    FollowUpLog::create([
        'user_id' => $request->user()->id,
        'subject_type' => 'submission',
        'subject_id' => $submission->id,
        'channel' => $channel,
        'key' => $key,
        'phone' => $data['phone'] ?? null,
        'message' => $data['message'] ?? null,
        'sent_at' => $sentAt,
    ]);

    // 2) SAVE status step W / 1-4 (yang lu minta)
    switch ($key) {
        case 'welcome':
            $submission->welcome_sent_at = $sentAt;
            break;
        case 'fu1':
            $submission->followup1_sent_at = $sentAt;
            break;
        case 'fu2':
            $submission->followup2_sent_at = $sentAt;
            break;
        case 'fu3':
            $submission->followup3_sent_at = $sentAt;
            break;
        case 'fu4':
            $submission->followup4_sent_at = $sentAt;
            break;
        default:
            // key lain (wa_processing, sms, call, dll)
            // cuma masuk log + last_followup
            break;
    }

    $submission->last_followup_key = $key;
    $submission->last_followup_at = $sentAt;
    $submission->save();

    return response()->json(['ok' => true]);
}

}
