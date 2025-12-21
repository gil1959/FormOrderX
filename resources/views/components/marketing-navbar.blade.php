<header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/70 backdrop-blur">
  <div class="container-pad py-4 flex items-center justify-between gap-4">

    {{-- Brand --}}
    <a href="{{ route('landing') }}" class="inline-flex items-center gap-3">
      <img
        src="{{ asset('images/brand/logo.svg') }}"
        alt="{{ config('app.name', 'Form Order System') }}"
        class="h-9 w-auto"
      />
     
    </a>

    {{-- Desktop Nav --}}
    <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-700">
      <a href="#features" class="hover:text-slate-900">Fitur</a>
      <a href="#workflow" class="hover:text-slate-900">Alur</a>
      <a href="#access" class="hover:text-slate-900">Akses</a>
      <a href="#faq" class="hover:text-slate-900">FAQ</a>
    </nav>

    {{-- Actions --}}
    <div class="flex items-center gap-2">
      @auth
        <a class="btn-outline" href="{{ route('app.dashboard') }}">Dashboard</a>
      @else
        <a class="btn-outline" href="{{ route('login') }}">Masuk</a>
        <a class="btn-primary" href="{{ route('register') }}">Buat Akun</a>
      @endauth

      {{-- Mobile toggle --}}
      <button
        type="button"
        class="md:hidden btn-outline"
        aria-label="Buka menu"
        onclick="document.getElementById('mktMobileNav').classList.toggle('hidden')"
      >
        ☰
      </button>
    </div>
  </div>

  {{-- Mobile Nav --}}
  <div id="mktMobileNav" class="md:hidden hidden border-t border-slate-200/70 bg-white/70 backdrop-blur">
    <div class="container-pad py-3 flex flex-col gap-2">
      <a href="#features" class="btn-soft w-full justify-start">Fitur</a>
      <a href="#workflow" class="btn-soft w-full justify-start">Alur</a>
      <a href="#access" class="btn-soft w-full justify-start">Akses</a>
      <a href="#faq" class="btn-soft w-full justify-start">FAQ</a>
    </div>
  </div>
</header>
