{{-- resources/views/app/orders/index.blade.php --}}
@extends('layouts.app')

@section('header')
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold tracking-tight text-slate-900">Order</h1>
      <p class="mt-1 text-sm text-slate-600">
        Daftar pesanan yang masuk dari form. Admin bisa ubah status order dan pembayaran.
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('app.forms.index') }}" class="btn-outline">Kelola Form</a>
    </div>
  </div>
@endsection

@section('content')
  <div class="space-y-4">
    {{-- Filter --}}
    <div class="card p-4">
      <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-8 lg:items-end">
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

  <div class="lg:col-span-2">
    <label class="label">Status Order</label>
    <select name="status" class="input">
      <option value="">Semua</option>
      @foreach (['pending' => 'Pending', 'processed' => 'Processed', 'completed' => 'Complete', 'cancelled' => 'Cancel'] as $k => $v)
        <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
      @endforeach
    </select>
  </div>

  <div class="lg:col-span-2">
    <label class="label">Status Pembayaran</label>
    <select name="payment_status" class="input">
      <option value="">Semua</option>
      @foreach (['unpaid' => 'Unpaid', 'paid' => 'Paid', 'refunded' => 'Refund'] as $k => $v)
        <option value="{{ $k }}" @selected(request('payment_status')===$k)>{{ $v }}</option>
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

  <div class="lg:col-span-1">
    <button class="btn-primary w-full">Terapkan</button>
  </div>

  <div class="lg:col-span-1">
    <a href="{{ route('app.orders.index') }}" class="btn-outline w-full text-center block">Reset</a>
  </div>
</form>

    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
      <div class="border-b border-slate-200/70 px-4 py-3">
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-semibold text-slate-900">List Order</h2>
          <div class="text-xs text-slate-500">
            Total: {{ $orders->total() }}
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-white/60">
            <tr class="text-left text-slate-600">
              <th class="px-4 py-3 font-semibold">Waktu</th>
              <th class="px-4 py-3 font-semibold">Form</th>
              <th class="px-4 py-3 font-semibold">Ringkasan</th>
              <th class="px-4 py-3 font-semibold">Total</th>
              <th class="px-4 py-3 font-semibold">Order</th>
              <th class="px-4 py-3 font-semibold">Payment</th>
              <th class="px-4 py-3 font-semibold">Follow-Up</th>
              <th class="px-4 py-3 font-semibold">Tindakan</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-200/70">
            @php
  $defaultOrderTemplates = [
    'welcome'=>"Terima kasih kak {name}, pesanan kami terima.\n\n{summary}\n\nTotal: {total}",
    'fu1'=>"Halo kak {name}, kami follow up pesanan kakak yang belum dibayar ya 😊",
    'fu2'=>"Halo kak {name}, kami belum menerima pembayaran. Jika sudah transfer mohon kirim bukti 🙏",
    'fu3'=>"Halo kak {name}, stok terbatas ya kak. Kalau masih lanjut bisa segera dibayar 😊",
    'fu4'=>"Halo kak {name}, ini follow up terakhir. Jika masih ingin lanjut silakan lakukan pembayaran 🙏",

    'upsell'=>"Halo kak {name}, kami ada rekomendasi produk tambahan yang cocok untuk pesanan kakak 😊",
    'order_detail'=>"Halo kak {name}, berikut detail pesanan kakak:\n\n{summary}\n\nTotal: {total}",
    'processing'=>"Halo kak {name}, pesanan sedang kami proses ya 🙏",
    'completed'=>"Halo kak {name}, pesanan sudah selesai. Terima kasih 🙏",
    'sms'=>"SMS ke {name} ({phone})",
    'call'=>"Telepon ke {name} ({phone})",
  ];

  $mergedOrderTemplates = array_merge($defaultOrderTemplates, $orderTemplates ?? []);
@endphp

            @forelse($orders as $o)
              @php
                $payload = $o->data ?? [];
                $summary = $payload['summary'] ?? null;
                $submittedAt = $o->submitted_at ?? $o->created_at;

                // ===== ambil nama & hp dari payload['fields'] =====
                $fields = $payload['fields'] ?? [];
                $find = fn($keys) =>
                  collect($fields)->first(fn($v,$k)=>collect($keys)->contains(fn($x)=>str_contains(strtolower((string)$k),(string)$x)));

                $name = $find(['nama','name']) ?? '-';
                $phone = preg_replace('/[^0-9]/','', (string)($find(['hp','wa','tel','phone','telepon']) ?? ''));
                if(str_starts_with($phone,'0')) $phone='62'.substr($phone,1);

                // ===== summary text & total text =====
                $summaryText = '';
                if (is_array($summary) && count($summary)) {
                  $summaryText = collect($summary)
                    ->map(fn($s)=>($s['label']??'').': '.($s['value']??''))
                    ->implode("\n");
                }
                $totalText = $o->total_price !== null ? ('Rp '.number_format((float)$o->total_price, 0, ',', '.')) : '-';

                // ===== followup row data =====
                $rowData = [
                  'id'=>(string)$o->id,
                  'name'=>(string)$name,
                  'phone'=>(string)$phone,
                  'summary'=>(string)($summaryText ?: '-'),
                  'total'=>(string)$totalText,
                  'followup_store_url'=>route('app.orders.followup',$o),
                  'templates'=> $mergedOrderTemplates,

                  'state'=>[
                    'welcome'=>!empty($o->welcome_sent_at),
                    'fu1'=>!empty($o->followup1_sent_at),
                    'fu2'=>!empty($o->followup2_sent_at),
                    'fu3'=>!empty($o->followup3_sent_at),
                    'fu4'=>!empty($o->followup4_sent_at),
                  ],
                ];

                // warna mirip screenshot: W hijau, 1 kuning, 2-4 abu; kalau belum sent = abu muda
                // konsisten: kalau sudah sent = HIJAU, kalau belum = abu
$fuColors = [
  'welcome' => $rowData['state']['welcome'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600',
  'fu1'     => $rowData['state']['fu1'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600',
  'fu2'     => $rowData['state']['fu2'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600',
  'fu3'     => $rowData['state']['fu3'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600',
  'fu4'     => $rowData['state']['fu4'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600',
];


                $chatSvg = '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                  <path d="M20 2H4a2 2 0 0 0-2 2v14l4-3h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z"/>
                </svg>';
                $btnBase = 'relative inline-flex h-8 w-8 items-center justify-center rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-400/40';
                $labelBase = 'absolute -top-1 -right-1 grid h-4 w-4 place-items-center rounded-full text-[10px] font-bold';
              @endphp

              <tr class="align-top hover:bg-slate-50/60">
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="font-semibold text-slate-900">{{ $submittedAt?->format('Y-m-d') }}</div>
                  <div class="text-xs text-slate-500">{{ $submittedAt?->format('H:i') }}</div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="font-semibold text-slate-900">{{ $o->form?->name ?? '-' }}</div>
                  <div class="text-xs text-slate-500">#{{ $o->id }}</div>
                </td>

                <td class="px-4 py-3">
                  @if (is_array($summary) && count($summary))
                    <div class="space-y-1">
                      @foreach($summary as $s)
                        <div class="text-slate-700">
                          <span class="font-semibold text-slate-900">{{ $s['label'] ?? '-' }}:</span>
                          <span>{{ $s['value'] ?? '-' }}</span>
                        </div>
                      @endforeach
                    </div>
                  @else
                    <div class="text-slate-500">Tidak ada ringkasan (summary kosong).</div>
                  @endif
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="font-semibold text-slate-900">
                    {{ $totalText }}
                  </div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  @php
                    $badge = match($o->status) {
                      'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                      'processed' => 'bg-blue-50 text-blue-800 border-blue-200',
                      'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                      'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
                      default => 'bg-slate-50 text-slate-800 border-slate-200',
                    };
                  @endphp
                  <span class="inline-flex items-center rounded-xl border px-2.5 py-1 text-xs font-semibold {{ $badge }}">
                    {{ strtoupper($o->status) }}
                  </span>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  @php
                    $pbadge = match($o->payment_status) {
                      'paid' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                      'unpaid' => 'bg-slate-50 text-slate-800 border-slate-200',
                      'refunded' => 'bg-rose-50 text-rose-800 border-rose-200',
                      default => 'bg-slate-50 text-slate-800 border-slate-200',
                    };
                  @endphp
                  <span class="inline-flex items-center rounded-xl border px-2.5 py-1 text-xs font-semibold {{ $pbadge }}">
                    {{ strtoupper($o->payment_status ?? 'UNPAID') }}
                  </span>
                </td>

                {{-- FOLLOW-UP column --}}
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center gap-1">

                    <button type="button"
                      class="{{ $btnBase }} {{ $fuColors['welcome'] }}"
                      onclick='openFU(@json($rowData),"welcome")'
                      title="Welcome">
                      {!! $chatSvg !!}
                      <span class="{{ $labelBase }} bg-white/90 text-slate-800">W</span>
                    </button>

                    <button type="button"
                      class="{{ $btnBase }} {{ $fuColors['fu1'] }}"
                      onclick='openFU(@json($rowData),"fu1")'
                      title="Follow Up 1">
                      {!! $chatSvg !!}
                      <span class="{{ $labelBase }} bg-white/90 text-slate-800">1</span>
                    </button>

                    <button type="button"
                      class="{{ $btnBase }} {{ $fuColors['fu2'] }}"
                      onclick='openFU(@json($rowData),"fu2")'
                      title="Follow Up 2">
                      {!! $chatSvg !!}
                      <span class="{{ $labelBase }} bg-white/90 text-slate-800">2</span>
                    </button>

                    <button type="button"
                      class="{{ $btnBase }} {{ $fuColors['fu3'] }}"
                      onclick='openFU(@json($rowData),"fu3")'
                      title="Follow Up 3">
                      {!! $chatSvg !!}
                      <span class="{{ $labelBase }} bg-white/90 text-slate-800">3</span>
                    </button>

                    <button type="button"
                      class="{{ $btnBase }} {{ $fuColors['fu4'] }}"
                      onclick='openFU(@json($rowData),"fu4")'
                      title="Follow Up 4">
                      {!! $chatSvg !!}
                      <span class="{{ $labelBase }} bg-white/90 text-slate-800">4</span>
                    </button>

                    {{-- More --}}
                    <div class="relative">
                      <button type="button"
                        class="{{ $btnBase }} bg-slate-300 text-slate-800 hover:bg-slate-400"
                        onclick='toggleMoreMenu("more-{{ $o->id }}")'
                        title="More">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                          <path d="M12 7a2 2 0 1 0 .001-4.001A2 2 0 0 0 12 7Zm0 7a2 2 0 1 0 .001-4.001A2 2 0 0 0 12 14Zm0 7a2 2 0 1 0 .001-4.001A2 2 0 0 0 12 21Z"/>
                        </svg>
                      </button>

                      <div id="more-{{ $o->id }}"
                        class="absolute left-0 z-20 mt-2 hidden w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                        <div class="px-3 py-2 text-xs font-semibold text-slate-500">WhatsApp</div>
                        <button type="button" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-50"
                          onclick='openFU(@json($rowData),"upsell"); hideMoreMenu("more-{{ $o->id }}")'>Up Selling</button>
                        <button type="button" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-50"
                          onclick='openFU(@json($rowData),"order_detail"); hideMoreMenu("more-{{ $o->id }}")'>Order Detail</button>
                        <button type="button" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-50"
                          onclick='openFU(@json($rowData),"processing"); hideMoreMenu("more-{{ $o->id }}")'>Processing</button>
                        <button type="button" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-50"
                          onclick='openFU(@json($rowData),"completed"); hideMoreMenu("more-{{ $o->id }}")'>Completed</button>

                        <div class="border-t border-slate-200"></div>
                        <div class="px-3 py-2 text-xs font-semibold text-slate-500">Other</div>
                        <button type="button" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-50"
                          onclick='openFU(@json($rowData),"sms"); hideMoreMenu("more-{{ $o->id }}")'>Send SMS</button>
                        <button type="button" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-50"
                          onclick='openFU(@json($rowData),"call"); hideMoreMenu("more-{{ $o->id }}")'>Phone Call</button>
                      </div>
                    </div>

                  </div>
                </td>

                {{-- Tindakan column (as-is) --}}
                <td class="px-4 py-3 whitespace-nowrap">
                  <form method="POST" action="{{ route('app.orders.updateStatus', $o) }}">
                    @csrf
                    @method('PATCH')

                    <select name="action" class="input w-[190px]" onchange="this.form.submit()">
                      <option value="" selected disabled>Tindakan</option>
                      <optgroup label="Order">
                        <option value="mark_pending">Mark As Pending</option>
                        <option value="mark_processed">Mark As Process</option>
                        <option value="mark_completed">Mark As Complete</option>
                        <option value="mark_cancelled">Mark As Cancel</option>
                      </optgroup>
                      <optgroup label="Payment">
                        <option value="mark_paid">Mark As Paid</option>
                        <option value="mark_unpaid">Mark As Unpaid</option>
                        <option value="mark_refunded">Mark As Refund</option>
                      </optgroup>
                    </select>
                  </form>

                  <div class="mt-2 text-xs text-slate-500">
                    Perubahan otomatis tersimpan.
                  </div>

                  <form method="POST"
                        action="{{ route('app.orders.destroy', $o) }}"
                        class="mt-2"
                        onsubmit="return confirm('Yakin hapus order #{{ $o->id }}? Tindakan ini permanen.')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn-outline w-[190px] border-rose-200 text-rose-700 hover:bg-rose-50">
                      Hapus Order
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-4 py-10">
                  <div class="text-center">
                    <div class="text-sm font-semibold text-slate-900">Belum ada order.</div>
                    <div class="mt-1 text-sm text-slate-600">
                      Coba submit form dari embed dulu, atau cek filter.
                    </div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="border-t border-slate-200/70 px-4 py-3">
        {{ $orders->links() }}
      </div>
    </div>
  </div>

  {{-- MODAL --}}
<div id="fuModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-lg p-4 space-y-3">

    {{-- Header + tombol X --}}
    <div class="flex items-start justify-between gap-3">
      <div class="font-semibold" id="fuTitle"></div>

      <button type="button"
        onclick="closeFU()"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50"
        aria-label="Tutup">
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <input id="fuPhone" class="input" placeholder="628xxx">
    <textarea id="fuText" class="input min-h-[120px]"></textarea>

    <div class="flex justify-end gap-2">
      <button type="button" onclick="closeFU()" class="btn-outline">Batal</button>
      <button type="button" onclick="saveFU()" class="btn-outline">Save Template</button>
      <button type="button" onclick="sendFU()" class="btn-primary">Follow Up</button>
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

  let FU_CTX=null


  function openFU(ctx,key){
  FU_CTX={ctx,key}
  document.getElementById('fuTitle').innerText=`Follow Up ${key.toUpperCase()}`
  document.getElementById('fuPhone').value=ctx.phone || ''

  const fallback = ((ctx.templates || {})[key] || '');
  document.getElementById('fuText').value = ugGet('orders', key, fallback);

  document.getElementById('fuModal').classList.remove('hidden')
}

  function closeFU(){
  document.getElementById('fuModal').classList.add('hidden')
  FU_CTX = null
}

async function saveFU(){
  const {ctx,key} = FU_CTX || {}
  if(!ctx || !key) return

  const template = document.getElementById('fuText').value || ''

  const res = await fetch('{{ route('app.message_templates.save') }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      context: 'orders',
      key: key,
      template: template,
    })
  })

  if(!res.ok){
    const t = await res.text()
    alert('Gagal save template: ' + t)
    return
  }
  ugSet('orders', key, template);
  // ✅ PENTING: update cache template di browser (tanpa reload pun kebaca)
  ctx.templates = ctx.templates || {}
  ctx.templates[key] = template

  alert('Template tersimpan')
}


  async function sendFU(){
    const {ctx,key}=FU_CTX
    const phone=document.getElementById('fuPhone').value
   const tpl = document.getElementById('fuText').value || ''
const text = renderOrderTpl(tpl, ctx)

await fetch(ctx.followup_store_url,{
  method:'POST',
  headers:{
    'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
    'Content-Type':'application/json',
    'Accept':'application/json'
  },
  body:JSON.stringify({key,phone,message:text})
})

if(!['sms','call'].includes(key)){
  window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`)
}


    location.reload()
  }

  // More menu handler
  function toggleMoreMenu(id){
    const el = document.getElementById(id)
    if(!el) return
    const isHidden = el.classList.contains('hidden')
    document.querySelectorAll('[id^="more-"]').forEach(x=>x.classList.add('hidden'))
    if(isHidden) el.classList.remove('hidden')
    else el.classList.add('hidden')
  }
  function renderOrderTpl(tpl, ctx){
  return String(tpl || '')
    .replaceAll('{name}', ctx.name || '-')
    .replaceAll('{summary}', ctx.summary || '-')
    .replaceAll('{total}', ctx.total || '-')
    .replaceAll('{phone}', ctx.phone || '-')
}

  function hideMoreMenu(id){
    const el = document.getElementById(id)
    if(el) el.classList.add('hidden')
  }
  document.addEventListener('click', function(e){
    const clickedMenu = e.target.closest('[id^="more-"]')
    const clickedBtn = e.target.closest('button[title="More"]')
    if(clickedMenu || clickedBtn) return
    document.querySelectorAll('[id^="more-"]').forEach(x=>x.classList.add('hidden'))
  })
  </script>
@endsection
