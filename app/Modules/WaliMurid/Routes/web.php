<?php

use Illuminate\Support\Facades\Route;
use Modules\WaliMurid\Controllers\WaliMuridController;
use Modules\WaliMurid\Controllers\PortalController;

Route::middleware('auth')->group(function () {
    // Admin Routes
    Route::resource('admin', WaliMuridController::class)->names([
        'index' => 'index',
        'create' => 'create',
        'store' => 'store',
        'edit' => 'edit',
        'update' => 'update',
        'destroy' => 'destroy',
        'show' => 'show',
    ])->parameters(['admin' => 'walimurid']);

    // Portal Wali Murid Routes
    Route::group(['prefix' => 'portal'], function() {
        Route::get('/dashboard', [PortalController::class, 'dashboard'])->name('portal.dashboard');
        Route::get('/siswa/{id}', [PortalController::class, 'siswaDetail'])->name('portal.siswa-detail');
        Route::get('/siswa/{id}/jadwal', [PortalController::class, 'siswaJadwal'])->name('portal.siswa-jadwal');
        Route::get('/siswa/{id}/pembayaran', [PortalController::class, 'siswaPembayaran'])->name('portal.siswa-pembayaran');
    });
});
