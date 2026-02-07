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
    Route::get('/', [ManajemenAsetDanAsramaController::class, 'index'])->name('index');
    Route::get('/create', [ManajemenAsetDanAsramaController::class, 'create'])->name('create');
    Route::post('/', [ManajemenAsetDanAsramaController::class, 'store'])->name('store');
    Route::get('/{id}', [ManajemenAsetDanAsramaController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ManajemenAsetDanAsramaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ManajemenAsetDanAsramaController::class, 'update'])->name('update');
    Route::delete('/{id}', [ManajemenAsetDanAsramaController::class, 'destroy'])->name('destroy');
});
