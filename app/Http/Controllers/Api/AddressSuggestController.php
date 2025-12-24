<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AddressSuggestController extends Controller
{
    /**
     * GET /api/address-suggest?q=pam
     * Response: { "items": [ { "label": "...", "value": "...", "postal": "....." }, ... ] }
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $limit = (int) $request->query('limit', 8);
        $limit = max(1, min($limit, 20));

        // ✅ DEBUG must be BEFORE q-length gate, so it can run with empty q
        if ($request->query('debug') === '1') {
            $path = storage_path('app/address_suggest/indonesia_kecamatan_labels.json');

            return response()->json([
                'path' => $path,
                'exists' => file_exists($path),
                'size_bytes' => file_exists($path) ? filesize($path) : null,
                'q' => $q,
                'q_len' => mb_strlen($q),
                'cache_driver' => config('cache.default'),
            ]);
        }

        // Load & normalize dataset (cached)
        $data = Cache::rememberForever('address_suggest_dataset_v2', function () {
            $path = storage_path('app/address_suggest/indonesia_kecamatan_labels.json');
            if (!file_exists($path)) return [];

            $raw = file_get_contents($path);
            $arr = json_decode($raw, true);
            if (!is_array($arr)) return [];

            $out = [];

            foreach ($arr as $row) {
                // legacy: array of string
                if (is_string($row)) {
                    $label = trim($row);
                    if ($label === '') continue;

                    $out[] = [
                        'label'  => $label,
                        'value'  => $label,
                        'postal' => null,
                    ];
                    continue;
                }

                // new: array of object {label,value,postal}
                if (is_array($row) && isset($row['label'])) {
                    $label = trim((string)($row['label'] ?? ''));
                    if ($label === '') continue;

                    $out[] = [
                        'label'  => $label,
                        'value'  => (string)($row['value'] ?? $label),
                        'postal' => isset($row['postal']) ? (string)$row['postal'] : null,
                    ];
                }
            }

            return $out;
        });

        // ✅ debug=2: verify dataset loaded (count + first item)
        if ($request->query('debug') === '2') {
            return response()->json([
                'count' => is_array($data) ? count($data) : null,
                'first' => is_array($data) && isset($data[0]) ? $data[0] : null,
            ]);
        }

        // Gate AFTER debug, so normal calls still require 3 chars
        if (mb_strlen($q) < 3) {
            return response()->json(['items' => []]);
        }

        // Search (contains)
        $needle = Str::lower($q);

        $items = [];
        foreach ($data as $item) {
            $label = (string)($item['label'] ?? '');
            if ($label === '') continue;

            if (Str::contains(Str::lower($label), $needle)) {
                $items[] = [
                    'label'  => $item['label'],
                    'value'  => $item['value'] ?? $item['label'],
                    'postal' => $item['postal'] ?? null,
                ];

                if (count($items) >= $limit) break;
            }
        }

        return response()->json(['items' => $items]);
    }
}
