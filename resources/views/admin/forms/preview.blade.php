{{-- resources/views/admin/forms/preview.blade.php --}}

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
        $settings   = $form->settings ?? [];

        // layout
        $layout     = $settings['layout'] ?? [];
        $background = $layout['background'] ?? 'white';

        $bgClass = match ($background) {
            'soft_green' => 'bg-emerald-50',
            'soft_beige' => 'bg-amber-50',
            'soft_gray'  => 'bg-slate-50',
            default      => 'bg-white',
        };

        // product & guarantee
        $product        = $settings['product'] ?? [];
        $showImage      = $product['show_image'] ?? false;
        $imageUrl       = $product['image_url'] ?? null;
        $showGuarantee  = $product['show_guarantee'] ?? false;
        $guaranteeLabel = $product['guarantee_label'] ?? null;

        // variation
        $variation   = $settings['variation'] ?? [];
        $varEnabled  = $variation['enabled'] ?? false;
        $varType     = $variation['type'] ?? 'radio';
        $varLabel    = $variation['label'] ?? 'Pilih Varian';
        $varOptions  = $variation['options'] ?? [];

        // button
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

    <div class="py-10 {{ $bgClass }}">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-8">

                {{-- gambar produk --}}
                @if ($showImage && $imageUrl)
                    <div class="mb-4 flex justify-center">
                       <img src="{{ asset($imageUrl) }}" alt="Gambar produk"
     class="max-h-40 rounded-lg shadow-sm object-contain">
                    </div>
                @endif

                {{-- label garansi --}}
                @if ($showGuarantee && $guaranteeLabel)
                    <div class="mb-2 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium uppercase tracking-wide">
                            {{ $guaranteeLabel }}
                        </span>
                    </div>
                @endif

                {{-- judul form --}}
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-6">
                    {{ $form->name }}
                </h3>

                <form onsubmit="return false;" class="space-y-4">
                    {{-- fields --}}
                    @foreach ($fields as $field)
                        @php
                            $isRequired = $field->is_required ?? false;
                        @endphp

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ $field->label }}
                                @if ($isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>

                            @if ($field->type === 'textarea')
                                <textarea
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    rows="3"
                                    placeholder="{{ $field->placeholder ?? '' }}"
                                    disabled></textarea>
                            @elseif($field->type === 'select')
                                <select
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    disabled>
                                    <option value="">-- pilih --</option>
                                    @foreach (($field->options ?? []) as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="{{ $field->placeholder ?? '' }}"
                                    disabled>
                            @endif
                        </div>
                    @endforeach

                    {{-- variation preview jika aktif --}}
                    @if ($varEnabled && count($varOptions))
                        <div class="space-y-1 pt-2 border-t border-dashed border-gray-200 mt-4">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ $varLabel }}
                            </label>

                            @if ($varType === 'dropdown')
                                <select
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    disabled>
                                    <option value="">-- pilih varian --</option>
                                    @foreach ($varOptions as $opt)
    @php
        $optLabel = is_array($opt)
            ? ($opt['label'] ?? ($opt['value'] ?? json_encode($opt)))
            : $opt;
    @endphp
    <option value="{{ $optLabel }}">{{ $optLabel }}</option>
@endforeach
                                </select>
                            @else
                               <div class="flex flex-wrap gap-3">
    @foreach ($varOptions as $opt)
        @php
            $optLabel = is_array($opt)
                ? ($opt['label'] ?? ($opt['value'] ?? json_encode($opt)))
                : $opt;
        @endphp

        <label class="inline-flex items-center gap-1 text-sm text-gray-700">
            <input type="radio" disabled class="text-indigo-600 border-gray-300">
            <span>{{ $optLabel }}</span>
        </label>
    @endforeach
</div>
                            @endif
                        </div>
                    @endif

                    {{-- tombol submit preview --}}
                   <div class="mt-6 flex justify-center">
    <button
        type="button"
        class="inline-flex items-center px-6 py-2.5 rounded-full bg-emerald-600 text-white text-sm font-semibold shadow-lg shadow-emerald-500/30"
    >
        GAS (PREVIEW)
    </button>
</div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
