<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $wa = AppSetting::where('user_id', $userId)->where('key', 'whatsapp')->first();
        $waValue = $wa?->value ?? [
            'enabled' => true,
            'phone' => '',
            'message_template' =>
            "Hallo Admin, saya mau order:\n\n{summary}\n\nTotal: {total}\n\nLink: {source_url}",
        ];

        return view('app.settings.index', [
            'whatsapp' => $waValue,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'whatsapp.enabled' => ['nullable', 'boolean'],
            'whatsapp.phone' => ['nullable', 'string', 'max:30'],
            'whatsapp.message_template' => ['nullable', 'string', 'max:4000'],
        ]);

        $userId = $request->user()->id;

        $wa = $validated['whatsapp'] ?? [];
        $value = [
            'enabled' => $request->boolean('whatsapp.enabled'),
            'phone' => $wa['phone'] ?? '',
            'message_template' => $wa['message_template'] ?? '',
        ];

        AppSetting::updateOrCreate(
            ['user_id' => $userId, 'key' => 'whatsapp'],
            ['value' => $value]
        );

        return back()->with('success', 'Settings berhasil disimpan.');
    }
}
