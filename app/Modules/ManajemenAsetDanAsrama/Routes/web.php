<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenAsetDanAsrama\Controllers\ManajemenAsetDanAsramaController;

/*
|--------------------------------------------------------------------------
| ManajemenAsetDanAsrama Module Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with '/manajemenasetdanasrama' and named 'manajemenasetdanasrama.*'
| Middleware: web (auto-applied by ModuleServiceProvider)
|
*/

Route::middleware(['auth'])->group(function () {
    // Dashboard / Index
    Route::get('/', [ManajemenAsetDanAsramaController::class, 'index'])->name('index');
    
    // Resource routes untuk Pengajuan Aset
    Route::get('/pengajuan', [ManajemenAsetDanAsramaController::class, 'pengajuanIndex'])->name('pengajuan.index');
    Route::get('/pengajuan/create', [ManajemenAsetDanAsramaController::class, 'pengajuanCreate'])->name('pengajuan.create');
    Route::post('/pengajuan', [ManajemenAsetDanAsramaController::class, 'pengajuanStore'])->name('pengajuan.store');
    Route::get('/pengajuan/{id}', [ManajemenAsetDanAsramaController::class, 'pengajuanShow'])->name('pengajuan.show');
    Route::get('/pengajuan/{id}/edit', [ManajemenAsetDanAsramaController::class, 'pengajuanEdit'])->name('pengajuan.edit');
    Route::put('/pengajuan/{id}', [ManajemenAsetDanAsramaController::class, 'pengajuanUpdate'])->name('pengajuan.update');
    Route::delete('/pengajuan/{id}', [ManajemenAsetDanAsramaController::class, 'pengajuanDestroy'])->name('pengajuan.destroy');
    
    // Routes untuk Persetujuan
    Route::get('/persetujuan', [ManajemenAsetDanAsramaController::class, 'persetujuanIndex'])->name('persetujuan.index');
    Route::post('/persetujuan/{id}/approve', [ManajemenAsetDanAsramaController::class, 'persetujuanApprove'])->name('persetujuan.approve');
    Route::post('/persetujuan/{id}/reject', [ManajemenAsetDanAsramaController::class, 'persetujuanReject'])->name('persetujuan.reject');
    
    // Routes untuk Pengadaan
    Route::get('/pengadaan', [ManajemenAsetDanAsramaController::class, 'pengadaanIndex'])->name('pengadaan.index');
    Route::get('/pengadaan/{id}/proses', [ManajemenAsetDanAsramaController::class, 'pengadaanProses'])->name('pengadaan.proses');
    Route::post('/pengadaan/store', [ManajemenAsetDanAsramaController::class, 'pengadaanStore'])->name('pengadaan.store');
    Route::post('/pengadaan/{id}/selesai', [ManajemenAsetDanAsramaController::class, 'pengadaanSelesai'])->name('pengadaan.selesai');
    
    // Routes untuk Master Aset
    Route::get('/aset', [ManajemenAsetDanAsramaController::class, 'asetIndex'])->name('aset.index');
    Route::get('/aset/{id}', [ManajemenAsetDanAsramaController::class, 'asetShow'])->name('aset.show');
    Route::get('/aset/{id}/edit', [ManajemenAsetDanAsramaController::class, 'asetEdit'])->name('aset.edit');
    Route::put('/aset/{id}', [ManajemenAsetDanAsramaController::class, 'asetUpdate'])->name('aset.update');
    Route::delete('/aset/{id}', [ManajemenAsetDanAsramaController::class, 'asetDestroy'])->name('aset.destroy');
    
    // Routes untuk Kamar
    Route::get('/kamar', [ManajemenAsetDanAsramaController::class, 'kamarIndex'])->name('kamar.index');
    Route::get('/kamar/create', [ManajemenAsetDanAsramaController::class, 'kamarCreate'])->name('kamar.create');
    Route::post('/kamar', [ManajemenAsetDanAsramaController::class, 'kamarStore'])->name('kamar.store');
    Route::get('/kamar/{id}/edit', [ManajemenAsetDanAsramaController::class, 'kamarEdit'])->name('kamar.edit');
    Route::put('/kamar/{id}', [ManajemenAsetDanAsramaController::class, 'kamarUpdate'])->name('kamar.update');
    Route::delete('/kamar/{id}', [ManajemenAsetDanAsramaController::class, 'kamarDestroy'])->name('kamar.destroy');
    
    // Routes untuk Penghuni Kamar
    Route::get('/penghuni', [ManajemenAsetDanAsramaController::class, 'penghuniIndex'])->name('penghuni.index');
    Route::get('/penghuni/create', [ManajemenAsetDanAsramaController::class, 'penghuniCreate'])->name('penghuni.create');
    Route::post('/penghuni', [ManajemenAsetDanAsramaController::class, 'penghuniStore'])->name('penghuni.store');
    Route::get('/penghuni/{id}/edit', [ManajemenAsetDanAsramaController::class, 'penghuniEdit'])->name('penghuni.edit');
    Route::put('/penghuni/{id}', [ManajemenAsetDanAsramaController::class, 'penghuniUpdate'])->name('penghuni.update');
    Route::delete('/penghuni/{id}', [ManajemenAsetDanAsramaController::class, 'penghuniDestroy'])->name('penghuni.destroy');
    
    // Routes untuk Jadwal Piket
    Route::get('/jadwal-piket', [ManajemenAsetDanAsramaController::class, 'jadwalPiketIndex'])->name('jadwal-piket.index');
    Route::get('/jadwal-piket/create', [ManajemenAsetDanAsramaController::class, 'jadwalPiketCreate'])->name('jadwal-piket.create');
    Route::post('/jadwal-piket', [ManajemenAsetDanAsramaController::class, 'jadwalPiketStore'])->name('jadwal-piket.store');
    Route::get('/jadwal-piket/{id}/edit', [ManajemenAsetDanAsramaController::class, 'jadwalPiketEdit'])->name('jadwal-piket.edit');
    Route::put('/jadwal-piket/{id}', [ManajemenAsetDanAsramaController::class, 'jadwalPiketUpdate'])->name('jadwal-piket.update');
    Route::delete('/jadwal-piket/{id}', [ManajemenAsetDanAsramaController::class, 'jadwalPiketDestroy'])->name('jadwal-piket.destroy');
    Route::post('/jadwal-piket/{id}/selesai', [ManajemenAsetDanAsramaController::class, 'jadwalPiketSelesai'])->name('jadwal-piket.selesai');
    
    // Routes untuk Kerusakan
    Route::get('/kerusakan', [ManajemenAsetDanAsramaController::class, 'kerusakanIndex'])->name('kerusakan.index');
    Route::get('/kerusakan/create', [ManajemenAsetDanAsramaController::class, 'kerusakanCreate'])->name('kerusakan.create');
    Route::post('/kerusakan', [ManajemenAsetDanAsramaController::class, 'kerusakanStore'])->name('kerusakan.store');
    Route::get('/kerusakan/{id}/edit', [ManajemenAsetDanAsramaController::class, 'kerusakanEdit'])->name('kerusakan.edit');
    Route::put('/kerusakan/{id}', [ManajemenAsetDanAsramaController::class, 'kerusakanUpdate'])->name('kerusakan.update');
    Route::delete('/kerusakan/{id}', [ManajemenAsetDanAsramaController::class, 'kerusakanDestroy'])->name('kerusakan.destroy');
    
    // Routes untuk Pemeliharaan
    Route::get('/pemeliharaan', [ManajemenAsetDanAsramaController::class, 'pemeliharaanIndex'])->name('pemeliharaan.index');
    Route::get('/pemeliharaan/create', [ManajemenAsetDanAsramaController::class, 'pemeliharaanCreate'])->name('pemeliharaan.create');
    Route::post('/pemeliharaan', [ManajemenAsetDanAsramaController::class, 'pemeliharaanStore'])->name('pemeliharaan.store');
    Route::get('/pemeliharaan/{id}/edit', [ManajemenAsetDanAsramaController::class, 'pemeliharaanEdit'])->name('pemeliharaan.edit');
    Route::put('/pemeliharaan/{id}', [ManajemenAsetDanAsramaController::class, 'pemeliharaanUpdate'])->name('pemeliharaan.update');
    Route::delete('/pemeliharaan/{id}', [ManajemenAsetDanAsramaController::class, 'pemeliharaanDestroy'])->name('pemeliharaan.destroy');
    
    // Routes untuk Trash (Soft Delete)
    Route::get('/trash', [ManajemenAsetDanAsramaController::class, 'trashIndex'])->name('trash.index');
    Route::post('/trash/{type}/{id}/restore', [ManajemenAsetDanAsramaController::class, 'trashRestore'])->name('trash.restore');
    Route::delete('/trash/{type}/{id}/force-delete', [ManajemenAsetDanAsramaController::class, 'trashForceDelete'])->name('trash.force-delete');
});