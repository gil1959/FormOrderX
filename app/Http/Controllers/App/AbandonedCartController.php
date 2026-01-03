<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AbandonedSession;
use Illuminate\Http\Request;
use App\Models\FollowUpLog;
use Illuminate\Support\Carbon;
use App\Models\Form;
use App\Models\AppSetting;


class AbandonedCartController extends Controller
{
    public function index(Request $request)
{
    $q = AbandonedSession::query()
        ->with('form')
        ->where('user_id', $request->user()->id)
        ->where('converted', false)
        ->orderByDesc('last_activity_at');

    // ==== FILTER BARU: produk (form_id) ====
    if ($request->filled('form_id')) {
        $q->where('form_id', (int) $request->input('form_id'));
    }

    // ==== FILTER BARU: tanggal (last_activity_at) ====
    if ($request->filled('date_from')) {
        $from = Carbon::parse($request->input('date_from'))->startOfDay();
        $q->where('last_activity_at', '>=', $from);
    }

    if ($request->filled('date_to')) {
        $to = Carbon::parse($request->input('date_to'))->endOfDay();
        $q->where('last_activity_at', '<=', $to);
    }

    $sessions = $q->paginate(20)->withQueryString();

    $forms = Form::query()
        ->where('user_id', $request->user()->id)
        ->orderBy('name')
        ->get(['id', 'name']);
$tplSetting = AppSetting::where('user_id', $request->user()->id)
    ->where('key', 'message_templates')
    ->first();

$messageTemplates = $tplSetting?->value ?? [];
if (!is_array($messageTemplates)) $messageTemplates = [];

$abandonedTemplates = $messageTemplates['abandoned'] ?? [];
if (!is_array($abandonedTemplates)) $abandonedTemplates = [];
    return view('app.abandoned.index', compact('sessions', 'forms','abandonedTemplates'));
}

    public function destroy(Request $request, AbandonedSession $session)
    {
        abort_unless($session->user_id === $request->user()->id, 403);

        $session->delete();

        return back()->with('success', 'Abandoned cart berhasil dihapus.');
    }
    public function storeFollowUp(Request $request, AbandonedSession $session)
{
    abort_unless($session->user_id === $request->user()->id, 403);

    $data = $request->validate([
        'key' => ['required', 'string', 'max:32'], // 'abandoned'
        'channel' => ['nullable', 'string', 'max:16'],
        'phone' => ['nullable', 'string', 'max:32'],
        'message' => ['nullable', 'string'],
        'sent_at' => ['nullable', 'date'],
    ]);

    $key = $data['key'];
    $channel = $data['channel'] ?? 'whatsapp';
    $sentAt = isset($data['sent_at']) ? Carbon::parse($data['sent_at']) : now();

    FollowUpLog::create([
        'user_id' => $request->user()->id,
        'subject_type' => 'abandoned',
        'subject_id' => $session->id,
        'channel' => $channel,
        'key' => $key,
        'phone' => $data['phone'] ?? null,
        'message' => $data['message'] ?? null,
        'sent_at' => $sentAt,
    ]);

 // status global (pernah followup)
if (empty($session->followup_sent_at)) {
    $session->followup_sent_at = $sentAt;
}

// status terakhir (biar tetap ada info last)
$session->last_followup_key = $key;
$session->last_followup_at = $sentAt;

// status per-step (biar FU1..FU4 bisa ijo semua)
if ($key === 'fu1' && empty($session->followup1_sent_at)) $session->followup1_sent_at = $sentAt;
if ($key === 'fu2' && empty($session->followup2_sent_at)) $session->followup2_sent_at = $sentAt;
if ($key === 'fu3' && empty($session->followup3_sent_at)) $session->followup3_sent_at = $sentAt;
if ($key === 'fu4' && empty($session->followup4_sent_at)) $session->followup4_sent_at = $sentAt;

$session->save();


    return response()->json(['ok' => true]);
}

}
