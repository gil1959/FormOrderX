<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EmbedNonceController
{
    /**
     * Issue nonce single-use untuk submit embed.
     * Dipanggil oleh JS embed (tanpa ganggu UI).
     */
    public function issue(Request $request, string $token)
    {
        // Nonce random, single-use, TTL pendek
        $nonce = Str::random(48);

        // Simpan marker valid untuk token + nonce (di-cache, bukan DB)
        Cache::put($this->cacheKey($token, $nonce), 1, now()->addMinutes(10));

        return response()->json([
            'nonce' => $nonce,
        ], 200);
    }

    private function cacheKey(string $token, string $nonce): string
    {
        return 'embed_nonce:' . $token . ':' . hash('sha256', $nonce);
    }
}
