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
    Route::post('/store-self-manual', [AbsensiController::class, 'storeSelfManual'])->name('store-self-manual');
    Route::post('/update-self-manual', [AbsensiController::class, 'updateSelfManual'])->name('update-self-manual');

    // Admin/Manage Routes (Only accessible by SUPER_ADMIN)
    Route::middleware(['role:SUPER_ADMIN'])->group(function () {
        Route::resource('hari-libur', \Modules\Absensi\Controllers\HariLiburController::class)->except(['create', 'show', 'edit']);
        Route::get('/manage', [\Modules\Absensi\Controllers\ManageAbsensiController::class, 'index'])->name('manage.index');
        Route::post('/manage/store-manual', [\Modules\Absensi\Controllers\ManageAbsensiController::class, 'storeManual'])->name('manage.store-manual');
        Route::get('/manage/export', [\Modules\Absensi\Controllers\ManageAbsensiController::class, 'export'])->name('manage.export');
        Route::get('/manage/qr-generator', [\Modules\Absensi\Controllers\ManageAbsensiController::class, 'qrGenerator'])->name('manage.qr-generator');
        Route::post('/manage/scan-card', [\Modules\Absensi\Controllers\ManageAbsensiController::class, 'scanCard'])->name('manage.scan-card');
    });
});
