@extends('layouts.app')

@section('header')
  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-semibold tracking-tight text-slate-900">Dashboard</h1>
      <p class="mt-1 text-sm text-slate-600">Ringkasan aktivitas dan akses cepat ke pengelolaan form.</p>
    </div>
    <a href="{{ route('app.forms.create') }}" class="btn-primary">Buat Form</a>
  </div>
@endsection

@section('content')
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="card-solid p-5">
      <p class="text-xs font-semibold text-slate-500">Total Form</p>
      <p class="mt-2 text-2xl font-semibold text-slate-900">{{ auth()->user()->forms()->count() }}</p>
      <p class="mt-1 text-sm text-slate-600">Jumlah form yang Anda miliki.</p>
    </div>

    <div class="card-solid p-5">
      <p class="text-xs font-semibold text-slate-500">Form Aktif</p>
      <p class="mt-2 text-2xl font-semibold text-slate-900">{{ auth()->user()->forms()->where('is_active', true)->count() }}</p>
      <p class="mt-1 text-sm text-slate-600">Form yang saat ini dapat digunakan.</p>
    </div>

    <div class="card-solid p-5">
      <p class="text-xs font-semibold text-slate-500">Submission Hari Ini</p>
      <p class="mt-2 text-2xl font-semibold text-slate-900">
        {{ auth()->user()->submissions()->whereDate('created_at', now()->toDateString())->count() }}
      </p>
      <p class="mt-1 text-sm text-slate-600">Total submission yang masuk hari ini.</p>
    </div>
  </div>

  <div class="mt-6 card-solid p-5">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold">Akses Cepat</h2>
        <p class="mt-1 text-sm text-slate-600">Kelola form dan pengaturan tampilan.</p>
      </div>
      <a href="{{ route('app.forms.index') }}" class="btn-outline">Lihat Semua</a>
    </div>

    <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
      <a href="{{ route('app.forms.index') }}" class="rounded-2xl border border-slate-200 bg-white p-4 hover:bg-slate-50 transition">
        <p class="font-semibold">Daftar Form</p>
        <p class="mt-1 text-sm text-slate-600">Lihat dan kelola seluruh form.</p>
      </a>
      <a href="{{ route('app.forms.create') }}" class="rounded-2xl border border-slate-200 bg-white p-4 hover:bg-slate-50 transition">
        <p class="font-semibold">Buat Form Baru</p>
        <p class="mt-1 text-sm text-slate-600">Buat form baru dan siapkan embed script.</p>
      </a>
      <a href="{{ route('profile.edit') }}" class="rounded-2xl border border-slate-200 bg-white p-4 hover:bg-slate-50 transition">
        <p class="font-semibold">Profil</p>
        <p class="mt-1 text-sm text-slate-600">Atur data akun dan keamanan.</p>
      </a>
    </div>
  </div>
@endsection
