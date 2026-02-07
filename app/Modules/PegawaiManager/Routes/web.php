<?php

use Illuminate\Support\Facades\Route;
use Modules\PegawaiManager\Controllers\PegawaiManagerController;

/*
|--------------------------------------------------------------------------
| PegawaiManager Module Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with '/pegawaimanager' and named 'pegawaimanager.*'
| Middleware: web (auto-applied by ModuleServiceProvider)
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [PegawaiManagerController::class, 'index'])->name('index');
    Route::get('/create', [PegawaiManagerController::class, 'create'])->name('create');
    Route::post('/', [PegawaiManagerController::class, 'store'])->name('store');
    Route::get('/{id}', [PegawaiManagerController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PegawaiManagerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PegawaiManagerController::class, 'update'])->name('update');
    Route::delete('/{id}', [PegawaiManagerController::class, 'destroy'])->name('destroy');
});
