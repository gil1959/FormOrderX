<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Preview Form: {{ $form->name }}
            </h2>

            <a href="{{ route('admin.forms.fields.edit', $form) }}"
               class="text-xs text-indigo-600 hover:text-indigo-800">
                &larr; Kembali ke Kelola Field
            </a>
        </div>

        <p class="mt-1 text-xs text-gray-500">
            Begini tampilan form yang akan muncul di landing page / embed.
        </p>
    </x-slot>

    @php
        $settings = $form->settings ?? [];

        // --- layout ---
        $layout     = $settings['layout'] ?? [];
        $template   = $layout['template'] ?? 'right_sidebar';
        $background = $layout['background'] ?? 'white';

        $bgClass = match($background) {
            'soft_green' => 'bg-emerald-50',
            'soft_beige' => 'bg-amber-50',
            'soft_gray'  => 'bg-slate-50',
            default      => 'bg-white',
        };

        // --- product ---
        $product        = $settings['product'] ?? [];
        $showImage      = $product['show_image'] ?? false;
        $imageUrl       = $product['image_url'] ?? null;
        $showGuarantee  = $product['show_guarantee'] ?? false;
        $guaranteeLabel = $product['guarantee_label'] ?? null;

        // --- variation ---
        $variation  = $settings['variation'] ?? [];
        $varEnabled = $variation['enabled'] ?? false;
        $varType    = $variation['type'] ?? 'radio';
        $varLabel   = $variation['label'] ?? 'Pilih Varian';
        $varOptions = $variation['options'] ?? [];

        // --- button ---
        $button   = $settings['button'] ?? [];
        $btnLabel = $button['label'] ?? 'KIRIM';
        $btnColor = $button['color'] ?? 'blue';
        $btnShape = $button['shape'] ?? 'pill';

        $btnColorClass = match ($btnColor) {
            'green'  => 'bg-emerald-600 hover:bg-emerald-700',
            'orange' => 'bg-orange-500 hover:bg-orange-600',
            'red'    => 'bg-rose-600 hover:bg-rose-700',
            default  => 'bg-indigo-600 hover:bg-indigo-700',
        };

        $btnShapeClass = match ($btnShape) {
            'square'  => 'rounded-md',
            'rounded' => 'rounded-lg',
            'pill'    => 'rounded-full',
            default   => 'rounded-full',
        };
    @endphp

    {{-- =============================================
        LAYOUT WRAPPER LEVEL 1
        ============================================= --}}
    <div class="py-10 {{ $bgClass }}">
        <div class="max-w-6xl mx-auto px-4">

            {{-- =========================
                 TEMPLATE: RIGHT SIDEBAR
               ========================= --}}
            @if ($template === 'right_sidebar')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

                    {{-- kiri = gambar --}}
                    <div class="flex flex-col items-center">
                        @if ($showImage && $imageUrl)
                            <img src="{{ $imageUrl }}"
                                 class="max-h-56 rounded-lg shadow-sm object-contain mb-3">
                        @endif

                        @if ($showGuarantee && $guaranteeLabel)
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs">
                                {{ $guaranteeLabel }}
                            </span>
                        @endif
                    </div>

                    {{-- kanan = form --}}
                    <div>
                        @include('admin.forms._preview_form_inner')
                    </div>

                </div>

            {{-- =========================
                 TEMPLATE: LEFT SIDEBAR
               ========================= --}}
            @elseif ($template === 'left_sidebar')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

                    {{-- kiri = form --}}
                    <div>
                        @include('admin.forms._preview_form_inner')
                    </div>

                    {{-- kanan = gambar --}}
                    <div class="flex flex-col items-center">
                        @if ($showImage && $imageUrl)
                            <img src="{{ $imageUrl }}"
                                 class="max-h-56 rounded-lg shadow-sm object-contain mb-3">
                        @endif

                        @if ($showGuarantee && $guaranteeLabel)
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs">
                                {{ $guaranteeLabel }}
                            </span>
                        @endif
                    </div>

                </div>

            {{-- =========================
                 TEMPLATE: NO SIDEBAR
               ========================= --}}
            @else
                <div class="max-w-xl mx-auto">

                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-8">
                        @if ($showImage && $imageUrl)
                            <div class="flex justify-center mb-4">
                                <img src="{{ $imageUrl }}"
                                     class="max-h-56 rounded-lg shadow-sm object-contain">
                            </div>
                        @endif

                        @if ($showGuarantee && $guaranteeLabel)
                            <div class="text-center mb-3">
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs">
                                    {{ $guaranteeLabel }}
                                </span>
                            </div>
                        @endif

                        @include('admin.forms._preview_form_inner')
                    </div>

                </div>
            @endif

        </div>
    </div>

</x-app-layout>