<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\FormFieldController;
use App\Http\Controllers\App\OrderController;
use App\Http\Controllers\App\AbandonedCartController;
use App\Http\Controllers\App\SettingsController;



Route::middleware(['auth', 'verified'])
    ->name('app.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('app.dashboard');
        })->name('dashboard');

        // daftar form
        Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
        Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
        Route::post('/forms', [FormController::class, 'store'])->name('forms.store');

        // halaman design
        Route::get('/forms/{form}/design', [FormController::class, 'design'])->name('forms.design');
        Route::post('/forms/{form}/design', [FormController::class, 'updateDesign'])->name('forms.design.update');

        // preview
        Route::get('/forms/{form}/preview', [FormController::class, 'preview'])->name('forms.preview');
        Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('forms.destroy');

        // kelola field
        Route::get('/forms/{form}/fields', [FormFieldController::class, 'edit'])->name('forms.fields.edit');
        Route::post('/forms/{form}/fields', [FormFieldController::class, 'store'])->name('forms.fields.store');
        Route::delete('/forms/{form}/fields/{field}', [FormFieldController::class, 'destroy'])->name('forms.fields.destroy');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{submission}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::delete('/orders/{submission}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::delete('/abandoned-carts/{session}', [AbandonedCartController::class, 'destroy'])->name('abandoned.destroy');
Route::post('/orders/{submission}/follow-up', [OrderController::class, 'storeFollowUp'])
    ->name('orders.followup');

Route::post('/abandoned-carts/{session}/follow-up', [AbandonedCartController::class, 'storeFollowUp'])
    ->name('abandoned.followup');

        Route::get('/abandoned-carts', [AbandonedCartController::class, 'index'])->name('abandoned.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
