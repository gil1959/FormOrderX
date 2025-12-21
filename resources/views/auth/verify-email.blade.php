<x-guest-layout>
  <div>
    <div class="flex items-center gap-3">
      <x-application-logo class="h-9 w-auto" />
      <div class="leading-tight">
        <p class="text-sm font-semibold tracking-tight text-slate-900">{{ config('app.name', 'Form Order System') }}</p>
        <p class="text-xs text-slate-500">Verifikasi email</p>
      </div>
    </div>

    <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Verifikasi email</h1>
    <p class="mt-2 text-sm text-slate-600">
      Link verifikasi sudah dikirim ke email kamu. Jika belum masuk, kamu bisa kirim ulang.
    </p>

    @if (session('status') == 'verification-link-sent')
      <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        Link verifikasi baru sudah dikirim.
      </div>
    @endif

    <div class="mt-6 flex flex-col sm:flex-row gap-3">
      <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
        @csrf
        <button type="submit" class="btn-primary w-full">Kirim ulang</button>
      </form>

      <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
        @csrf
        <button type="submit" class="btn-outline w-full">Keluar</button>
      </form>
    </div>
  </div>
</x-guest-layout>
