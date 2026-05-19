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

    // Penilaian Akademik Routes
    Route::prefix('penilaianakademik')->name('penilaianakademik.')->group(function () {
        Route::get('/', [PenilaianAkademikController::class, 'index'])->name('index');
        Route::get('/create', [PenilaianAkademikController::class, 'create'])->name('create');
        Route::get('/history', [PenilaianAkademikController::class, 'history'])->name('history');
        Route::get('/get-siswa-by-rombel/{rombelId}', [PenilaianAkademikController::class, 'getSiswaByRombel'])->name('get-siswa-by-rombel');
        Route::get('/get-kkm/{rombelId}/{mapelId}', [PenilaianAkademikController::class, 'getKkm'])->name('get-kkm');
        Route::get('/get-data-by-guru/{guruId}', [PenilaianAkademikController::class, 'getDataByGuru'])->name('get-data-by-guru');
        Route::post('/', [PenilaianAkademikController::class, 'store'])->name('store');
        
        // Export & Raport Routes
        Route::get('/export-excel', [PenilaianAkademikController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [PenilaianAkademikController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/raport', [PenilaianAkademikController::class, 'raportIndex'])->name('raport.index');
        Route::get('/raport/{id}', [PenilaianAkademikController::class, 'raportShow'])->name('raport.show');
        Route::post('/raport/save-catatan', [PenilaianAkademikController::class, 'saveCatatan'])->name('raport.save-catatan');

        Route::get('/{id}', [PenilaianAkademikController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PenilaianAkademikController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PenilaianAkademikController::class, 'update'])->name('update');
        Route::delete('/{id}', [PenilaianAkademikController::class, 'destroy'])->name('destroy');
    });

    // Presensi Siswa Routes (khusus siswa)
    Route::get('presensi/siswa', [PresensiController::class, 'siswaIndex'])->name('presensi.siswa.index');
    Route::post('presensi/siswa', [PresensiController::class, 'siswaStore'])->name('presensi.siswa.store');

    // Izin Sakit Siswa Routes (khusus siswa)
    Route::get('izinsakit/siswa', [IzinSakitController::class, 'siswaIndex'])->name('izinsakit.siswa.index');
    Route::get('izinsakit/siswa/create', [IzinSakitController::class, 'siswaCreate'])->name('izinsakit.siswa.create');
    Route::post('izinsakit/siswa', [IzinSakitController::class, 'siswaStore'])->name('izinsakit.siswa.store');
    Route::get('izinsakit/siswa/{id}/edit', [IzinSakitController::class, 'siswaEdit'])->name('izinsakit.siswa.edit');
    Route::put('izinsakit/siswa/{id}', [IzinSakitController::class, 'siswaUpdate'])->name('izinsakit.siswa.update');
    Route::delete('izinsakit/siswa/{id}', [IzinSakitController::class, 'siswaDestroy'])->name('izinsakit.siswa.destroy');

    // Presensi Routes
    Route::prefix('presensi')->name('presensi.')->group(function () {
        Route::get('/', [PresensiController::class, 'index'])->name('index');
        Route::post('/scanning', [PresensiController::class, 'scanningStore'])->name('scanning.store');
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
        Route::get('/get-siswa-by-rombel/{rombelId}', [PenilaianTahfidzController::class, 'getSiswaByRombel'])->name('get-siswa-by-rombel');
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
        Route::patch('/{id}/confirm', [IzinSakitController::class, 'confirm'])->name('confirm');
        Route::delete('/{id}', [IzinSakitController::class, 'destroy'])->name('destroy');
    });
});
