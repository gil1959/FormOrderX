{{-- resources/views/admin/forms/preview.blade.php --}}
@extends('layouts.app')

@section('header')
  <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold tracking-tight text-slate-900">Preview Form</h1>
      <p class="mt-1 text-sm text-slate-600">{{ $form->name }}</p>
      <p class="mt-1 text-xs text-slate-500">Begini tampilan form yang akan muncul di landing page / embed.</p>
    </div>

    <a href="{{ route('app.forms.fields.edit', $form) }}" class="btn-outline">&larr; Kelola Field</a>
  </div>
@endsection

@section('content')
  @php
    $settings = $form->settings ?? [];

    // layout
    $layout     = $settings['layout'] ?? [];
    $template   = $layout['template'] ?? 'right_sidebar';
    $background = $layout['background'] ?? 'white';

    $bgClass = match($background) {
      'soft_green' => 'bg-emerald-50',
      'soft_beige' => 'bg-amber-50',
      'soft_gray'  => 'bg-slate-50',
      default      => 'bg-white',
    };

    // product
    $product        = $settings['product'] ?? [];
    $showImage      = $product['show_image'] ?? false;
    $imageUrl       = $product['image_url'] ?? null;
    $showGuarantee  = $product['show_guarantee'] ?? false;
    $guaranteeLabel = $product['guarantee_label'] ?? null;

    // variation
    $variation  = $settings['variation'] ?? [];
    $varEnabled = $variation['enabled'] ?? false;
    $varType    = $variation['type'] ?? 'radio';
    $varLabel   = $variation['label'] ?? 'Pilih Varian';
    $varOptions = $variation['options'] ?? [];

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

  <div class="rounded-2xl border border-slate-200 overflow-hidden">
    <div class="p-6 {{ $bgClass }}">
      <div class="max-w-6xl mx-auto">

        @if ($template === 'right_sidebar')
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div class="flex flex-col items-center">
              @if ($showImage && $imageUrl)
                <img src="{{ $imageUrl }}" class="max-h-56 rounded-2xl border border-slate-200 bg-white object-contain mb-3">
              @endif

              @if ($showGuarantee && $guaranteeLabel)
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200">
                  {{ $guaranteeLabel }}
                </span>
              @endif
            </div>

            <div>
              @include('admin.forms._preview_form_inner')
            </div>
          </div>

        @elseif ($template === 'left_sidebar')
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div>
              @include('admin.forms._preview_form_inner')
            </div>

            <div class="flex flex-col items-center">
              @if ($showImage && $imageUrl)
                <img src="{{ $imageUrl }}" class="max-h-56 rounded-2xl border border-slate-200 bg-white object-contain mb-3">
              @endif

              @if ($showGuarantee && $guaranteeLabel)
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200">
                  {{ $guaranteeLabel }}
                </span>
              @endif
            </div>
          </div>

        @else
          <div class="max-w-xl mx-auto">
            <div class="card-solid p-8">
              @if ($showImage && $imageUrl)
                <div class="flex justify-center mb-4">
                  <img src="{{ $imageUrl }}" class="max-h-56 rounded-2xl border border-slate-200 bg-white object-contain">
                </div>
              @endif

              @if ($showGuarantee && $guaranteeLabel)
                <div class="text-center mb-3">
                  <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200">
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
  </div>
@endsection
