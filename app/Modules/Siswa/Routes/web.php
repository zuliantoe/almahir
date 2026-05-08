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

Route::middleware(['auth', 'role:SISWA'])->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
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