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
    <div class="absolute inset-0 bg-grid opacity-50"></div>
    <div class="absolute inset-0 bg-noise opacity-30"></div>

    <div class="pointer-events-none absolute -top-24 -left-24 h-96 w-96 rounded-full bg-indigo-400/18 blur-3xl"></div>
    <div class="pointer-events-none absolute bottom-0 -right-24 h-96 w-96 rounded-full bg-emerald-400/14 blur-3xl"></div>

    <div class="relative">
      <x-app-navbar />

      <main class="container-pad py-8">
        @hasSection('header')
          <div class="card p-6 mb-6">
            @yield('header')
          </div>
        @endif

        @if (session('success'))
          <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
          </div>
        @endif

        @yield('content')

        <footer class="mt-10 text-center text-xs text-slate-500">
          © {{ date('Y') }} {{ config('app.name', 'Form Order System') }}
        </footer>
      </main>
    </div>
  </div>
</body>
</html>
