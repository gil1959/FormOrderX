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
      <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-6 lg:items-end">
        <div class="lg:col-span-2">
          <label class="label">Status Order</label>
          <select name="status" class="input">
            <option value="">Semua</option>
            @foreach (['pending' => 'Pending', 'processed' => 'Process', 'completed' => 'Complete', 'cancelled' => 'Cancel'] as $k => $v)
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

        <div class="lg:col-span-2">
          <button class="btn-primary w-full">Terapkan Filter</button>
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
              <th class="px-4 py-3 font-semibold">Tindakan</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-200/70">
            @forelse($orders as $o)
              @php
                $payload = $o->data ?? [];
                $summary = $payload['summary'] ?? null;
                $submittedAt = $o->submitted_at ?? $o->created_at;
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
                    {{ $o->total_price !== null ? ('Rp '.number_format((float)$o->total_price, 0, ',', '.')) : '-' }}
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
                <td colspan="7" class="px-4 py-10">
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
@endsection
