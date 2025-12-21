{{-- resources/views/app/abandoned/index.blade.php --}}
@extends('layouts.app')

@section('header')
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold tracking-tight text-slate-900">Abandoned Cart</h1>
      <p class="mt-1 text-sm text-slate-600">
        Data pengisian form yang belum checkout/submit (autosave).
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('app.orders.index') }}" class="btn-outline">Lihat Order</a>
    </div>
  </div>
@endsection

@section('content')
  <div class="space-y-4">
    <div class="card overflow-hidden">
      <div class="border-b border-slate-200/70 px-4 py-3">
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-semibold text-slate-900">Daftar Abandoned</h2>
          <div class="text-xs text-slate-500">
            Total: {{ $sessions->total() }}
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-white/60">
            <tr class="text-left text-slate-600">
              <th class="px-4 py-3 font-semibold">Last Activity</th>
              <th class="px-4 py-3 font-semibold">Form</th>
              <th class="px-4 py-3 font-semibold">Session</th>
              <th class="px-4 py-3 font-semibold">Preview Data</th>
              <th class="px-4 py-3 font-semibold">Aksi</th>

            </tr>
          </thead>

          <tbody class="divide-y divide-slate-200/70">
            @forelse($sessions as $s)
              @php
                $payload = $s->data ?? [];
                $fields = $payload['fields'] ?? [];
              @endphp

              <tr class="align-top hover:bg-slate-50/60">
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="font-semibold text-slate-900">
                    {{ $s->last_activity_at?->format('Y-m-d') ?? '-' }}
                  </div>
                  <div class="text-xs text-slate-500">
                    {{ $s->last_activity_at?->format('H:i') ?? '-' }}
                  </div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="font-semibold text-slate-900">{{ $s->form?->name ?? '-' }}</div>
                  <div class="text-xs text-slate-500">#{{ $s->id }}</div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <code class="rounded-xl border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700">
                    {{ $s->session_key }}
                  </code>
                </td>

                <td class="px-4 py-3">
                  @if (is_array($fields) && count($fields))
                    <div class="grid grid-cols-1 gap-1">
                      @foreach(array_slice($fields, 0, 10, true) as $k => $v)
                        <div class="text-slate-700">
                          <span class="font-semibold text-slate-900">{{ $k }}:</span>
                          <span>
                            @if (is_scalar($v))
                              {{ $v }}
                            @else
                              {{ json_encode($v) }}
                            @endif
                          </span>
                        </div>
                      @endforeach

                      @if (count($fields) > 10)
                        <div class="text-slate-500">…</div>
                      @endif
                    </div>
                  @else
                    <div class="text-slate-500">Data kosong (belum ada input yang tersimpan).</div>
                  @endif
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
  <form method="POST"
        action="{{ route('app.abandoned.destroy', $s) }}"
        onsubmit="return confirm('Yakin hapus abandoned #{{ $s->id }}?')">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn-outline border-rose-200 text-rose-700 hover:bg-rose-50">
      Hapus
    </button>
  </form>
</td>

              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-4 py-10">
                  <div class="text-center">
                    <div class="text-sm font-semibold text-slate-900">Belum ada abandoned cart.</div>
                    <div class="mt-1 text-sm text-slate-600">
                      Isi form di embed tanpa submit dulu (tunggu ~1–2 detik) biar autosave masuk.
                    </div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="border-t border-slate-200/70 px-4 py-3">
        {{ $sessions->links() }}
      </div>
    </div>
  </div>
@endsection
