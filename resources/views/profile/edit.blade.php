{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('header')
  <div>
    <h1 class="text-xl font-semibold tracking-tight text-slate-900">Profil</h1>
    <p class="mt-1 text-sm text-slate-600">Atur akun dan keamanan.</p>
  </div>
@endsection

@section('content')
  <div class="space-y-4 max-w-4xl">
    <div class="card-solid p-6">
      <div class="max-w-xl">
        @include('profile.partials.update-profile-information-form')
      </div>
    </div>

    <div class="card-solid p-6">
      <div class="max-w-xl">
        @include('profile.partials.update-password-form')
      </div>
    </div>

    <div class="card-solid p-6">
      <div class="max-w-xl">
        @include('profile.partials.delete-user-form')
      </div>
    </div>
  </div>
@endsection
