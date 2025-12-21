@extends('layouts.app')

@section('header')
  <div class="flex flex-col gap-2">
    <h1 class="text-xl font-semibold tracking-tight text-slate-900">Settings</h1>
    <p class="text-sm text-slate-600">Pengaturan global dashboard (WhatsApp, dan nanti fitur lainnya).</p>
  </div>
@endsection

@section('content')
  <div class="space-y-4">

    @if (session('success'))
      <div class="card p-4 border border-emerald-200 bg-emerald-50 text-emerald-800">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('app.settings.update') }}" class="space-y-4">
      @csrf

      <div class="card p-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-sm font-semibold text-slate-900">WhatsApp Redirect</h2>
            <p class="mt-1 text-xs text-slate-500">
              Setelah order sukses, user diarahkan ke WhatsApp admin dengan pesan otomatis.
            </p>
          </div>
        </div>

        @php
          $waEnabled = old('whatsapp.enabled', (bool)($whatsapp['enabled'] ?? true));
          $waPhone = old('whatsapp.phone', $whatsapp['phone'] ?? '');
          $waTpl = old('whatsapp.message_template', $whatsapp['message_template'] ?? '');
        @endphp

        <div class="mt-5 space-y-4">
          <label class="flex items-center gap-2">
            <input type="checkbox" name="whatsapp[enabled]" value="1" class="h-4 w-4" {{ $waEnabled ? 'checked' : '' }}>
            <span class="text-sm font-semibold text-slate-700">Aktifkan redirect WhatsApp</span>
          </label>

          <div>
            <label class="label">Nomor WhatsApp Admin</label>
            <input type="text" name="whatsapp[phone]" value="{{ $waPhone }}" class="input"
                   placeholder="Contoh: 6281234567890 (tanpa +, tanpa spasi)">
            <p class="mt-1 text-xs text-slate-500">Format internasional. Sistem akan buang karakter non-angka.</p>
            @error('whatsapp.phone')
              <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="label">Template Pesan</label>
            <textarea name="whatsapp[message_template]" class="input" rows="8"
              placeholder="Gunakan placeholder: {form_name} {summary} {total} {source_url}">{{ $waTpl }}</textarea>
            <p class="mt-1 text-xs text-slate-500">
              Placeholder: <code>{form_name}</code>, <code>{summary}</code>, <code>{total}</code>, <code>{source_url}</code>
            </p>
            @error('whatsapp.message_template')
              <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          <div class="pt-2">
            <button class="btn-primary">Simpan Settings</button>
          </div>
        </div>
      </div>
    </form>
  </div>
@endsection
