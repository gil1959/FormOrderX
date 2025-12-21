<x-guest-layout>
  <div class="grid lg:grid-cols-2 gap-6 items-stretch">
    {{-- Left: Form --}}
    <div>
      <div class="flex items-center gap-3">
        <x-application-logo class="h-9 w-auto" />
        <div class="leading-tight">
          <p class="text-sm font-semibold tracking-tight text-slate-900">{{ config('app.name', 'Form Order System') }}</p>
          <p class="text-xs text-slate-500">Akses akun</p>
        </div>
      </div>

      <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Masuk</h1>
      <p class="mt-2 text-sm text-slate-600">
        Gunakan email dan kata sandi yang terdaftar.
      </p>

      <x-auth-session-status class="mt-4" :status="session('status')" />

      <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
        @csrf

        <div>
          <label for="email" class="label">Email</label>
          <input
            id="email"
            name="email"
            type="email"
            class="input mt-2"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="username"
            placeholder="nama@domain.com"
          />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="label">Kata sandi</label>
            @if (Route::has('password.request'))
              <a class="text-sm font-semibold text-slate-700 hover:text-slate-900" href="{{ route('password.request') }}">
                Lupa kata sandi?
              </a>
            @endif
          </div>

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

        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
          <input id="remember_me" type="checkbox" class="rounded border-slate-300" name="remember">
          Ingat perangkat ini
        </label>

        <button type="submit" class="btn-primary w-full">Masuk</button>

        <div class="pt-2 text-sm text-slate-600">
          Belum punya akun?
          <a class="font-semibold text-slate-900 hover:underline" href="{{ route('register') }}">Buat akun</a>
        </div>
      </form>
    </div>

    {{-- Right: Info --}}
    <div class="hidden lg:block">
      <div class="card p-8 h-full">
        <p class="text-sm font-semibold text-slate-900">Ringkas</p>
        <p class="mt-2 text-sm text-slate-600">
          Akses cepat ke modul utama:
        </p>

        <div class="mt-6 grid gap-3">
          <div class="card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Forms</p>
            <p class="mt-1 text-xs text-slate-600">Buat dan kelola form</p>
          </div>
          <div class="card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Orders</p>
            <p class="mt-1 text-xs text-slate-600">Pantau order masuk</p>
          </div>
          <div class="card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Abandoned</p>
            <p class="mt-1 text-xs text-slate-600">Lihat sesi yang belum selesai</p>
          </div>
        </div>

        
      </div>
    </div>
  </div>
</x-guest-layout>
