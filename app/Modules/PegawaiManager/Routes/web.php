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

    // Type Pegawai CRUD - Hanya yang memiliki hak edit guru yang bisa mengelola jabatan
    // Diletakkan SEBELUM Pegawai CRUD agar kata 'types' tidak ditangkap oleh parameter {id}
    Route::resource('types', TypePegawaiController::class)
        ->middleware('permission:guru.edit')
        ->names('types');

    // Calon Pegawai CRUD
    Route::resource('calon-pegawai', \Modules\PegawaiManager\Controllers\CalonPegawaiController::class)
        ->middleware('permission:guru.create')
        ->names('calon-pegawai');

    // Export Laporan Pegawai
    Route::get('/export', [PegawaiManagerController::class, 'export'])
        ->middleware('permission:guru.view')
        ->name('export');

    // Import Data Pegawai
    Route::get('/import', [PegawaiManagerController::class, 'importForm'])
        ->middleware('permission:guru.create')
        ->name('import');
    Route::post('/import', [PegawaiManagerController::class, 'processImport'])
        ->middleware('permission:guru.create')
        ->name('process_import');

    // Pegawai CRUD
    Route::get('/', [PegawaiManagerController::class, 'index'])->middleware('permission:guru.view')->name('index');
    Route::get('/create', [PegawaiManagerController::class, 'create'])->middleware('permission:guru.create')->name('create');
    Route::post('/', [PegawaiManagerController::class, 'store'])->middleware('permission:guru.create')->name('store');
    Route::get('/{id}/print-card', [PegawaiManagerController::class, 'printCard'])->middleware('permission:guru.view')->name('print-card');
    Route::get('/{id}', [PegawaiManagerController::class, 'show'])->middleware('permission:guru.view')->name('show');
    Route::get('/{id}/edit', [PegawaiManagerController::class, 'edit'])->middleware('permission:guru.edit')->name('edit');
    Route::put('/{id}', [PegawaiManagerController::class, 'update'])->middleware('permission:guru.edit')->name('update');
    Route::patch('/{id}/toggle-status', [PegawaiManagerController::class, 'toggleStatus'])
        ->middleware('role:SUPER_ADMIN,STAF_TU')
        ->name('toggle-status');
    Route::delete('/{id}', [PegawaiManagerController::class, 'destroy'])->middleware('role:SUPER_ADMIN,STAF_TU')->name('destroy');
    Route::post('/{id}/reset-password', [PegawaiManagerController::class, 'resetPassword'])->middleware('permission:guru.edit')->name('reset-password');
});
