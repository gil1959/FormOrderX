{{-- resources/views/admin/forms/fields.blade.php --}}
@extends('layouts.app')

@section('header')
  <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold tracking-tight text-slate-900">Kelola Field</h1>
      <p class="mt-1 text-sm text-slate-600">
        Form: <span class="font-semibold">{{ $form->name }}</span>
      </p>
    </div>

    <a href="{{ route('app.forms.index') }}" class="btn-outline">&larr; Kembali</a>
  </div>
@endsection

@section('content')
  <div class="space-y-4 max-w-5xl">

    @if (session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        {{ session('success') }}
      </div>
    @endif

    <div class="card-solid p-6">
      <div class="flex items-start justify-between gap-3">
        <div>
          <h2 class="text-sm font-semibold text-slate-900">Field aktif</h2>
          <p class="mt-1 text-xs text-slate-500">Field yang tampil di form embed.</p>
        </div>
        <a href="{{ route('app.forms.preview', $form) }}" class="btn-soft px-3 py-1.5 text-xs">Preview</a>
      </div>

      <div class="mt-4">
        @if ($fields->isEmpty())
          <p class="text-sm text-slate-600">Belum ada field. Tambahin di panel bawah.</p>
        @else
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50">
                <tr class="border-b border-slate-200">
                  <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Label</th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Kode Field</th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tipe</th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Wajib</th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Ringkasan</th>
                  <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @foreach ($fields as $field)
                  <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-4 py-3">{{ $field->label }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-slate-600">{{ $field->name }}</td>
                    <td class="px-4 py-3 text-xs text-slate-700">{{ $fieldTypeLabels[$field->type] ?? $field->type }}</td>
                    <td class="px-4 py-3 text-xs text-slate-700">{{ $field->required ? 'Ya' : 'Tidak' }}</td>
                    <td class="px-4 py-3 text-xs text-slate-700">{{ ($field->show_in_summary ?? true) ? 'Tampil' : 'Sembunyi' }}</td>
                    <td class="px-4 py-3 text-right">
                      <form action="{{ route('app.forms.fields.destroy', [$form, $field]) }}"
                            method="POST"
                            onsubmit="return confirm('Hapus field ini?')"
                            class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-outline px-3 py-1.5 text-xs border-rose-200 text-rose-700 hover:bg-rose-50">
                          Hapus
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

    <div class="card-solid p-6">
      <h2 class="text-sm font-semibold text-slate-900">Tambah field baru</h2>
      <p class="mt-1 text-xs text-slate-500">Bikin field sesuai kebutuhan campaign.</p>

      <form method="POST" action="{{ route('app.forms.fields.store', $form) }}" class="mt-4 space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="label">Label</label>
            <input id="field_label" type="text" name="label" value="{{ old('label') }}" required class="input mt-2" placeholder="Contoh: Nama Lengkap">
            @error('label') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="label">Kode Field (untuk sistem)</label>
            <input id="field_name" type="text" name="name" value="{{ old('name') }}" required class="input mt-2"
                   placeholder="otomatis dari label, contoh: nama_lengkap">
            <p class="mt-2 text-xs text-slate-500">
              Ini bukan terlihat oleh pembeli. Dipakai sistem untuk menyimpan data (harus unik, tanpa spasi).
            </p>
            @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="label">Tipe field</label>
            <select id="field_type" name="type" class="input mt-2" required>
              <option value="">Pilih tipe</option>
              @foreach (($fieldTypeLabels ?? []) as $type => $label)
                <option value="{{ $type }}" @selected(old('type') === $type)>{{ $label }}</option>
              @endforeach

              {{-- fallback kalau suatu saat $fieldTypeLabels tidak terisi --}}
              @if(empty($fieldTypeLabels))
                @foreach ($fieldTypes as $type)
                  <option value="{{ $type }}" @selected(old('type') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
              @endif
            </select>
            @error('type') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <div id="options_wrap" style="display:none;">
              <label class="label">Daftar pilihan (khusus tipe: Dropdown)</label>
              <input type="text" name="options" value="{{ old('options') }}" class="input mt-2" placeholder="Contoh: Paket A, Paket B, Paket C">
              <p class="mt-2 text-xs text-slate-500">Pisahkan dengan koma. Contoh: Merah, Biru, Hijau</p>
              @error('options') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <input id="required" type="checkbox" name="required" value="1"
                     class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20"
                     {{ old('required') ? 'checked' : '' }}>
              <div>
                <label for="required" class="text-sm font-semibold text-slate-800">Wajib diisi</label>
                <p class="mt-1 text-xs text-slate-500">User tidak bisa submit kalau kosong.</p>
              </div>
            </div>

            <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <input id="show_in_summary" type="checkbox" name="show_in_summary" value="1"
                     class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20"
                     {{ old('show_in_summary', true) ? 'checked' : '' }}>
              <div>
                <label for="show_in_summary" class="text-sm font-semibold text-slate-800">Tampilkan di ringkasan pemesanan</label>
                <p class="mt-1 text-xs text-slate-500">Field ini akan muncul di ringkasan otomatis di form embed.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="btn-primary">Tambah Field</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  (function(){
    const labelEl = document.getElementById('field_label');
    const nameEl  = document.getElementById('field_name');
    const typeEl  = document.getElementById('field_type');
    const optWrap = document.getElementById('options_wrap');

    function slugify(str){
      return (str || '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s_-]/g, '')
        .replace(/\s+/g, '_')
        .replace(/_+/g, '_');
    }

    function syncName(){
      if (!labelEl || !nameEl) return;
      if (nameEl.dataset.touched === '1') return;
      nameEl.value = slugify(labelEl.value);
    }

    if (nameEl) {
      nameEl.addEventListener('input', function(){
        nameEl.dataset.touched = '1';
      });
    }

    if (labelEl) {
      labelEl.addEventListener('input', syncName);
    }

    function toggleOptions(){
      if (!typeEl || !optWrap) return;
      optWrap.style.display = (typeEl.value === 'select') ? 'block' : 'none';
    }

    if (typeEl) {
      typeEl.addEventListener('change', toggleOptions);
      toggleOptions();
    }

    syncName();
  })();
  </script>
@endsection
