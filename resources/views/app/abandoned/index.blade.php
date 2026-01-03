{{-- resources/views/app/abandoned/index.blade.php --}}
@extends('layouts.app')

@section('header')
<div class="card p-4 mb-4">
  <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6 lg:items-end">
    <div class="lg:col-span-2">
      <label class="label">Produk</label>
      <select name="form_id" class="input">
        <option value="">Semua</option>
        @foreach ($forms as $f)
          <option value="{{ $f->id }}" @selected((string)request('form_id')===(string)$f->id)>
            {{ $f->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="label">Dari Tanggal</label>
      <input type="date" name="date_from" class="input" value="{{ request('date_from') }}">
    </div>

    <div>
      <label class="label">Sampai</label>
      <input type="date" name="date_to" class="input" value="{{ request('date_to') }}">
    </div>

    <div>
      <button class="btn-primary w-full">Terapkan</button>
    </div>

    <div>
      <a href="{{ route('app.abandoned.index') }}" class="btn-outline w-full text-center block">Reset</a>
    </div>
  </form>
</div>

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
           @php
  // templates default + override dari DB ($abandonedTemplates dari controller)
  $defaultAbandonedTemplates = [
    'fu1' => "Halo kak {name} 😊\n\nKami lihat kakak sempat mengisi form tapi belum selesai checkout.\nKalau kakak masih ingin lanjut, bisa dibantu ya kak?",
    'fu2' => "Halo kak {name} 😊\n\nMau aku bantu lanjut checkout kak? Balas 'LANJUT' ya kak.",
    'fu3' => "Halo kak {name} 😊\n\nReminder ya kak, kalau masih mau lanjut checkout kabarin aku ya 😊",
    'fu4' => "Halo kak {name} 😊\n\nFollow up terakhir ya kak. Kalau masih ingin lanjut, aku siap bantu 😊",
  ];
  $mergedAbandonedTemplates = array_merge($defaultAbandonedTemplates, $abandonedTemplates ?? []);

  // icon + base style tombol (JANGAN pake string yang ada "..." karena itu rusak)
  $chatSvg = '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
    <path d="M20 2H4a2 2 0 0 0-2 2v14l4-3h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z"/>
  </svg>';

  $btnBase = 'relative inline-flex h-8 w-8 items-center justify-center rounded-xl border border-slate-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-400/40';
  $labelBase = 'absolute -top-1 -right-1 grid h-4 w-4 place-items-center rounded-full text-[10px] font-bold';
@endphp


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
               $lastKey = (string)($s->last_followup_key ?? '');

$rowData = [
  'id' => (string)$s->id,
  'name' => (string)$name,
  'phone' => (string)$phone,
  'followup_store_url' => route('app.abandoned.followup', $s),
  'templates' => $mergedAbandonedTemplates,
  'state' => [
    'fu1' => !empty($s->followup1_sent_at),
    'fu2' => !empty($s->followup2_sent_at),
    'fu3' => !empty($s->followup3_sent_at),
    'fu4' => !empty($s->followup4_sent_at),
  ],
];

// warna tombol: kalau key terakhir sama → ijo
$fuColors = [
  'fu1' => $rowData['state']['fu1'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600',
  'fu2' => $rowData['state']['fu2'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600',
  'fu3' => $rowData['state']['fu3'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600',
  'fu4' => $rowData['state']['fu4'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600',
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
                 <div class="flex items-center gap-1">
  <button type="button"
    class="{{ $btnBase }} {{ $fuColors['fu1'] }}"
    onclick='openAbandonedFU(@json($rowData),"fu1")'
    title="Follow Up 1">
    {!! $chatSvg !!}
    <span class="{{ $labelBase }} bg-white/90 text-slate-800">1</span>
  </button>

  <button type="button"
    class="{{ $btnBase }} {{ $fuColors['fu2'] }}"
    onclick='openAbandonedFU(@json($rowData),"fu2")'
    title="Follow Up 2">
    {!! $chatSvg !!}
    <span class="{{ $labelBase }} bg-white/90 text-slate-800">2</span>
  </button>

  <button type="button"
    class="{{ $btnBase }} {{ $fuColors['fu3'] }}"
    onclick='openAbandonedFU(@json($rowData),"fu3")'
    title="Follow Up 3">
    {!! $chatSvg !!}
    <span class="{{ $labelBase }} bg-white/90 text-slate-800">3</span>
  </button>

  <button type="button"
    class="{{ $btnBase }} {{ $fuColors['fu4'] }}"
    onclick='openAbandonedFU(@json($rowData),"fu4")'
    title="Follow Up 4">
    {!! $chatSvg !!}
    <span class="{{ $labelBase }} bg-white/90 text-slate-800">4</span>
  </button>
</div>



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

    {{-- Header + tombol X --}}
    <div class="flex items-start justify-between gap-3">
      <div>
        <div class="font-semibold" id="abTitle">Follow Up</div>
        <div class="text-xs text-slate-500" id="abSub">-</div>
      </div>

      <button type="button"
        onclick="closeAbandonedFU()"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50"
        aria-label="Tutup">
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div>
      <label class="label">No HP / WhatsApp</label>
      <input id="abPhone" class="input" placeholder="628xxx">
    </div>

    <div>
      <label class="label">Teks</label>
      <textarea id="abText" class="input min-h-[140px]"></textarea>
    </div>

    <div class="flex justify-end gap-2">
      <button type="button" onclick="closeAbandonedFU()" class="btn-outline">Batal</button>
      <button type="button" onclick="saveAbandonedTpl()" class="btn-outline">Save Template</button>
      <button type="button" onclick="sendAbandonedFU()" class="btn-primary">Follow Up</button>
    </div>

  </div>
</div>


  <script>
  // ===== GLOBAL TEMPLATE CACHE (persist walau modal ditutup) =====
  const UG_TPL_KEY = 'ug_tpl_cache_v1';
  const UG_TPL = (() => {
    try { return JSON.parse(localStorage.getItem(UG_TPL_KEY) || '{}'); }
    catch(e){ return {}; }
  })();

  function ugKey(ctx, key){ return `${ctx}::${key}`; }
  function ugGet(ctx, key, fallback){
    const k = ugKey(ctx, key);
    return (UG_TPL[k] !== undefined) ? UG_TPL[k] : fallback;
  }
  function ugSet(ctx, key, val){
    const k = ugKey(ctx, key);
    UG_TPL[k] = String(val ?? '');
    localStorage.setItem(UG_TPL_KEY, JSON.stringify(UG_TPL));
  }

  let AB_CTX = null;


function renderAbandonedTpl(tpl, ctx) {
  return String(tpl || '')
    .replaceAll('{name}', ctx.name || '-')
    .replaceAll('{phone}', ctx.phone || '-');
}

function openAbandonedFU(ctx, key) {
  AB_CTX = { ctx, key };
  document.getElementById('abTitle').innerText = `Follow Up ${String(key||'').toUpperCase()}`;
  document.getElementById('abSub').innerText = `Abandoned #${ctx.id}`;
  document.getElementById('abPhone').value = ctx.phone || '';

  const fallback = (ctx.templates || {})[key] || '';
  document.getElementById('abText').value = ugGet('abandoned', key, fallback);

  document.getElementById('abFU').classList.remove('hidden');
}


function closeAbandonedFU() {
  document.getElementById('abFU').classList.add('hidden');
  AB_CTX = null;
}

async function saveAbandonedTpl(){
  if(!AB_CTX) return;

  const template = document.getElementById('abText').value || '';
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  const res = await fetch('{{ route('app.message_templates.save') }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      context: 'abandoned',
      key: AB_CTX.key,
      template: template,
    }),
  });

  if (!res.ok) {
    ugSet('abandoned', AB_CTX.key, template);

    const t = await res.text();
    alert('Gagal save template: ' + t);
    return;
  }
  ugSet('abandoned', AB_CTX.key, template);
  // ✅ update cache template di browser
  AB_CTX.ctx.templates = AB_CTX.ctx.templates || {};
  AB_CTX.ctx.templates[AB_CTX.key] = template;

  alert('Template tersimpan');
}


async function sendAbandonedFU() {
  if (!AB_CTX) return;

  const { ctx, key } = AB_CTX;

  const phone = String(document.getElementById('abPhone').value || '').replace(/[^0-9]/g,'');
  const tpl = document.getElementById('abText').value || '';
  const msg = renderAbandonedTpl(tpl, ctx);

  if (!phone) { alert('Nomor WhatsApp belum ada. Isi dulu.'); return; }

  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const res = await fetch(ctx.followup_store_url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      key: key,
      phone: phone,
      message: msg,
    }),
  });

  if (!res.ok) {
    const t = await res.text();
    alert('Gagal simpan follow up: ' + t);
    return;
  }

  window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
  location.reload();
}

  </script>
@endsection
