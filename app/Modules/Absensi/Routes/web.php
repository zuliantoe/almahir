<?php

use Illuminate\Support\Facades\Route;
use Modules\Absensi\Controllers\AbsensiController;

/*
|--------------------------------------------------------------------------
| Absensi Module Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Pegawai Routes
    Route::get('/', [AbsensiController::class, 'index'])->name('index');
    Route::get('/scan', [AbsensiController::class, 'create'])->name('create');
    Route::post('/store', [AbsensiController::class, 'store'])->name('store');
    Route::post('/update', [AbsensiController::class, 'update'])->name('update');

    // Admin/Manage Routes (Only accessible by SUPER_ADMIN)
    Route::middleware(['role:SUPER_ADMIN'])->group(function () {
        Route::get('/manage', [\Modules\Absensi\Controllers\ManageAbsensiController::class, 'index'])->name('manage.index');
        Route::get('/manage/export', [\Modules\Absensi\Controllers\ManageAbsensiController::class, 'export'])->name('manage.export');
        Route::get('/manage/qr-generator', [\Modules\Absensi\Controllers\ManageAbsensiController::class, 'qrGenerator'])->name('manage.qr-generator');
    });
});
