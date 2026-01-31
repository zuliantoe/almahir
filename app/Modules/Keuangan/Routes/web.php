<?php

use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Controllers\KeuanganController;

/*
|--------------------------------------------------------------------------
| Keuangan Module Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with '/keuangan' and named 'keuangan.*'
| Middleware: web (auto-applied by ModuleServiceProvider)
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [KeuanganController::class, 'index'])->name('index');
    Route::get('/create', [KeuanganController::class, 'create'])->name('create');
    Route::post('/', [KeuanganController::class, 'store'])->name('store');
    Route::get('/{id}', [KeuanganController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [KeuanganController::class, 'edit'])->name('edit');
    Route::put('/{id}', [KeuanganController::class, 'update'])->name('update');
    Route::delete('/{id}', [KeuanganController::class, 'destroy'])->name('destroy');
});
