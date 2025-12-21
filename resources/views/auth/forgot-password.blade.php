<x-guest-layout>
  <div>
    <div class="flex items-center gap-3">
      <x-application-logo class="h-9 w-auto" />
      <div class="leading-tight">
        <p class="text-sm font-semibold tracking-tight text-slate-900">{{ config('app.name', 'Form Order System') }}</p>
        <p class="text-xs text-slate-500">Reset kata sandi</p>
      </div>
    </div>

    <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Lupa kata sandi</h1>
    <p class="mt-2 text-sm text-slate-600">
      Masukkan email yang terdaftar. Link reset akan dikirim ke email tersebut.
    </p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form class="mt-6 space-y-4" method="POST" action="{{ route('password.email') }}">
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

      <button type="submit" class="btn-primary w-full">Kirim link reset</button>

      <div class="pt-2 text-sm text-slate-600">
        <a class="font-semibold text-slate-900 hover:underline" href="{{ route('login') }}">Kembali ke login</a>
      </div>
    </form>
  </div>
</x-guest-layout>
