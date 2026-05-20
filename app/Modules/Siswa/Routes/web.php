<?php

use Illuminate\Support\Facades\Route;
use Modules\Siswa\Controllers\SiswaController;

/*
|--------------------------------------------------------------------------
| Siswa Module - Web Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the ModuleServiceProvider and automatically
| prefixed with 'siswa'. All routes here will be accessible at /siswa/*.
|
| Example: Route::get('/') maps to /siswa/
|
*/

use Modules\Siswa\Controllers\SiswaDashboardController;
use Modules\Siswa\Controllers\SiswaAsramaController;

Route::middleware(['auth', 'role:SISWA'])->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    
    // Halaman Asrama Khusus Siswa
    Route::prefix('asrama')->name('asrama.')->group(function () {
        Route::get('/kamar', [SiswaAsramaController::class, 'kamarIndex'])->name('kamar.index');
        Route::get('/kamar/{id}', [SiswaAsramaController::class, 'kamarShow'])->name('kamar.show');
        Route::get('/penghuni', [SiswaAsramaController::class, 'penghuniIndex'])->name('penghuni.index');
        Route::get('/jadwal-piket', [SiswaAsramaController::class, 'jadwalPiket'])->name('jadwal-piket.index');
    });
});

// Resource routes for CRUD operations
Route::middleware(['auth'])->group(function () {
    Route::get('/', [SiswaController::class, 'index'])->name('index');
    Route::get('/create', [SiswaController::class, 'create'])->name('create');
    Route::post('/', [SiswaController::class, 'store'])->name('store');
    Route::get('/kalender-akademik', [\Modules\Akademik\Controllers\KalenderAkademikController::class, 'index'])->name('kalender-akademik');
    Route::get('/{id}', [SiswaController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [SiswaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SiswaController::class, 'update'])->name('update');
    Route::delete('/{id}', [SiswaController::class, 'destroy'])->name('destroy');
});