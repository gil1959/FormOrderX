<x-guest-layout>
  <div>
    <div class="flex items-center gap-3">
      <x-application-logo class="h-9 w-auto" />
      <div class="leading-tight">
        <p class="text-sm font-semibold tracking-tight text-slate-900">{{ config('app.name', 'Form Order System') }}</p>
        <p class="text-xs text-slate-500">Konfirmasi akses</p>
      </div>
    </div>

    <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Konfirmasi kata sandi</h1>
    <p class="mt-2 text-sm text-slate-600">
      Untuk melanjutkan, masukkan kata sandi kamu.
    </p>

    <form class="mt-6 space-y-4" method="POST" action="{{ route('password.confirm') }}">
      @csrf

      <div>
        <label for="password" class="label">Kata sandi</label>
        <input
          id="password"
          name="password"
          type="password"
          class="input mt-2"
          required
          autocomplete="current-password"
          placeholder="••••••••"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <button type="submit" class="btn-primary w-full">Lanjut</button>
    </form>
  </div>
</x-guest-layout>
