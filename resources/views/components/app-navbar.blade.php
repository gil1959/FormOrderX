@props([
  'links' => [
    ['label' => 'Dashboard', 'route' => 'app.dashboard'],
    ['label' => 'Form', 'route' => 'app.forms.index'],
    ['label' => 'Order', 'route' => 'app.orders.index'],
    ['label' => 'Abandoned', 'route' => 'app.abandoned.index'],
    ['label' => 'Profil', 'route' => 'profile.edit'],
    ['label' => 'Settings', 'route' => 'app.settings.index'],
  ],
])

@php
  $isActive = function (string $routeName): bool {
      if (str_ends_with($routeName, '.index')) {
          $prefix = substr($routeName, 0, -strlen('.index'));
          return request()->routeIs($prefix . '.*') || request()->routeIs($routeName);
      }
      return request()->routeIs($routeName);
  };

  // ICON RENDERER (aman walau icon gak ada)
  $icon = function (?string $name): string {
      $base = "class='h-4 w-4' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'";

      return match ($name) {
          'home' => "<svg $base><path d='M3 9l9-7 9 7'/><path d='M9 22V12h6v10'/></svg>",
          'forms' => "<svg $base><rect x='6' y='4' width='12' height='16' rx='2'/><path d='M9 8h6'/><path d='M9 12h6'/><path d='M9 16h4'/></svg>",
          'bag' => "<svg $base><path d='M6 7h12l-1 14H7L6 7z'/><path d='M9 7a3 3 0 0 1 6 0'/></svg>",
          'clock' => "<svg $base><circle cx='12' cy='12' r='9'/><path d='M12 7v6l4 2'/></svg>",
          'user' => "<svg $base><path d='M20 21a8 8 0 0 0-16 0'/><circle cx='12' cy='8' r='4'/></svg>",
          'gear' => "<svg $base><path d='M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z'/><path d='M19.4 15a7.8 7.8 0 0 0 .1-2l2-1.2-2-3.4-2.3.7a7.5 7.5 0 0 0-1.7-1L15 4h-6l-.5 3.1a7.5 7.5 0 0 0-1.7 1L4.5 7.4 2.5 10.8l2 1.2a7.8 7.8 0 0 0 .1 2l-2 1.2 2 3.4 2.3-.7a7.5 7.5 0 0 0 1.7 1L9 20h6l.5-3.1a7.5 7.5 0 0 0 1.7-1l2.3.7 2-3.4-2-1.2z'/></svg>",
          default => "<svg $base><path d='M12 12h.01'/></svg>",
      };
  };

  // fallback icon berdasarkan route kalau item gak punya 'icon'
  $iconForRoute = function (string $route): string {
      return match ($route) {
          'app.dashboard' => 'home',
          'app.forms.index' => 'forms',
          'app.orders.index' => 'bag',
          'app.abandoned.index' => 'clock',
          'profile.edit' => 'user',
          'app.settings.index' => 'gear',
          default => 'default',
      };
  };
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/70 backdrop-blur">
  <div class="container-pad py-4 flex items-center justify-between gap-4">

    {{-- Brand --}}
    <a href="{{ route('app.dashboard') }}" class="inline-flex items-center gap-3">
      {{-- kalau application-logo lu udah diganti ke file logo.svg, ini jadi proper --}}
      <x-application-logo class="h-9 w-auto" />
      <div class="leading-tight">
        <p class="text-sm font-semibold tracking-tight text-slate-900">{{ config('app.name', 'Form Order System') }}</p>
        <p class="text-xs text-slate-500">Dashboard</p>
      </div>
    </a>

    {{-- Desktop Navigation --}}
    <nav class="hidden md:flex items-center gap-2">
      @foreach ($links as $item)
        @php
          $active = $isActive($item['route']);
          $iconName = $item['icon'] ?? $iconForRoute($item['route']);  // <- INI YANG BIKIN GA AKAN ERROR LAGI
        @endphp

        <a
          href="{{ route($item['route']) }}"
          class="{{ $active ? 'btn-primary' : 'btn-soft' }} inline-flex items-center gap-2"
        >
          {!! $icon($iconName) !!}
          <span>{{ $item['label'] }}</span>
        </a>
      @endforeach
    </nav>

    {{-- Right actions --}}
    <div class="flex items-center gap-2">
      <div class="hidden sm:block text-right">
        <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
      </div>

      {{-- Mobile toggle --}}
      <button
        type="button"
        class="md:hidden btn-outline"
        aria-label="Buka menu"
        onclick="document.getElementById('appMobileNav').classList.toggle('hidden')"
      >
        ☰
      </button>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-outline">Keluar</button>
      </form>
    </div>
  </div>

  {{-- Mobile Navigation --}}
  <div id="appMobileNav" class="md:hidden hidden border-t border-slate-200/70 bg-white/70 backdrop-blur">
    <div class="container-pad py-3 flex flex-col gap-2">
      @foreach ($links as $item)
        @php
          $active = $isActive($item['route']);
          $iconName = $item['icon'] ?? $iconForRoute($item['route']);
        @endphp

        <a
          href="{{ route($item['route']) }}"
          class="{{ $active ? 'btn-primary w-full justify-start' : 'btn-soft w-full justify-start' }} inline-flex items-center gap-2"
        >
          {!! $icon($iconName) !!}
          <span>{{ $item['label'] }}</span>
        </a>
      @endforeach
    </div>
  </div>
</header>
