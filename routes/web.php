<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Embed\FormEmbedController;

// Landing page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Semua route yang butuh login
Route::middleware(['auth'])->group(function () {
    // Alias dashboard lama Breeze -> redirect ke admin dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    // Route profile bawaan Breeze (dipakai di navigation.blade.php)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// route auth Breeze (login, register, dll)
require __DIR__ . '/auth.php';

// route admin (dashboard + form builder, dll)
require __DIR__ . '/admin.php';

Route::get('/embed/{token}.js', [FormEmbedController::class, 'script']);
