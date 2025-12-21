<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AbandonedSession;
use Illuminate\Http\Request;


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
}
