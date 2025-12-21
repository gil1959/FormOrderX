{{-- resources/views/admin/forms/design.blade.php --}}
@extends('layouts.app')

@section('header')
  <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold tracking-tight text-slate-900">Pengaturan Tampilan Form</h1>
      <p class="mt-1 text-sm text-slate-600">
        Form: <span class="font-semibold">{{ $form->name }}</span>
      </p>
      @if ($form->description)
        <p class="mt-1 text-xs text-slate-500">{{ $form->description }}</p>
      @endif
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('app.forms.index') }}" class="btn-outline">&larr; Kembali</a>
      <a href="{{ route('app.forms.preview', $form) }}" class="btn-soft">Preview</a>
    </div>
  </div>
@endsection

@section('content')
  <div class="max-w-6xl space-y-4">

  

    <form method="POST"
          action="{{ route('app.forms.design.update', $form) }}"
          enctype="multipart/form-data"
          class="space-y-6">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- ================== TEMPLATE & LAYOUT ================== --}}
        <div class="card-solid p-6">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h2 class="text-sm font-semibold text-slate-900">Template & Layout</h2>
              <p class="mt-1 text-xs text-slate-500">
                Atur posisi form dan warna background supaya nyatu dengan landing page.
              </p>
            </div>
          </div>

          @php
            $layout     = $settings['layout'] ?? [];
            $template   = old('layout.template', $layout['template'] ?? 'right_sidebar');
            $background = old('layout.background', $layout['background'] ?? 'white');

            $bgOptions = [
              'white'      => ['label' => 'White',      'color' => '#ffffff'],
              'soft_green' => ['label' => 'Soft Green', 'color' => '#ecfdf3'],
              'soft_beige' => ['label' => 'Soft Beige', 'color' => '#fffbeb'],
              'soft_gray'  => ['label' => 'Soft Gray',  'color' => '#f8fafc'],
            ];
          @endphp

          <div class="mt-5 space-y-4">

            <div>
              <p class="text-xs font-semibold text-slate-700 mb-2">Posisi Layout</p>

              <div class="space-y-2">
                @foreach ([
                  'right_sidebar' => ['Kanan Sidebar', 'Form di kanan, konten di kiri.'],
                  'left_sidebar'  => ['Kiri Sidebar',  'Form di kiri, konten di kanan.'],
                  'no_sidebar'    => ['Tanpa Sidebar', 'Form full lebar (cocok ditengah halaman).'],
                ] as $key => [$ttl, $desc])
                  <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 cursor-pointer hover:bg-slate-50">
                    <input
                      type="radio"
                      name="layout[template]"
                      value="{{ $key }}"
                      class="mt-1 h-4 w-4 rounded-full border-slate-300 text-slate-900 focus:ring-slate-900/20"
                      {{ $template === $key ? 'checked' : '' }}
                    >
                    <div>
                      <div class="text-sm font-semibold text-slate-900">{{ $ttl }}</div>
                      <div class="mt-1 text-xs text-slate-500">{{ $desc }}</div>
                    </div>
                  </label>
                @endforeach
              </div>

              @error('layout.template')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <p class="text-xs font-semibold text-slate-700 mb-2">Warna Background</p>

              <div class="flex flex-wrap items-center gap-3">
                @foreach ($bgOptions as $key => $cfg)
                  <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 cursor-pointer hover:bg-slate-50">
                    <input
                      type="radio"
                      name="layout[background]"
                      value="{{ $key }}"
                      class="h-4 w-4 border-slate-300 text-slate-900 focus:ring-slate-900/20"
                      {{ $background === $key ? 'checked' : '' }}
                    >
                    <span class="h-6 w-6 rounded-lg border border-slate-200 inline-block" style="background: {{ $cfg['color'] }}"></span>
                    <span class="text-xs font-semibold text-slate-700">{{ $cfg['label'] }}</span>
                  </label>
                @endforeach
              </div>

              @error('layout.background')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>

          </div>
        </div>

        {{-- ================== GAMBAR & GARANSI ================== --}}
        <div class="card-solid p-6">
          <h2 class="text-sm font-semibold text-slate-900">Gambar Produk & Label Garansi</h2>
          <p class="mt-1 text-xs text-slate-500">
            Bisa pakai upload file (utama) atau URL (fallback).
          </p>

          @php
            $product        = $settings['product'] ?? [];
            $showImage      = old('product.show_image', $product['show_image'] ?? false);
            $imageUrl       = old('product.image_url', $product['image_url'] ?? '');
            $showGuarantee  = old('product.show_guarantee', $product['show_guarantee'] ?? false);
            $guaranteeLabel = old('product.guarantee_label', $product['guarantee_label'] ?? '100% Jaminan Kepuasan');
          @endphp

          <div class="mt-5 space-y-4">
            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <input
                type="checkbox"
                name="product[show_image]"
                value="1"
                class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20"
                {{ $showImage ? 'checked' : '' }}
              >
              <div>
                <div class="text-sm font-semibold text-slate-800">Tampilkan gambar produk</div>
                <p class="mt-1 text-xs text-slate-500">Gambar muncul di atas form (atau di sisi sesuai template).</p>
              </div>
            </label>

            @if ($imageUrl)
              <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold text-slate-700">Preview gambar saat ini</p>
                <img
                  src="{{ $imageUrl }}"
                  alt="Gambar produk"
                  class="mt-3 max-h-44 w-full rounded-xl border border-slate-200 object-contain bg-white"
                >
              </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
              <label class="label">Upload gambar (utama)</label>
              <p class="help mt-1">Maks 2MB. Jika tidak upload baru, sistem pakai yang lama atau URL.</p>

              <input
                type="file"
                name="product_image"
                accept="image/*"
                class="mt-3 block w-full text-sm text-slate-700 file:mr-3 file:rounded-xl file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-900 hover:file:bg-slate-200"
              >
              @error('product_image')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror

              <div class="mt-4">
                <label class="label">Atau URL gambar (fallback)</label>
                <input
                  type="text"
                  name="product[image_url]"
                  value="{{ $imageUrl }}"
                  class="input mt-2"
                  placeholder="https://..."
                >
                @error('product.image_url')
                  <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <input
                type="checkbox"
                name="product[show_guarantee]"
                value="1"
                class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20"
                {{ $showGuarantee ? 'checked' : '' }}
              >
              <div>
                <div class="text-sm font-semibold text-slate-800">Tampilkan label garansi</div>
                <p class="mt-1 text-xs text-slate-500">Badge kecil di atas judul form.</p>
              </div>
            </label>

            <div>
              <label class="label">Teks label garansi</label>
              <input
                type="text"
                name="product[guarantee_label]"
                value="{{ $guaranteeLabel }}"
                class="input mt-2"
              >
              @error('product.guarantee_label')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        {{-- ================== VARIASI PRODUK ================== --}}
        <div class="card-solid p-6">
          <h2 class="text-sm font-semibold text-slate-900">Variasi Produk</h2>
          <p class="mt-1 text-xs text-slate-500">
            Contoh: Paket Hemat / Normal / Premium.
          </p>

          @php
            $variation  = $settings['variation'] ?? [];
            $varEnabled = old('variation.enabled', $variation['enabled'] ?? false);
            $varType    = old('variation.type', $variation['type'] ?? 'radio');
            $varLabel   = old('variation.label', $variation['label'] ?? 'Pilih Varian');

            $varOptions = old('variation.options', $variation['options'] ?? []);
            $varOptions = collect($varOptions)->map(function($o){
              return [
                'label' => is_array($o) ? ($o['label'] ?? '') : '',
                'price' => is_array($o) ? ($o['price'] ?? null) : null,
              ];
            })->values()->all();
          @endphp

          <div class="mt-5 space-y-4">
            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <input
                type="checkbox"
                name="variation[enabled]"
                value="1"
                class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20"
                {{ $varEnabled ? 'checked' : '' }}
              >
              <div>
                <div class="text-sm font-semibold text-slate-800">Aktifkan variasi</div>
                <p class="mt-1 text-xs text-slate-500">Kalau aktif, user wajib pilih varian sebelum submit.</p>
              </div>
            </label>

            <div>
              <p class="text-xs font-semibold text-slate-700 mb-2">Tipe variasi</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach (['radio' => 'Radio', 'dropdown' => 'Dropdown'] as $k => $ttl)
                  <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 cursor-pointer hover:bg-slate-50">
                    <input
                      type="radio"
                      name="variation[type]"
                      value="{{ $k }}"
                      class="h-4 w-4 border-slate-300 text-slate-900 focus:ring-slate-900/20"
                      {{ $varType === $k ? 'checked' : '' }}
                    >
                    <span class="text-sm font-semibold text-slate-800">{{ $ttl }}</span>
                  </label>
                @endforeach
              </div>
              @error('variation.type')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="label">Label variasi</label>
              <input type="text" name="variation[label]" value="{{ $varLabel }}" class="input mt-2">
              @error('variation.label')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            {{-- ✅ UI Variasi: tombol tambah + row nama/harga --}}
            <div class="mt-2">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <label class="label">Daftar Variasi</label>
                  <p class="help mt-1">Klik “Tambah Variasi” untuk menambah opsi. Harga boleh kosong.</p>
                </div>
                <button type="button" id="btnAddVariation" class="btn-soft px-3 py-2 text-xs">+ Tambah Variasi</button>
              </div>

              <div id="variationRows" class="mt-3 space-y-2">
                @if (count($varOptions))
                  @foreach ($varOptions as $i => $opt)
                    <div class="variation-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-center rounded-2xl border border-slate-200 bg-white p-3">
                      <div class="sm:col-span-7">
                        <label class="text-xs font-semibold text-slate-600">Nama Variasi</label>
                        <input type="text"
                               name="variation[options][{{ $i }}][label]"
                               value="{{ $opt['label'] }}"
                               class="input mt-2 var-label"
                               placeholder="Contoh: Paket Hemat">
                      </div>

                      <div class="sm:col-span-4">
                        <label class="text-xs font-semibold text-slate-600">Harga (opsional)</label>
                        <input type="text"
                               inputmode="numeric"
                               name="variation[options][{{ $i }}][price]"
                               value="{{ $opt['price'] }}"
                               class="input mt-2 var-price"
                               placeholder="Contoh: 25000">
                        <p class="mt-1 text-[11px] text-slate-500">Boleh kosong. Boleh pakai “25.000” / “Rp 25.000”.</p>
                      </div>

                      <div class="sm:col-span-1 flex sm:justify-end">
                        <button type="button"
                                class="btn-outline px-3 py-2 text-xs border-rose-200 text-rose-700 hover:bg-rose-50 btnRemoveVariation">
                          Hapus
                        </button>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="variation-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-center rounded-2xl border border-slate-200 bg-white p-3">
                    <div class="sm:col-span-7">
                      <label class="text-xs font-semibold text-slate-600">Nama Variasi</label>
                      <input type="text"
                             name="variation[options][0][label]"
                             class="input mt-2 var-label"
                             placeholder="Contoh: Paket Hemat">
                    </div>

                    <div class="sm:col-span-4">
                      <label class="text-xs font-semibold text-slate-600">Harga (opsional)</label>
                      <input type="text"
                             inputmode="numeric"
                             name="variation[options][0][price]"
                             class="input mt-2 var-price"
                             placeholder="Contoh: 25000">
                      <p class="mt-1 text-[11px] text-slate-500">Boleh kosong. Boleh pakai “25.000” / “Rp 25.000”.</p>
                    </div>

                    <div class="sm:col-span-1 flex sm:justify-end">
                      <button type="button"
                              class="btn-outline px-3 py-2 text-xs border-rose-200 text-rose-700 hover:bg-rose-50 btnRemoveVariation">
                        Hapus
                      </button>
                    </div>
                  </div>
                @endif
              </div>

              @error('variation.options')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            <template id="variationRowTpl">
              <div class="variation-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-center rounded-2xl border border-slate-200 bg-white p-3">
                <div class="sm:col-span-7">
                  <label class="text-xs font-semibold text-slate-600">Nama Variasi</label>
                  <input type="text" class="input mt-2 var-label" placeholder="Contoh: Paket Hemat">
                </div>

                <div class="sm:col-span-4">
                  <label class="text-xs font-semibold text-slate-600">Harga (opsional)</label>
                  <input type="text" inputmode="numeric" class="input mt-2 var-price" placeholder="Contoh: 25000">
                  <p class="mt-1 text-[11px] text-slate-500">Boleh kosong. Boleh pakai “25.000” / “Rp 25.000”.</p>
                </div>

                <div class="sm:col-span-1 flex sm:justify-end">
                  <button type="button"
                          class="btn-outline px-3 py-2 text-xs border-rose-200 text-rose-700 hover:bg-rose-50 btnRemoveVariation">
                    Hapus
                  </button>
                </div>
              </div>
            </template>

            <script>
              (function(){
                const wrap = document.getElementById('variationRows');
                const btnAdd = document.getElementById('btnAddVariation');
                const tpl = document.getElementById('variationRowTpl');

                if (!wrap || !btnAdd || !tpl) return;

                function reindex(){
                  const rows = wrap.querySelectorAll('.variation-row');
                  rows.forEach((row, idx) => {
                    const labelInput = row.querySelector('.var-label');
                    const priceInput = row.querySelector('.var-price');
                    if (labelInput) labelInput.name = `variation[options][${idx}][label]`;
                    if (priceInput) priceInput.name = `variation[options][${idx}][price]`;
                  });
                }

                function addRow(){
                  const node = tpl.content.cloneNode(true);
                  wrap.appendChild(node);
                  reindex();
                }

                function removeRow(btn){
                  const row = btn.closest('.variation-row');
                  if (!row) return;

                  const rows = wrap.querySelectorAll('.variation-row');
                  if (rows.length <= 1) {
                    const labelInput = row.querySelector('.var-label');
                    const priceInput = row.querySelector('.var-price');
                    if (labelInput) labelInput.value = '';
                    if (priceInput) priceInput.value = '';
                    return;
                  }

                  row.remove();
                  reindex();
                }

                wrap.addEventListener('click', function(e){
                  const btn = e.target.closest('.btnRemoveVariation');
                  if (btn) removeRow(btn);
                });

                btnAdd.addEventListener('click', addRow);

                reindex();
              })();
            </script>

          </div>
        </div>

        {{-- ================== TOMBOL SUBMIT ================== --}}
        <div class="card-solid p-6">
          <h2 class="text-sm font-semibold text-slate-900">Tombol Submit</h2>
          <p class="mt-1 text-xs text-slate-500">
            Atur label, warna, dan bentuk tombol.
          </p>

          @php
            $button   = $settings['button'] ?? [];
            $btnLabel = old('button.label', $button['label'] ?? 'KIRIM');
            $btnColor = old('button.color', $button['color'] ?? 'blue');
            $btnShape = old('button.shape', $button['shape'] ?? 'pill');

            $btnColors = [
              'blue'   => ['label' => 'Blue',   'color' => '#2563eb'],
              'green'  => ['label' => 'Green',  'color' => '#16a34a'],
              'orange' => ['label' => 'Orange', 'color' => '#f97316'],
              'red'    => ['label' => 'Red',    'color' => '#dc2626'],
            ];
          @endphp

          <div class="mt-5 space-y-4">
            <div>
              <label class="label">Teks tombol</label>
              <input type="text" name="button[label]" value="{{ $btnLabel }}" class="input mt-2">
              @error('button.label')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <p class="text-xs font-semibold text-slate-700 mb-2">Warna tombol</p>
              <div class="flex flex-wrap items-center gap-3">
                @foreach ($btnColors as $key => $cfg)
                  <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 cursor-pointer hover:bg-slate-50">
                    <input
                      type="radio"
                      name="button[color]"
                      value="{{ $key }}"
                      class="h-4 w-4 border-slate-300 text-slate-900 focus:ring-slate-900/20"
                      {{ $btnColor === $key ? 'checked' : '' }}
                    >
                    <span class="h-6 w-6 rounded-lg border border-slate-200 inline-block" style="background: {{ $cfg['color'] }}"></span>
                    <span class="text-xs font-semibold text-slate-700">{{ $cfg['label'] }}</span>
                  </label>
                @endforeach
              </div>
              @error('button.color')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <p class="text-xs font-semibold text-slate-700 mb-2">Bentuk tombol</p>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                @foreach (['square' => 'Kotak', 'rounded' => 'Rounded', 'pill' => 'Pill'] as $k => $ttl)
                  <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 cursor-pointer hover:bg-slate-50">
                    <input
                      type="radio"
                      name="button[shape]"
                      value="{{ $k }}"
                      class="h-4 w-4 border-slate-300 text-slate-900 focus:ring-slate-900/20"
                      {{ $btnShape === $k ? 'checked' : '' }}
                    >
                    <span class="text-sm font-semibold text-slate-800">{{ $ttl }}</span>
                  </label>
                @endforeach
              </div>
              @error('button.shape')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        {{-- ================== TRACKING PIXEL ================== --}}
        <div class="card-solid p-6 md:col-span-2">
          <h2 class="text-sm font-semibold text-slate-900">Tracking (Facebook Pixel, GTM, TikTok)</h2>
          <p class="mt-1 text-xs text-slate-500">
            Masukkan ID dan event dipisah koma. Akan disimpan rapi ke JSON settings.
          </p>

          @php
            $tracking = $settings['tracking'] ?? [];
            $fbIdsText   = old('tracking.facebook_pixel_ids_text', implode(', ', $tracking['facebook_pixel_ids'] ?? []));
            $fbEventsTxt = old('tracking.facebook_events_text', implode(', ', $tracking['facebook_events'] ?? []));
            $gtmIdsText  = old('tracking.gtm_ids_text', implode(', ', $tracking['gtm_ids'] ?? []));
            $ttIdsText   = old('tracking.tiktok_pixel_ids_text', implode(', ', $tracking['tiktok_pixel_ids'] ?? []));
          @endphp

          <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="label">Facebook Pixel IDs</label>
              <input type="text" name="tracking[facebook_pixel_ids_text]" value="{{ $fbIdsText }}" class="input mt-2" placeholder="ID1, ID2, ...">
              <p class="help mt-1">Contoh: 1234567890, 9876543210</p>
            </div>

            <div>
              <label class="label">Facebook Events</label>
              <input type="text" name="tracking[facebook_events_text]" value="{{ $fbEventsTxt }}" class="input mt-2" placeholder="Lead, Purchase, CompleteRegistration">
              <p class="help mt-1">Pisahkan dengan koma.</p>
            </div>

            <div>
              <label class="label">GTM IDs</label>
              <input type="text" name="tracking[gtm_ids_text]" value="{{ $gtmIdsText }}" class="input mt-2" placeholder="GTM-XXXXXX, GTM-YYYYYY">
            </div>

            <div>
              <label class="label">TikTok Pixel IDs</label>
              <input type="text" name="tracking[tiktok_pixel_ids_text]" value="{{ $ttIdsText }}" class="input mt-2" placeholder="ID1, ID2, ...">
            </div>
          </div>
        </div>

      </div>

      <div class="flex items-center justify-end gap-2">
        <a href="{{ route('app.forms.index') }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn-primary">Simpan Pengaturan</button>
      </div>
    </form>
  </div>
@endsection
