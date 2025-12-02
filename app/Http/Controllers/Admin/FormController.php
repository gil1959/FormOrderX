<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FormController extends Controller
{
    /**
     * List semua form milik user yang login.
     */
    public function index(Request $request)
    {
        $forms = $request->user()
            ->forms()
            ->latest()
            ->paginate(10);

        return view('admin.forms.index', compact('forms'));
    }

    /**
     * Tampilkan halaman buat form baru.
     */
    public function create()
    {
        return view('admin.forms.create');
    }

    /**
     * Simpan form baru ke database.
     * - Tetap pakai relasi $user->forms()
     * - Sekarang sekalian isi kolom JSON "settings" dengan default.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price'  => ['nullable', 'numeric', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        // generate slug unik per user
        $baseSlug = Str::slug($data['name']);
        $slug     = $baseSlug;
        $i        = 1;

        while (
            Form::where('user_id', $user->id)
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $i++;
        }

        // generate embed_token unik global
        do {
            $embedToken = 'js_' . Str::random(20);
        } while (Form::where('embed_token', $embedToken)->exists());

        $form = $user->forms()->create([
            'name'        => $data['name'],
            'slug'        => $slug,
            'embed_token' => $embedToken,
            'description' => $data['description'] ?? null,
            'base_price'  => $data['base_price'] ?? null,
            'is_active'   => $data['is_active'] ?? true,

            // ⬇️ INI JSON SETTINGS DEFAULT BUAT TAMPILAN & TRACKING
            'settings'    => [
                'layout' => [
                    // posisi / layout checkout (kayak kanan sidebar, kiri, dll)
                    'template'   => 'right_sidebar', // right_sidebar | left_sidebar | no_sidebar
                    'background' => 'white',         // white | soft_green | soft_beige | soft_gray
                ],
                'button' => [
                    // pengaturan tombol submit
                    'label' => 'KIRIM',              // teks tombol
                    'color' => 'blue',               // blue | green | orange | red
                    'shape' => 'pill',               // square | rounded | pill
                ],
                'product' => [
                    // pengaturan tampilan gambar & label garansi
                    'show_image'      => true,
                    'image_url'       => null,
                    'show_guarantee'  => true,
                    'guarantee_label' => '100% Jaminan Kepuasan',
                ],
                'variation' => [
                    // variasi produk (misal Paket A/B/C)
                    'enabled' => false,              // aktif / tidak
                    'type'    => 'radio',            // radio | dropdown
                    'label'   => 'Pilih Varian',
                    'options' => [],                 // nanti diisi dari halaman Design
                ],
                'tracking' => [
                    // tracking pixel
                    'facebook_pixel_ids' => [],      // array of string
                    'facebook_events'    => [],
                    'gtm_ids'            => [],
                    'tiktok_pixel_ids'   => [],
                ],
            ],
        ]);

        return redirect()
            ->route('admin.forms.index')
            ->with('success', 'Form "' . $form->name . '" berhasil dibuat. Embed code siap digunakan.');
    }

    /**
     * Preview form di dalam dashboard (bukan embed).
     */
    public function preview(Form $form)
    {
        // pastikan ini memang form milik user yg login
        abort_unless($form->user_id === Auth::id(), 403);

        $form->load(['fields' => function ($q) {
            $q->where('is_active', true)->orderBy('order');
        }]);

        return view('admin.forms.preview', [
            'form'   => $form,
            'fields' => $form->fields,
        ]);
    }

    /**
     * Halaman "Design" untuk atur template, warna, variasi, tracking, dll.
     * View: resources/views/admin/forms/design.blade.php
     */
    public function design(Form $form)
    {
        abort_unless($form->user_id === Auth::id(), 403);

        $settings = $form->settings ?? [];

        $layout    = $settings['layout']    ?? [];
        $button    = $settings['button']    ?? [];
        $product   = $settings['product']   ?? [];
        $variation = $settings['variation'] ?? [];
        $tracking  = $settings['tracking']  ?? [];

        return view('admin.forms.design', [
            'form'     => $form,
            'settings' => [
                'layout' => [
                    'template'   => $layout['template']   ?? 'right_sidebar',
                    'background' => $layout['background'] ?? 'white',
                ],
                'button' => [
                    'label' => $button['label'] ?? 'KIRIM',
                    'color' => $button['color'] ?? 'blue',
                    'shape' => $button['shape'] ?? 'pill',
                ],
                'product' => [
                    'show_image'      => $product['show_image']      ?? true,
                    'image_url'       => $product['image_url']       ?? null,
                    'show_guarantee'  => $product['show_guarantee']  ?? true,
                    'guarantee_label' => $product['guarantee_label'] ?? '100% Jaminan Kepuasan',
                ],
                'variation' => [
                    'enabled' => $variation['enabled'] ?? false,
                    'type'    => $variation['type']    ?? 'radio',
                    'label'   => $variation['label']   ?? 'Pilih Varian',
                    'options' => $variation['options'] ?? [],
                ],
                'tracking' => [
                    'facebook_pixel_ids' => $tracking['facebook_pixel_ids'] ?? [],
                    'facebook_events'    => $tracking['facebook_events']    ?? [],
                    'gtm_ids'            => $tracking['gtm_ids']            ?? [],
                    'tiktok_pixel_ids'   => $tracking['tiktok_pixel_ids']   ?? [],
                ],
            ],
        ]);
    }

    /**
     * Simpan perubahan dari halaman "Design".
     */
    public function updateDesign(Request $request, Form $form)
    {
        abort_unless($form->user_id === Auth::id(), 403);

        // ========= VALIDASI =========
        $validated = $request->validate([
            'layout.template'          => ['required', 'string'],
            'layout.background'        => ['required', 'string'],

            'product.show_image'       => ['nullable', 'boolean'],
            'product.image_url'        => ['nullable', 'string'],
            'product.show_guarantee'   => ['nullable', 'boolean'],
            'product.guarantee_label'  => ['nullable', 'string'],
            'product_image'            => ['nullable', 'image', 'max:2048'], // 2MB

            'variation.enabled'        => ['nullable', 'boolean'],
            'variation.type'           => ['required', 'string'],
            'variation.label'          => ['nullable', 'string'],
            'variation.options_text'   => ['nullable', 'string'],

            'button.label'             => ['required', 'string'],
            'button.color'             => ['required', 'string'],
            'button.shape'             => ['required', 'string'],

            // tracking (sementara cuma disimpan apa adanya)
            'tracking.facebook_pixel_ids_text' => ['nullable', 'string'],
            'tracking.facebook_events_text'    => ['nullable', 'string'],
            'tracking.gtm_ids_text'            => ['nullable', 'string'],
            'tracking.tiktok_pixel_ids_text'   => ['nullable', 'string'],
        ]);

        // Biar aman, pecah dulu per-section
        $layoutData    = $validated['layout']    ?? [];
        $productData   = $validated['product']   ?? [];
        $variationData = $validated['variation'] ?? [];
        $buttonData    = $validated['button']    ?? [];
        $trackingData  = $validated['tracking']  ?? [];

        // ========= HANDLE OPSI VARIASI =========
        $options = [];
        $rawOptionsText = $variationData['options_text'] ?? '';

        if (trim($rawOptionsText) !== '') {
            $lines = preg_split('/\r\n|\r|\n/', $rawOptionsText);

            foreach ($lines as $line) {
                $label = trim($line);
                if ($label === '') {
                    continue;
                }

                $options[] = [
                    'label' => $label,
                    'value' => Str::slug($label),
                ];
            }
        }

        // ========= HANDLE UPLOAD GAMBAR =========
        // default pakai yang lama kalau ada
        $currentSettings = $form->settings ?? [];
        $currentProduct  = $currentSettings['product'] ?? [];

        $imageUrl = $currentProduct['image_url'] ?? null;

        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');

            // simpan ke storage/app/public/form-images
            $path = $file->store('form-images', 'public');

            // convert ke URL penuh (https://domain/storage/...)
            $imageUrl = url(Storage::url($path));
        } else {
            // kalau user isi manual URL pakai text
            if (!empty($productData['image_url'])) {
                $imageUrl = $productData['image_url'];
            }
        }

        // ========= HANDLE TRACKING (optional, biar rapi di DB) =========
        $facebookIds   = array_filter(array_map('trim', explode(',', $trackingData['facebook_pixel_ids_text'] ?? '')));
        $facebookEvts  = array_filter(array_map('trim', explode(',', $trackingData['facebook_events_text'] ?? '')));
        $gtmIds        = array_filter(array_map('trim', explode(',', $trackingData['gtm_ids_text'] ?? '')));
        $tiktokIds     = array_filter(array_map('trim', explode(',', $trackingData['tiktok_pixel_ids_text'] ?? '')));

        // ========= SUSUN SETTINGS FINAL =========
        $finalSettings = [
            'layout' => [
                'template'   => $layoutData['template']   ?? 'right_sidebar',
                'background' => $layoutData['background'] ?? 'white',
            ],
            'product' => [
                'show_image'      => $request->boolean('product.show_image'),
                'image_url'       => $imageUrl,
                'show_guarantee'  => $request->boolean('product.show_guarantee'),
                'guarantee_label' => $productData['guarantee_label'] ?? '100% Jaminan Kepuasan',
            ],
            'variation' => [
                'enabled' => $request->boolean('variation.enabled'),
                'type'    => $variationData['type']  ?? 'radio',
                'label'   => $variationData['label'] ?? 'Pilih Varian',
                'options' => $options,
            ],
            'button' => [
                'label' => $buttonData['label'] ?? 'KIRIM',
                'color' => $buttonData['color'] ?? 'blue',
                'shape' => $buttonData['shape'] ?? 'pill',
            ],
            'tracking' => [
                'facebook_pixel_ids' => $facebookIds,
                'facebook_events'    => $facebookEvts,
                'gtm_ids'            => $gtmIds,
                'tiktok_pixel_ids'   => $tiktokIds,
            ],
        ];

        $form->update([
            'settings' => $finalSettings,
        ]);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
