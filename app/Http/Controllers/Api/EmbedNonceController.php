<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
class EmbedNonceController
{
    /**
     * Issue nonce single-use untuk submit embed.
     * Dipanggil oleh JS embed (tanpa ganggu UI).
     */
   public function issue(Request $request, string $token)
{
    $nonce = Str::random(48);

    DB::table('embed_nonces')->insert([
        'token'       => $token,
        'nonce_hash'  => hash('sha256', $nonce),
        'expires_at'  => now()->addMinutes(10),
        'consumed_at' => null,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    return response()->json([
        'nonce' => $nonce,
    ], 200);
}


    private function cacheKey(string $token, string $nonce): string
    {
        return 'embed_nonce:' . $token . ':' . hash('sha256', $nonce);
    }
}
