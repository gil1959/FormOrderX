{{-- resources/views/admin/forms/create.blade.php --}}
@extends('layouts.app')

@section('header')
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold tracking-tight text-slate-900">Buat Form Baru</h1>
      <p class="mt-1 text-sm text-slate-600">Siapkan campaign baru, nanti lanjut atur field dan tampilan.</p>
    </div>

    <a href="{{ route('app.forms.index') }}" class="btn-outline">
      Kembali
    </a>
  </div>
@endsection

@section('content')
  <div class="max-w-3xl">
    <div class="card-solid p-6">
      <form method="POST" action="{{ route('app.forms.store') }}" class="space-y-5">
        @csrf

        <div>
          <label class="label" for="name">Nama Form / Campaign</label>
          <p class="help mt-1">Contoh: Hijab Bergo Paket 10</p>
          <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name') }}"
            required
            class="input mt-2"
            placeholder="Contoh: Hijab Bergo Paket 10"
          >
          @error('name')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="label" for="description">Deskripsi</label>
          <p class="help mt-1">Opsional. Buat ringkas, fokus benefit.</p>
          <textarea
            id="description"
            name="description"
            rows="4"
            class="input mt-2"
            placeholder="Deskripsi singkat campaign..."
          >{{ old('description') }}</textarea>
          @error('description')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            

          <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <input
              id="is_active"
              type="checkbox"
              name="is_active"
              value="1"
              class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20"
              {{ old('is_active', true) ? 'checked' : '' }}
            >
            <div>
              <label for="is_active" class="text-sm font-semibold text-slate-800">
                Form aktif
              </label>
              <p class="mt-1 text-xs text-slate-500">
                Jika aktif, form bisa menerima order/submission.
              </p>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
          <a href="{{ route('app.forms.index') }}" class="btn-outline">
            Batal
          </a>
          <button type="submit" class="btn-primary">
            Simpan Form
          </button>
        </div>
      </form>
    </div>

    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5">
      <p class="text-sm font-semibold text-slate-900">Langkah berikutnya</p>
      <ul class="mt-2 list-disc pl-5 text-sm text-slate-600 space-y-1">
        <li>Kelola Field (input apa aja yang muncul)</li>
        <li>Pengaturan tampilan (layout, background, button, variasi, dsb)</li>
        <li>Copy embed script ke landing page</li>
      </ul>
    </div>
  </div>
@endsection
