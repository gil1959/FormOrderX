<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->validate([
            'context'  => ['required', 'string', 'in:orders,abandoned'],
            'key'      => ['required', 'string', 'max:32'],
            'template' => ['nullable', 'string'],
        ]);

        $userId = $request->user()->id;

        $setting = AppSetting::where('user_id', $userId)
            ->where('key', 'message_templates')
            ->first();

        $value = $setting?->value ?? [];
        if (!is_array($value)) $value = [];

        if (!isset($value[$data['context']]) || !is_array($value[$data['context']])) {
            $value[$data['context']] = [];
        }

        $value[$data['context']][$data['key']] = $data['template'] ?? '';

        AppSetting::updateOrCreate(
            ['user_id' => $userId, 'key' => 'message_templates'],
            ['value' => $value]
        );

        return response()->json(['ok' => true]);
    }
}
