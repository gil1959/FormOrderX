<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AbandonedSession;
use Illuminate\Http\Request;
use App\Models\FollowUpLog;
use Illuminate\Support\Carbon;


class AbandonedCartController extends Controller
{
    public function index(Request $request)
    {
        $sessions = AbandonedSession::query()
            ->with('form')
            ->where('user_id', $request->user()->id)
            ->where('converted', false)
            ->orderByDesc('last_activity_at')
            ->paginate(20)
            ->withQueryString();

        return view('app.abandoned.index', compact('sessions'));
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

    // Abandoned cuma 1 status
    $session->followup_sent_at = $sentAt;
    $session->last_followup_key = $key;
    $session->last_followup_at = $sentAt;
    $session->save();

    return response()->json(['ok' => true]);
}

}
