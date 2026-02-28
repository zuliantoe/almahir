<?php

use Illuminate\Support\Facades\Route;
use Modules\Pendaftaran\Controllers\SeleksiController;
use Modules\Pendaftaran\Controllers\PendaftaranController;


Route::get('/', [PendaftaranController::class, 'create'])
    ->name('create');

Route::post('/', [PendaftaranController::class, 'store'])
    ->name('store');



Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/', [PendaftaranController::class, 'index'])
        ->name('index');

    Route::get('/{id}', [PendaftaranController::class, 'show'])
        ->name('show');

    Route::put('/{id}/status', [PendaftaranController::class, 'updateStatus'])
        ->name('updateStatus');

});


Route::middleware(['auth'])->group(function () {
    Route::post('/seleksi', [SeleksiController::class, 'store']);
    Route::put('/seleksi/{id}', [SeleksiController::class, 'update']);
    Route::delete('/seleksi/{id}', [SeleksiController::class, 'destroy']);
});
