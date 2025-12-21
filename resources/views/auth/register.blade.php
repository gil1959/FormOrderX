<x-guest-layout>
  <div class="grid lg:grid-cols-2 gap-6 items-stretch">
    {{-- Left: Form --}}
    <div>
      <div class="flex items-center gap-3">
        <x-application-logo class="h-9 w-auto" />
        <div class="leading-tight">
          <p class="text-sm font-semibold tracking-tight text-slate-900">{{ config('app.name', 'Form Order System') }}</p>
          <p class="text-xs text-slate-500">Pembuatan akun</p>
        </div>
      </div>

      <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Buat akun</h1>
      <p class="mt-2 text-sm text-slate-600">
        Isi data dasar untuk membuat akun.
      </p>

      <form class="mt-6 space-y-4" method="POST" action="{{ route('register') }}">
        @csrf

        <div>
          <label for="name" class="label">Nama</label>
          <input
            id="name"
            name="name"
            type="text"
            class="input mt-2"
            value="{{ old('name') }}"
            required
            autofocus
            autocomplete="name"
            placeholder="Nama pengguna"
          />
          <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
          <label for="email" class="label">Email</label>
          <input
            id="email"
            name="email"
            type="email"
            class="input mt-2"
            value="{{ old('email') }}"
            required
            autocomplete="username"
            placeholder="nama@domain.com"
          />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
          <label for="password" class="label">Kata sandi</label>
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
          <label for="password_confirmation" class="label">Konfirmasi kata sandi</label>
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

        <button type="submit" class="btn-primary w-full">Buat akun</button>

        <div class="pt-2 text-sm text-slate-600">
          Sudah punya akun?
          <a class="font-semibold text-slate-900 hover:underline" href="{{ route('login') }}">Masuk</a>
        </div>
      </form>
    </div>

    {{-- Right: Info --}}
    <div class="hidden lg:block">
      <div class="card p-8 h-full">
        <p class="text-sm font-semibold text-slate-900">Catatan</p>
        <p class="mt-2 text-sm text-slate-600">
          Setelah akun dibuat, kamu bisa langsung mengakses dashboard.
        </p>

        <div class="mt-6 grid gap-3">
          <div class="card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Akses modul</p>
            <p class="mt-1 text-xs text-slate-600">Dashboard, Forms, Orders, Abandoned</p>
          </div>
          <div class="card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Pengaturan</p>
            <p class="mt-1 text-xs text-slate-600">Profil dan pengaturan dasar akun</p>
          </div>
        </div>

        
      </div>
    </div>
  </div>
</x-guest-layout>
