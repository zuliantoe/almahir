<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevController;
use Modules\Auth\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\DashboardController;

// Dashboard (requires auth)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Public Root Route (landing page for guests, redirects to dashboard for authenticated users)
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
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

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Notifications
    Route::get('/notifications/read-all', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    })->name('notifications.readAll');

    Route::get('/notifications/{id}/read', function($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? url('/'));
    })->name('notifications.read');

    // User Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
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

    // ─── Database Backup ───────────────────────────────────────────
    Route::get('/system/backup-database', [\App\Http\Controllers\BackupController::class, 'download'])
        ->name('system.backup');
});

/*
|--------------------------------------------------------------------------
| Development Routes (Debug Mode Only)
|--------------------------------------------------------------------------
*/
if (config('app.debug') && class_exists(\App\Http\Controllers\DevController::class)) {
    Route::get('/dev/ui-guide', [\App\Http\Controllers\DevController::class, 'uiGuide'])->name('dev.ui-guide');
}

/*
|--------------------------------------------------------------------------
| Presensi Routes (SHORT PREFIX)
|--------------------------------------------------------------------------
*/
use Modules\PenilaianDanPresensi\Controllers\PresensiController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('presensi')->name('presensi.')->group(function () {
        Route::get('/', [PresensiController::class, 'index'])->name('index');
        Route::get('/create', [PresensiController::class, 'create'])->name('create');
        Route::post('/', [PresensiController::class, 'store'])->name('store');
        Route::get('/{id}', [PresensiController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PresensiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PresensiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PresensiController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Penilaian dan Presensi Dashboard (UNIFIED)
|--------------------------------------------------------------------------
*/
use Modules\PenilaianDanPresensi\Controllers\DashboardController as PenilaianDashboardController;

Route::middleware(['auth'])->group(function () {
    Route::get('/penilaian', [PenilaianDashboardController::class, 'index'])->name('penilaian.dashboard');
});

