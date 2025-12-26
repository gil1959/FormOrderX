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

                // ambil nama & hp/wa dari field
                $findField = function(array $fields, array $keys) {
                  foreach ($fields as $k => $v) {
                    $k2 = strtolower((string)$k);
                    foreach ($keys as $key) {
                      if (str_contains($k2, strtolower($key))) {
                        if (is_scalar($v) && trim((string)$v) !== '') return trim((string)$v);
                      }
                    }
                  }
                  return null;
                };

                $name = $findField($fields, ['nama','name','fullname','full name']) ?? '-';
                $phoneRaw = $findField($fields, ['whatsapp','wa','hp','no hp','tel','telepon','phone']);
                $phone = preg_replace('/[^0-9]/', '', (string)$phoneRaw);
                if ($phone && str_starts_with($phone, '0')) $phone = '62'.substr($phone, 1);

                // template 1x follow up abandoned
                $tplAbandoned = "Halo kak {name} 😊\n\nKami lihat kakak sempat mengisi form tapi belum selesai checkout.\nKalau kakak masih ingin lanjut, bisa dibantu ya kak?";

                $rowData = [
                  'id' => (string)$s->id,
                  'name' => (string)$name,
                  'phone' => (string)$phone,
                  'followup_store_url' => route('app.abandoned.followup', $s),
                  'templates' => [
                    'abandoned' => $tplAbandoned,
                  ],
                  // butuh kolom followup_sent_at (migration)
                  'sent' => !is_null($s->followup_sent_at ?? null),
                ];
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

                <td class="px-4 py-3 whitespace-nowrap space-y-2">
                  {{-- FOLLOW UP (DITAMBAH, DELETE TETAP ADA) --}}
                  <button
                    type="button"
                    class="btn-outline w-[120px] {{ $rowData['sent'] ? 'border-emerald-200 text-emerald-800 hover:bg-emerald-50' : '' }}"
                    onclick='openAbandonedFU(@json($rowData))'>
                    {{ $rowData['sent'] ? 'Follow Up ✓' : 'Follow Up' }}
                  </button>

                  <form method="POST"
                        action="{{ route('app.abandoned.destroy', $s) }}"
                        onsubmit="return confirm('Yakin hapus abandoned #{{ $s->id }}?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn-outline w-[120px] border-rose-200 text-rose-700 hover:bg-rose-50">
                      Hapus
                    </button>
                  </form>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-10">
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

  {{-- MODAL FOLLOW UP --}}
  <div id="abFU" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-lg p-4 space-y-3">
      <div class="font-semibold" id="abTitle">Follow Up</div>
      <div class="text-xs text-slate-500" id="abSub">-</div>

      <div>
        <label class="label">No HP / WhatsApp</label>
        <input id="abPhone" class="input" placeholder="628xxx">
      </div>

      <div>
        <label class="label">Teks</label>
        <textarea id="abText" class="input min-h-[140px]"></textarea>
      </div>

      <div class="flex justify-end gap-2">
        <button onclick="closeAbandonedFU()" class="btn-outline">Batal</button>
        <button onclick="sendAbandonedFU()" class="btn-primary">Follow Up</button>
      </div>
    </div>
  </div>

  <script>
    let AB_CTX = null;

    function fillTpl(tpl, ctx) {
      return String(tpl || '')
        .replaceAll('{name}', ctx.name || '-')
        .replaceAll('{phone}', ctx.phone || '-');
    }

    function openAbandonedFU(ctx) {
      AB_CTX = ctx;
      document.getElementById('abTitle').innerText = 'Follow Up';
      document.getElementById('abSub').innerText = `Abandoned #${ctx.id}`;
      document.getElementById('abPhone').value = ctx.phone || '';
      document.getElementById('abText').value = fillTpl((ctx.templates || {}).abandoned, ctx);
      document.getElementById('abFU').classList.remove('hidden');
    }

    function closeAbandonedFU() {
      document.getElementById('abFU').classList.add('hidden');
      AB_CTX = null;
    }

    async function sendAbandonedFU() {
      const ctx = AB_CTX;
      if (!ctx) return;

      const phone = String(document.getElementById('abPhone').value || '').replace(/[^0-9]/g,'');
      const msg = document.getElementById('abText').value || '';

      if (!phone) { alert('Nomor WhatsApp belum ada. Isi dulu.'); return; }

      // SAVE dulu
      const csrf = document.querySelector('meta[name="csrf-token"]').content;
      const res = await fetch(ctx.followup_store_url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          key: 'abandoned',
          phone: phone,
          message: msg,
        }),
      });

      if (!res.ok) {
        const t = await res.text();
        alert('Gagal simpan follow up: ' + t);
        return;
      }

      // WA redirect
      window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
      location.reload();
    }
  </script>
@endsection
