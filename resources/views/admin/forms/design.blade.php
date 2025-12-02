{{-- resources/views/admin/forms/design.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pengaturan Tampilan Form
            </h2>

            <div class="flex items-center gap-3 text-xs">
                <a href="{{ route('admin.forms.index') }}" class="text-gray-500 hover:text-gray-700">
                    &larr; Kembali ke Form Saya
                </a>

                <a href="{{ route('admin.forms.preview', $form) }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-md border border-indigo-500 text-indigo-600 hover:bg-indigo-50">
                    Preview
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-3 rounded-md bg-green-50 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $form->name }}
                </h3>
                @if ($form->description)
                    <p class="text-sm text-gray-600">
                        {{ $form->description }}
                    </p>
                @endif
            </div>

           <form method="POST"
      action="{{ route('admin.forms.design.update', $form) }}"
      enctype="multipart/form-data"
      class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- ================== TEMPLATE & LAYOUT ================== --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            Template &amp; Layout
                        </h4>

                        <p class="text-xs text-gray-500 mb-4">
                            Atur posisi form dan warna background agar menyatu dengan desain landing page.
                        </p>

                        @php
                            $layout    = $settings['layout'] ?? [];
                            $template  = old('layout.template', $layout['template'] ?? 'right_sidebar');
                            $background = old('layout.background', $layout['background'] ?? 'white');
                        @endphp

                        {{-- PILIH TEMPLATE POSISI --}}
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-gray-700 mb-1">
                                Pilih Template Posisi
                            </p>

                            <div class="space-y-2 text-xs">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="layout[template]"
                                        value="right_sidebar"
                                        class="h-4 w-4 text-indigo-600 border-gray-300"
                                        {{ $template === 'right_sidebar' ? 'checked' : '' }}
                                    >
                                    <div>
                                        <div class="font-semibold text-gray-900">Kanan Sidebar</div>
                                        <div class="text-[11px] text-gray-500">
                                            Form di kanan, konten di kiri.
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="layout[template]"
                                        value="left_sidebar"
                                        class="h-4 w-4 text-indigo-600 border-gray-300"
                                        {{ $template === 'left_sidebar' ? 'checked' : '' }}
                                    >
                                    <div>
                                        <div class="font-semibold text-gray-900">Kiri Sidebar</div>
                                        <div class="text-[11px] text-gray-500">
                                            Form di kiri, konten di kanan.
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="layout[template]"
                                        value="no_sidebar"
                                        class="h-4 w-4 text-indigo-600 border-gray-300"
                                        {{ $template === 'no_sidebar' ? 'checked' : '' }}
                                    >
                                    <div>
                                        <div class="font-semibold text-gray-900">Tanpa Sidebar</div>
                                        <div class="text-[11px] text-gray-500">
                                            Form full lebar, cocok di tengah halaman.
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- PILIH WARNA BACKGROUND --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-700 mb-1">
                                Pilih Warna Background Form
                            </p>

                            @php
                                $bgOptions = [
                                    'white'      => '#ffffff',
                                    'soft_green' => '#ecfdf3',
                                    'soft_beige' => '#fffbeb',
                                    'soft_gray'  => '#f8fafc',
                                ];
                            @endphp

                            <div class="flex flex-wrap items-center gap-4 text-[11px]">
                                @foreach ($bgOptions as $key => $color)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="layout[background]"
                                            value="{{ $key }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300"
                                            {{ $background === $key ? 'checked' : '' }}
                                        >
                                        <span class="w-6 h-6 rounded-md border border-gray-300 inline-block"
                                              style="background-color: {{ $color }}"></span>
                                        <span class="text-gray-700">
                                            {{ ucwords(str_replace('_', ' ', $key)) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ================== GAMBAR & GARANSI ================== --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            Gambar Produk &amp; Label Garansi
                        </h4>

                        @php
                            $product        = $settings['product'] ?? [];
                            $showImage      = old('product.show_image', $product['show_image'] ?? false);
                            $imageUrl       = old('product.image_url', $product['image_url'] ?? '');
                            $showGuarantee  = old('product.show_guarantee', $product['show_guarantee'] ?? false);
                            $guaranteeLabel = old('product.guarantee_label', $product['guarantee_label'] ?? '100% Jaminan Kepuasan');
                        @endphp

                        <div class="space-y-4 text-xs">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="product[show_image]" value="1"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                       {{ $showImage ? 'checked' : '' }}>
                                <span class="text-gray-700">Tampilkan gambar produk di atas form?</span>
                            </label>

                           @if ($imageUrl)
    <div class="mb-3">
        <p class="text-[11px] text-gray-600 mb-1">Preview Gambar Saat Ini</p>
        <img src="{{ asset($imageUrl) }}"
             alt="Gambar produk"
             class="max-h-32 rounded-md border border-gray-200 object-contain bg-white">
    </div>
@endif

<div class="space-y-1">
    <label class="block text-gray-700 mb-1">
        Upload Gambar Produk
    </label>
    <input type="file"
           name="product_image"
           accept="image/*"
           class="block w-full text-xs text-gray-700
                  file:mr-3 file:py-1.5 file:px-3
                  file:rounded-md file:border-0
                  file:text-xs file:font-semibold
                  file:bg-indigo-50 file:text-indigo-700
                  hover:file:bg-indigo-100">

    <p class="text-[10px] text-gray-500">
        Pilih file gambar dari perangkat (maks. 2MB). Jika tidak memilih gambar baru,
        sistem akan memakai gambar yang sudah ada.
    </p>

    {{-- opsional: kalau masih mau kasih opsi URL manual --}}
    <div class="pt-2">
        <label class="block text-gray-700 mb-1">
            Atau pakai URL gambar (opsional)
        </label>
        <input type="text" name="product[image_url]" value="{{ $imageUrl }}"
               class="w-full border-gray-300 rounded-md shadow-sm text-xs"
               placeholder="https://...">
        <p class="text-[10px] text-gray-500 mt-1">
            Kalau isi ini, URL akan dipakai kalau tidak ada upload file baru.
        </p>
    </div>
</div>

                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="product[show_guarantee]" value="1"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                       {{ $showGuarantee ? 'checked' : '' }}>
                                <span class="text-gray-700">Tampilkan label garansi kecil di atas judul form?</span>
                            </label>

                            <div>
                                <label class="block text-gray-700 mb-1">
                                    Teks Label Garansi
                                </label>
                                <input type="text" name="product[guarantee_label]" value="{{ $guaranteeLabel }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs">
                            </div>
                        </div>
                    </div>

                    {{-- ================== VARIASI PRODUK ================== --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            Variasi Produk
                        </h4>

                        @php
                            $variation  = $settings['variation'] ?? [];
                            $varEnabled = old('variation.enabled', $variation['enabled'] ?? false);
                            $varType    = old('variation.type', $variation['type'] ?? 'radio');
                            $varLabel   = old('variation.label', $variation['label'] ?? 'Pilih Varian');
                           $optionsText = old(
    'variation.options_text',
    collect($variation['options'] ?? [])->pluck('label')->implode("\n")
);
                        @endphp

                        <div class="space-y-4 text-xs">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="variation[enabled]" value="1"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                       {{ $varEnabled ? 'checked' : '' }}>
                                <span class="text-gray-700">Aktifkan variasi produk?</span>
                            </label>

                            <div>
                                <label class="block text-gray-700 mb-1">
                                    Tipe Variasi
                                </label>

                                <div class="space-y-1 text-xs">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="variation[type]"
                                            value="radio"
                                            class="h-4 w-4 text-indigo-600 border-gray-300"
                                            {{ $varType === 'radio' ? 'checked' : '' }}
                                        >
                                        <span class="text-gray-700">Radio (pilihan bulat)</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="variation[type]"
                                            value="dropdown"
                                            class="h-4 w-4 text-indigo-600 border-gray-300"
                                            {{ $varType === 'dropdown' ? 'checked' : '' }}
                                        >
                                        <span class="text-gray-700">Dropdown (select box)</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1">
                                    Label Variasi
                                </label>
                                <input type="text" name="variation[label]" value="{{ $varLabel }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs">
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1">
                                    Daftar Opsi (1 baris = 1 opsi)
                                </label>
                                <textarea name="variation[options_text]"
                                          class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                          rows="4"
                                          placeholder="Contoh:
Paket Hemat
Paket Normal
Paket Premium">{{ $optionsText }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ================== TOMBOL SUBMIT ================== --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            Tombol Submit
                        </h4>

                        @php
                            $button   = $settings['button'] ?? [];
                            $btnLabel = old('button.label', $button['label'] ?? 'KIRIM');
                            $btnColor = old('button.color', $button['color'] ?? 'blue');
                            $btnShape = old('button.shape', $button['shape'] ?? 'pill');
                            $btnColors = [
                                'blue'   => '#2563eb',
                                'green'  => '#16a34a',
                                'orange' => '#f97316',
                                'red'    => '#dc2626',
                            ];
                        @endphp

                        <div class="space-y-4 text-xs">
                            <div>
                                <label class="block text-gray-700 mb-1">
                                    Teks Tombol
                                </label>
                                <input type="text" name="button[label]" value="{{ $btnLabel }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs">
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-700 mb-1">
                                    Warna Tombol
                                </p>

                                <div class="flex flex-wrap items-center gap-4 text-[11px]">
                                    @foreach ($btnColors as $key => $color)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                type="radio"
                                                name="button[color]"
                                                value="{{ $key }}"
                                                class="h-4 w-4 text-indigo-600 border-gray-300"
                                                {{ $btnColor === $key ? 'checked' : '' }}
                                            >
                                            <span class="w-7 h-7 rounded-md shadow"
                                                  style="background-color: {{ $color }}"></span>
                                            <span class="text-gray-700">
                                                {{ ucfirst($key) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-700 mb-1">
                                    Bentuk Tombol
                                </p>

                                <div class="space-y-1 text-xs">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="button[shape]"
                                            value="square"
                                            class="h-4 w-4 text-indigo-600 border-gray-300"
                                            {{ $btnShape === 'square' ? 'checked' : '' }}
                                        >
                                        <span class="text-gray-700">Kotak</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="button[shape]"
                                            value="rounded"
                                            class="h-4 w-4 text-indigo-600 border-gray-300"
                                            {{ $btnShape === 'rounded' ? 'checked' : '' }}
                                        >
                                        <span class="text-gray-700">Rounded</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="button[shape]"
                                            value="pill"
                                            class="h-4 w-4 text-indigo-600 border-gray-300"
                                            {{ $btnShape === 'pill' ? 'checked' : '' }}
                                        >
                                        <span class="text-gray-700">Pill (oval)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================== TRACKING PIXEL ================== --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 md:col-span-2">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            Tracking (Facebook Pixel, GTM, TikTok)
                        </h4>

                        @php
                            $tracking = $settings['tracking'] ?? [];
                            $fbIdsText   = old('tracking.facebook_pixel_ids_text', implode(', ', $tracking['facebook_pixel_ids'] ?? []));
                            $fbEventsTxt = old('tracking.facebook_events_text', implode(', ', $tracking['facebook_events'] ?? []));
                            $gtmIdsText  = old('tracking.gtm_ids_text', implode(', ', $tracking['gtm_ids'] ?? []));
                            $ttIdsText   = old('tracking.tiktok_pixel_ids_text', implode(', ', $tracking['tiktok_pixel_ids'] ?? []));
                        @endphp

                        <div class="grid md:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block text-gray-700 mb-1">
                                    Facebook Pixel IDs
                                </label>
                                <input type="text" name="tracking[facebook_pixel_ids_text]" value="{{ $fbIdsText }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                       placeholder="ID1, ID2, ...">
                                <p class="text-[10px] text-gray-500 mt-1">
                                    Pisahkan dengan koma jika lebih dari satu.
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1">
                                    Facebook Pixel Events
                                </label>
                                <input type="text" name="tracking[facebook_events_text]" value="{{ $fbEventsTxt }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                       placeholder="Lead, Purchase, CompleteRegistration">
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1">
                                    Google Tag Manager IDs
                                </label>
                                <input type="text" name="tracking[gtm_ids_text]" value="{{ $gtmIdsText }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                       placeholder="GTM-XXXXXX, GTM-YYYYYY">
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1">
                                    TikTok Pixel IDs
                                </label>
                                <input type="text" name="tracking[tiktok_pixel_ids_text]" value="{{ $ttIdsText }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                       placeholder="ID1, ID2, ...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-primary-button>
                        Simpan Pengaturan
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
