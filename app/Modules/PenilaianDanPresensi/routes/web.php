<?php

use Illuminate\Support\Facades\Route;
use Modules\PenilaianDanPresensi\Controllers\DashboardController;
use Modules\PenilaianDanPresensi\Controllers\PenilaianAkademikController;
use Modules\PenilaianDanPresensi\Controllers\PresensiController;
use Modules\PenilaianDanPresensi\Controllers\PenilaianTahfidzController;
use Modules\PenilaianDanPresensi\Controllers\IzinSakitController;

/*
|--------------------------------------------------------------------------
| PenilaianDanPresensi Module Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with '/penilaiandanpresensi' and named 'penilaiandanpresensi.*'
| Middleware: web (auto-applied by ModuleServiceProvider)
|
*/

Route::middleware(['auth'])->group(function () {
    // Dashboard Routes
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard-penilaian-akademik', [DashboardController::class, 'dashboardPenilaianAkademik'])->name('dashboard-penilaian-akademik');
    Route::get('/dashboard-penilaian-tahfidz', [DashboardController::class, 'dashboardPenilaianTahfidz'])->name('dashboard-penilaian-tahfidz');
    Route::get('/dashboard-presensi', [DashboardController::class, 'dashboardPresensi'])->name('dashboard-presensi');
    Route::get('/dashboard-izin-sakit', [DashboardController::class, 'dashboardIzinSakit'])->name('dashboard-izin-sakit');

    // Penilaian Akademik Routes
    Route::prefix('penilaianakademik')->name('penilaianakademik.')->group(function () {
        Route::get('/', [PenilaianAkademikController::class, 'index'])->name('index');
        Route::get('/create', [PenilaianAkademikController::class, 'create'])->name('create');
        Route::post('/', [PenilaianAkademikController::class, 'store'])->name('store');
        Route::get('/{id}', [PenilaianAkademikController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PenilaianAkademikController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PenilaianAkademikController::class, 'update'])->name('update');
        Route::delete('/{id}', [PenilaianAkademikController::class, 'destroy'])->name('destroy');
    });

    // Presensi Routes
    Route::prefix('presensi')->name('presensi.')->group(function () {
        Route::get('/', [PresensiController::class, 'index'])->name('index');
        Route::get('/create', [PresensiController::class, 'create'])->name('create');
        Route::post('/', [PresensiController::class, 'store'])->name('store');
        Route::post('/scan-card', [PresensiController::class, 'scanCard'])->name('scan-card');
        Route::get('/{id}', [PresensiController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PresensiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PresensiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PresensiController::class, 'destroy'])->name('destroy');
    });

    // Penilaian Tahfidz Routes
    Route::prefix('penilaiantahfidz')->name('penilaiantahfidz.')->group(function () {
        Route::get('/', [PenilaianTahfidzController::class, 'index'])->name('index');
        Route::get('/create', [PenilaianTahfidzController::class, 'create'])->name('create');
        Route::post('/', [PenilaianTahfidzController::class, 'store'])->name('store');
        Route::get('/{id}', [PenilaianTahfidzController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PenilaianTahfidzController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PenilaianTahfidzController::class, 'update'])->name('update');
        Route::delete('/{id}', [PenilaianTahfidzController::class, 'destroy'])->name('destroy');
    });

    // Izin Sakit Routes
    Route::prefix('izinsakit')->name('izinsakit.')->group(function () {
        Route::get('/', [IzinSakitController::class, 'index'])->name('index');
        Route::get('/create', [IzinSakitController::class, 'create'])->name('create');
        Route::post('/', [IzinSakitController::class, 'store'])->name('store');
        Route::get('/{id}', [IzinSakitController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [IzinSakitController::class, 'edit'])->name('edit');
        Route::put('/{id}', [IzinSakitController::class, 'update'])->name('update');
        Route::delete('/{id}', [IzinSakitController::class, 'destroy'])->name('destroy');
    });
});
