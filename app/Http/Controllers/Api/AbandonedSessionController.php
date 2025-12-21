<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbandonedSession;
use App\Models\Form;
use Illuminate\Http\Request;

class AbandonedSessionController extends Controller
{
    public function touch(Request $request, string $token)
    {
        $form = Form::where('embed_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $payload = $request->validate([
            'session_key' => ['required', 'string', 'max:191'],
            'data'        => ['nullable', 'array'],
            'source_url'  => ['nullable', 'string', 'max:2048'],
        ]);

        AbandonedSession::updateOrCreate(
            [
                'form_id'      => $form->id,
                'user_id'      => $form->user_id,
                'session_key'  => $payload['session_key'],
            ],
            [
                'data'             => [
                    'fields'     => $payload['data'] ?? null,
                    'source_url' => $payload['source_url'] ?? null,
                ],
                'converted'        => false,
                'last_activity_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
        ]);
    }
}
