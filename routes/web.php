<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevController;
use Modules\Auth\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard / Home (requires auth)
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (NO PREFIX)
|--------------------------------------------------------------------------
*/

// Guest routes (login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes (logout)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| User Manager Routes (SUPER_ADMIN only, NO PREFIX)
|--------------------------------------------------------------------------
*/
use Modules\UserManager\Controllers\UserManagerController;

Route::middleware(['auth', 'role:SUPER_ADMIN'])->group(function () {
    // Resource routes for user CRUD
    Route::resource('users', UserManagerController::class);
    
    // Custom route for toggling user status
    Route::post('users/{id}/toggle-status', [UserManagerController::class, 'toggleStatus'])
        ->name('users.toggle-status');
    
    // AJAX route for fetching linkable data
    Route::get('users/api/linkable-data', [UserManagerController::class, 'getLinkableData'])
        ->name('users.linkable-data');
});

/*
|--------------------------------------------------------------------------
| Development Routes (Debug Mode Only)
|--------------------------------------------------------------------------
*/
if (config('app.debug') && class_exists(\App\Http\Controllers\DevController::class)) {
    Route::get('/dev/ui-guide', [\App\Http\Controllers\DevController::class, 'uiGuide'])->name('dev.ui-guide');
}

