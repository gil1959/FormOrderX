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
            'is_active'   => $data['is_active'] ?? true,

            'settings' => [
                'layout' => [
                    'template'   => 'right_sidebar',
                    'background' => 'white',
                ],
                'button' => [
                    'label' => 'KIRIM',
                    'color' => 'blue',
                    'shape' => 'pill',
                ],
                'product' => [
                    'show_image'      => true,
                    'image_url'       => null,
                    'show_guarantee'  => true,
                    'guarantee_label' => '100% Jaminan Kepuasan',
                ],
                'variation' => [
                    'enabled' => false,
                    'type'    => 'radio',
                    'label'   => 'Pilih Varian',
                    'options' => [],
                ],
                'tracking' => [
                    'facebook_pixel_ids' => [],
                    'facebook_events'    => [],
                    'gtm_ids'            => [],
                    'tiktok_pixel_ids'   => [],
                ],
            ],
        ]);

        return redirect()
            ->route('app.forms.index')
            ->with('success', 'Form "' . $form->name . '" berhasil dibuat. Embed code siap digunakan.');
    }

    /**
     * Preview form di dalam dashboard (bukan embed).
     */
    public function preview(Form $form)
    {
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

        $validated = $request->validate([
            'layout.template'          => ['required', 'string'],
            'layout.background'        => ['required', 'string'],

            'product.show_image'       => ['nullable', 'boolean'],
            'product.image_url'        => ['nullable', 'string'],
            'product.show_guarantee'   => ['nullable', 'boolean'],
            'product.guarantee_label'  => ['nullable', 'string'],
            'product_image'            => ['nullable', 'image', 'max:2048'],

            'variation.enabled'        => ['nullable', 'boolean'],
            'variation.type'           => ['required', 'string'],
            'variation.label'          => ['nullable', 'string'],

            // ✅ baru: opsi variasi bentuk array dari UI
            'variation.options'            => ['nullable', 'array'],
            'variation.options.*.label'    => ['nullable', 'string', 'max:255'],
            'variation.options.*.price'    => ['nullable', 'string', 'max:50'],

            // ✅ kompatibilitas lama (kalau masih ada textarea yang submit)
            'variation.options_text'       => ['nullable', 'string'],

            'button.label'             => ['required', 'string'],
            'button.color'             => ['required', 'string'],
            'button.shape'             => ['required', 'string'],

            'tracking.facebook_pixel_ids_text' => ['nullable', 'string'],
            'tracking.facebook_events_text'    => ['nullable', 'string'],
            'tracking.gtm_ids_text'            => ['nullable', 'string'],
            'tracking.tiktok_pixel_ids_text'   => ['nullable', 'string'],
        ]);

        $layoutData    = $validated['layout']    ?? [];
        $productData   = $validated['product']   ?? [];
        $variationData = $validated['variation'] ?? [];
        $buttonData    = $validated['button']    ?? [];
        $trackingData  = $validated['tracking']  ?? [];

        // ========= HANDLE OPSI VARIASI (UI baru) =========
        $options = [];
        $seen = []; // buat anti tabrakan value

        $inputOptions = $variationData['options'] ?? [];

        if (is_array($inputOptions) && count($inputOptions) > 0) {
            foreach ($inputOptions as $row) {
                $label = trim((string)($row['label'] ?? ''));
                if ($label === '') continue;

                $rawPrice = trim((string)($row['price'] ?? ''));
                $price = null;

                if ($rawPrice !== '') {
                    $digits = preg_replace('/[^0-9]/', '', $rawPrice);
                    if ($digits !== '') $price = (int)$digits;
                }

                $baseValue = Str::slug($label);
                $value = $baseValue;

                // anti tabrakan: paket-hemat, paket-hemat-2, paket-hemat-3, dst.
                if (isset($seen[$value])) {
                    $seen[$baseValue] = ($seen[$baseValue] ?? 1) + 1;
                    $value = $baseValue . '-' . $seen[$baseValue];
                } else {
                    $seen[$value] = 1;
                    $seen[$baseValue] = $seen[$baseValue] ?? 1;
                }

                $options[] = [
                    'label' => $label,
                    'value' => $value,
                    'price' => $price,
                ];
            }
        }

        // ========= BACKWARD COMPAT: kalau UI baru kosong tapi options_text lama ada =========
        if (count($options) === 0) {
            $rawOptionsText = $variationData['options_text'] ?? '';
            if (trim($rawOptionsText) !== '') {
                $lines = preg_split('/\r\n|\r|\n/', $rawOptionsText);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') continue;

                    $label = $line;
                    $price = null;

                    if (str_contains($line, '|') || str_contains($line, '=')) {
                        $sep = str_contains($line, '|') ? '|' : '=';
                        [$left, $right] = array_pad(explode($sep, $line, 2), 2, '');
                        $label = trim($left);
                        $rawPrice = trim($right);

                        if ($rawPrice !== '') {
                            $digits = preg_replace('/[^0-9]/', '', $rawPrice);
                            if ($digits !== '') $price = (int)$digits;
                        }
                    }

                    if ($label === '') continue;

                    $baseValue = Str::slug($label);
                    $value = $baseValue;

                    if (isset($seen[$value])) {
                        $seen[$baseValue] = ($seen[$baseValue] ?? 1) + 1;
                        $value = $baseValue . '-' . $seen[$baseValue];
                    } else {
                        $seen[$value] = 1;
                        $seen[$baseValue] = $seen[$baseValue] ?? 1;
                    }

                    $options[] = [
                        'label' => $label,
                        'value' => $value,
                        'price' => $price,
                    ];
                }
            }
        }

        // ========= HANDLE UPLOAD GAMBAR =========
        $currentSettings = $form->settings ?? [];
        $currentProduct  = $currentSettings['product'] ?? [];
        $imageUrl = $currentProduct['image_url'] ?? null;

        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $path = $file->store('form-images', 'public');
            $imageUrl = url(Storage::url($path));
        } else {
            if (!empty($productData['image_url'])) {
                $imageUrl = $productData['image_url'];
            }
        }

        // ========= HANDLE TRACKING =========
        $facebookIds  = array_filter(array_map('trim', explode(',', $trackingData['facebook_pixel_ids_text'] ?? '')));
        $facebookEvts = array_filter(array_map('trim', explode(',', $trackingData['facebook_events_text'] ?? '')));
        $gtmIds       = array_filter(array_map('trim', explode(',', $trackingData['gtm_ids_text'] ?? '')));
        $tiktokIds    = array_filter(array_map('trim', explode(',', $trackingData['tiktok_pixel_ids_text'] ?? '')));

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
    public function destroy(Request $request, Form $form)
    {
        abort_unless($form->user_id === $request->user()->id, 403);

        // Ambil image_url dari settings untuk hapus file storage kalau itu file lokal
        $settings = $form->settings ?? [];
        $imageUrl = $settings['product']['image_url'] ?? null;

        // Hapus relasi dulu (biar aman walau gak ada FK cascade)
        // Sesuaikan nama relasi jika berbeda:
        if (method_exists($form, 'fields')) {
            $form->fields()->delete();
        }
        if (method_exists($form, 'submissions')) {
            $form->submissions()->delete();
        }

        // Hapus file gambar lokal (kalau disimpan di /storage/...)
        // NOTE: imageUrl di project lu disimpan sebagai url(...) jadi kita parse yang mengandung /storage/
        if ($imageUrl && is_string($imageUrl) && str_contains($imageUrl, '/storage/')) {
            $path = parse_url($imageUrl, PHP_URL_PATH); // /storage/form-images/xxx.png
            if ($path) {
                $path = ltrim($path, '/');
                // ubah storage/... -> public/...
                // Storage::url() itu biasanya /storage/xxx => disk public path xxx
                $relative = str_replace('storage/', '', $path); // form-images/xxx.png
                \Illuminate\Support\Facades\Storage::disk('public')->delete($relative);
            }
        }

        $formName = $form->name;

        $form->delete();

        return redirect()
            ->route('app.forms.index')
            ->with('success', 'Form "' . $formName . '" berhasil dihapus.');
    }
}
