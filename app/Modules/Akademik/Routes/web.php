<?php

// use App\Http\Controllers\TahunAjaranController as ControllersTahunAjaranController;

// use App\Http\Controllers\KelasController;


use Illuminate\Support\Facades\Route;
use Modules\Akademik\Controllers\AkademikController;
use Modules\Akademik\Controllers\JenisKegiatanController as ControllersJenisKegiatanController;
use Modules\Akademik\Controllers\KategoriPelajaranController as ControllersKategoriPelajaranController;
use Modules\Akademik\Controllers\KelasController as ControllersKelasController;
use Modules\Akademik\Controllers\MataPelajaranController;
use Modules\Akademik\Controllers\TahunAjaranController;

/*
|--------------------------------------------------------------------------
| Akademik Module Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with '/akademik' and named 'akademik.*'
| Middleware: web (auto-applied by ModuleServiceProvider)
|
*/

Route::middleware(['web','auth'])->group(function () {
    Route::get('/', [AkademikController::class, 'index'])->name('index');
    // Route::get('/create', [AkademikController::class, 'create'])->name('create');
    // Route::post('/', [AkademikController::class, 'store'])->name('store');
    // Route::get('/{id}', [AkademikController::class, 'show'])->name('show');
    // Route::get('/{id}/edit', [AkademikController::class, 'edit'])->name('edit');
    // Route::put('/{id}', [AkademikController::class, 'update'])->name('update');
    // Route::delete('/{id}', [AkademikController::class, 'destroy'])->name('destroy');

    Route::resource('tahun-ajaran', TahunAjaranController::class);
    Route::resource('kelas',ControllersKelasController::class);
    Route::resource('jenis-kegiatan',ControllersJenisKegiatanController::class);
    Route::resource('kategori-pelajaran',ControllersKategoriPelajaranController::class);
    Route::resource('mata-pelajaran',MataPelajaranController::class);
});


