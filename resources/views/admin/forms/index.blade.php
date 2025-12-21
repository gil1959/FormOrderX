{{-- resources/views/admin/forms/index.blade.php --}}
@extends('layouts.app')

@section('header')
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold tracking-tight text-slate-900">Form Saya</h1>
      <p class="mt-1 text-sm text-slate-600">
        Buat, kelola, dan ambil embed script untuk dipasang di landing page.
      </p>
    </div>

    <a href="{{ route('app.forms.create') }}" class="btn-primary">
      Buat Form Baru
    </a>
  </div>
@endsection

@section('content')
  <div class="space-y-4">
    @if ($forms->isEmpty())
      <div class="card-solid p-6">
        <p class="text-slate-700">
          Belum ada form. Klik <span class="font-semibold">“Buat Form Baru”</span> untuk bikin campaign pertama.
        </p>
      </div>
    @else
      <div class="card-solid overflow-hidden">
        <div class="border-b border-slate-200 p-5">
          <div class="flex items-center justify-between gap-3">
            <div>
              <p class="text-sm font-semibold text-slate-900">Daftar Form</p>
              <p class="mt-1 text-xs text-slate-500">
                Tip: embed script ada di kolom “Embed”.
              </p>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
              <tr class="border-b border-slate-200">
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600">Nama</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600">Slug</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600">Embed</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600">Dibuat</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-slate-600">Aksi</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              @foreach ($forms as $form)
                <tr class="hover:bg-slate-50/60 transition">
                  <td class="px-5 py-4 align-top">
                    <div class="font-semibold text-slate-900">{{ $form->name }}</div>
                    @if ($form->description)
                      <div class="mt-1 text-xs text-slate-500 line-clamp-2">
                        {{ $form->description }}
                      </div>
                    @endif
                  </td>

                  <td class="px-5 py-4 align-top">
                    <span class="text-xs font-mono text-slate-600">{{ $form->slug }}</span>
                  </td>

                  <td class="px-5 py-4 align-top">
                    @if ($form->is_active)
                      <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Nonaktif
                      </span>
                    @endif
                  </td>

                  <td class="px-5 py-4 align-top">
                    <div class="rounded-xl border border-slate-200 bg-white p-3">
                      @php
                        $embedCode = '<script src="' . url('/embed/'.$form->embed_token.'.js') . '"></script>';
                      @endphp

                      <div class="text-[11px] font-mono text-slate-700 break-all" id="embed_code_{{ $form->id }}">
                        {{ $embedCode }}
                      </div>

                      <div class="mt-3 flex flex-wrap items-center gap-2">
                        <button
                          type="button"
                          class="btn-soft px-3 py-1.5 text-xs"
                          data-copy-embed
                          data-copy-text="{{ $embedCode }}"
                        >
                          Copy Embed
                        </button>

                        <span class="text-[11px] text-emerald-700" data-copy-status style="display:none;">Tersalin ✓</span>
                      </div>
                      <p class="mt-2 text-[11px] text-slate-500">
                        Tempelkan di landing page (Berdu, WordPress, dll).
                      </p>
                    </div>
                  </td>

                  <td class="px-5 py-4 align-top">
                    <span class="text-xs text-slate-600">
                      {{ $form->created_at->format('d M Y H:i') }}
                    </span>
                  </td>

                  <td class="px-5 py-4 align-top text-right">
                    <div class="inline-flex flex-wrap items-center justify-end gap-2">
                      <a href="{{ route('app.forms.design', $form) }}" class="btn-outline px-3 py-1.5 text-xs">
                        Pengaturan
                      </a>
                      <a href="{{ route('app.forms.fields.edit', $form) }}" class="btn-outline px-3 py-1.5 text-xs">
                        Kelola Field
                      </a>
                      <a href="{{ route('app.forms.preview', $form) }}" class="btn-soft px-3 py-1.5 text-xs">
                        Preview
                      </a>

                      <form
                        method="POST"
                        action="{{ route('app.forms.destroy', $form) }}"
                        onsubmit="return confirm('Yakin mau hapus form ini? Semua field & data order akan ikut terhapus.')"
                      >
                        @csrf
                        @method('DELETE')
                        <button
                          type="submit"
                          class="btn-outline px-3 py-1.5 text-xs border-rose-200 text-rose-700 hover:bg-rose-50"
                        >
                          Hapus
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="border-t border-slate-200 p-4">
          {{ $forms->links() }}
        </div>
      </div>
    @endif
  </div>

  <script>
    (function () {
      function showStatus(btn) {
        var wrap = btn.parentElement;
        if (!wrap) return;
        var st = wrap.querySelector('[data-copy-status]');
        if (!st) return;
        st.style.display = 'inline';
        clearTimeout(st._t);
        st._t = setTimeout(function(){ st.style.display = 'none'; }, 1400);
      }

      function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta);
      }

      document.addEventListener('click', async function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('[data-copy-embed]') : null;
        if (!btn) return;
        var text = btn.getAttribute('data-copy-text') || '';
        if (!text) return;

        try {
          if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
          } else {
            fallbackCopy(text);
          }
          showStatus(btn);
        } catch (err) {
          fallbackCopy(text);
          showStatus(btn);
        }
      });
    })();
  </script>
@endsection
