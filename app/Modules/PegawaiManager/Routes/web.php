<?php

use Illuminate\Support\Facades\Route;
use Modules\PegawaiManager\Controllers\PegawaiManagerController;
use Modules\PegawaiManager\Controllers\TypePegawaiController;

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
    // Dashboard Pegawai
    Route::get('/dashboard', [\Modules\PegawaiManager\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Type Pegawai CRUD
    // Diletakkan SEBELUM Pegawai CRUD agar kata 'types' tidak ditangkap oleh parameter {id}
    Route::resource('types', TypePegawaiController::class)->names('types');

    // Export Laporan Pegawai
    Route::get('/export', [PegawaiManagerController::class, 'export'])->name('export');

    // Pegawai CRUD
    Route::get('/', [PegawaiManagerController::class, 'index'])->name('index');
    Route::get('/create', [PegawaiManagerController::class, 'create'])->name('create');
    Route::post('/', [PegawaiManagerController::class, 'store'])->name('store');
    Route::get('/{id}', [PegawaiManagerController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PegawaiManagerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PegawaiManagerController::class, 'update'])->name('update');
    Route::delete('/{id}', [PegawaiManagerController::class, 'destroy'])->name('destroy');
});
