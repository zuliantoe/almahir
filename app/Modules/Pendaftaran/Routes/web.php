<?php

use Illuminate\Support\Facades\Route;
use Modules\Pendaftaran\Controllers\SeleksiController;
use Modules\Pendaftaran\Controllers\PendaftaranController;
use Modules\Pendaftaran\Controllers\TemplateSeleksiController;


Route::middleware(['web'])->group(function () {

    Route::get('/', [PendaftaranController::class, 'create'])
        ->name('create');

    Route::post('/', [PendaftaranController::class, 'store'])
        ->name('store');
    Route::get('/cek-nisn/{nisn}', function ($nisn) {

        return response()->json([
            'exists' => \Modules\Pendaftaran\Models\Pendaftaran::where('nisn', $nisn)->exists()
        ]);
    });

    Route::get('/cek-email/{email}', function ($email) {

        return response()->json([
            'exists' => \Modules\Pendaftaran\Models\Pendaftaran::where('email', $email)->exists()
        ]);
    });
});

Route::prefix('admin')->group(function () {

    // URL: pendaftaran/admin/pendaftaran
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])
        ->name('admin.pendaftaran');

    // URL: pendaftaran/admin/pendaftaran/{id}
    Route::get('/pendaftaran/{id}', [PendaftaranController::class, 'show'])
        ->name('admin.pendaftaran.show');

    Route::get('/pendaftaran/{id}/download-jadwal', [PendaftaranController::class, 'downloadJadwal'])
        ->name('admin.pendaftaran.downloadJadwal');

    Route::get(
        '/pendaftaran/{id}/jadwal',
        [SeleksiController::class, 'index']
    )->name('admin.jadwal.index');

    Route::put(
        '/pendaftaran/{id}/status',
        [PendaftaranController::class, 'updateStatus']
    )->name('admin.pendaftaran.updateStatus');

    Route::put(
        '/pendaftaran/{id}/catatan',
        [PendaftaranController::class, 'updateCatatan']
    )->name('admin.pendaftaran.updateCatatan');

    Route::post(
        '/pendaftaran/{id}/apply-template',
        [SeleksiController::class, 'applyTemplate']
    )->name('admin.jadwal.applyTemplate');

    // Templates CRUD
    Route::get('/template-seleksi', [TemplateSeleksiController::class, 'index'])->name('admin.template.index');
    Route::post('/template-seleksi', [TemplateSeleksiController::class, 'store'])->name('admin.template.store');
    Route::delete('/template-seleksi/{id}', [TemplateSeleksiController::class, 'destroy'])->name('admin.template.destroy');
    Route::post('/template-seleksi/{id}/items', [TemplateSeleksiController::class, 'storeItem'])->name('admin.template.store-item');
    Route::delete('/template-seleksi/items/{id}', [TemplateSeleksiController::class, 'destroyItem'])->name('admin.template.destroy-item');

    Route::post(
        '/pendaftaran/{id}/jadwal',
        [SeleksiController::class, 'store']
    )->name('admin.jadwal.store');
    Route::put(
        '/pendaftaran/jadwal/{id}/nilai',
        [SeleksiController::class, 'updateNilai']
    )->name('admin.jadwal.updateNilai');
});



Route::middleware(['auth'])->prefix('admin/pendaftaran')->name('pendaftaran.')->group(function () {

    Route::get('/', [PendaftaranController::class, 'index'])
        ->name('index');

    Route::get('/{id}', [PendaftaranController::class, 'show'])
        ->name('show');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/seleksi', [SeleksiController::class, 'store']);
    Route::put('/seleksi/{id}', [SeleksiController::class, 'update']);
    Route::delete('/seleksi/{id}', [SeleksiController::class, 'destroy']);
});
