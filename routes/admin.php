<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\FormFieldController;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // daftar form
        Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
        Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
        Route::post('/forms', [FormController::class, 'store'])->name('forms.store');

        // HALAMAN DESIGN (pengaturan tampilan)
        Route::get('/forms/{form}/design', [FormController::class, 'design'])->name('forms.design');
        Route::post('/forms/{form}/design', [FormController::class, 'updateDesign'])->name('forms.design.update');

        // PREVIEW
        Route::get('/forms/{form}/preview', [FormController::class, 'preview'])->name('forms.preview');

        // KELOLA FIELD
        Route::get('/forms/{form}/fields', [FormFieldController::class, 'edit'])->name('forms.fields.edit');
        Route::post('/forms/{form}/fields', [FormFieldController::class, 'store'])->name('forms.fields.store');
        Route::delete('/forms/{form}/fields/{field}', [FormFieldController::class, 'destroy'])->name('forms.fields.destroy');
    });
