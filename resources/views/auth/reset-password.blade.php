<x-guest-layout>
  <div>
    <div class="flex items-center gap-3">
      <x-application-logo class="h-9 w-auto" />
      <div class="leading-tight">
        <p class="text-sm font-semibold tracking-tight text-slate-900">{{ config('app.name', 'Form Order System') }}</p>
        <p class="text-xs text-slate-500">Reset kata sandi</p>
      </div>
    </div>

    <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Buat kata sandi baru</h1>
    <p class="mt-2 text-sm text-slate-600">
      Masukkan kata sandi baru untuk akun kamu.
    </p>

    <form class="mt-6 space-y-4" method="POST" action="{{ route('password.store') }}">
      @csrf

      <input type="hidden" name="token" value="{{ $request->route('token') }}">

      <div>
        <label for="email" class="label">Email</label>
        <input
          id="email"
          name="email"
          type="email"
          class="input mt-2"
          value="{{ old('email', $request->email) }}"
          required
          autocomplete="username"
          placeholder="nama@domain.com"
        />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div>
        <label for="password" class="label">Kata sandi baru</label>
        <input
          id="password"
          name="password"
          type="password"
          class="input mt-2"
          required
          autocomplete="new-password"
          placeholder="Minimal 8 karakter"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <div>
        <label for="password_confirmation" class="label">Konfirmasi kata sandi baru</label>
        <input
          id="password_confirmation"
          name="password_confirmation"
          type="password"
          class="input mt-2"
          required
          autocomplete="new-password"
          placeholder="Ulangi kata sandi"
        />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
      </div>

      <button type="submit" class="btn-primary w-full">Simpan kata sandi</button>
    </form>
  </div>
</x-guest-layout>
