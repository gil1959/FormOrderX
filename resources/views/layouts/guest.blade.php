<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Form Order System') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
  <div class="relative min-h-screen overflow-hidden">
    <div class="absolute inset-0 bg-grid opacity-65"></div>
    <div class="absolute inset-0 bg-noise opacity-35"></div>
    <div class="pointer-events-none absolute -top-24 -left-24 h-96 w-96 rounded-full bg-indigo-400/25 blur-3xl"></div>
    <div class="pointer-events-none absolute bottom-0 -right-24 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>

    <main class="relative">
      <div class="container-pad py-10">
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-3">
          <x-application-logo class="h-9 w-auto" />
          <span class="sr-only">{{ config('app.name', 'Form Order System') }}</span>
        </a>

        <div class="mt-8">
          <div class="card p-6 sm:p-8">
            {{ $slot }}
          </div>

          <footer class="mt-6 text-center text-xs text-slate-500">
            © {{ date('Y') }} {{ config('app.name', 'Form Order System') }}
          </footer>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
