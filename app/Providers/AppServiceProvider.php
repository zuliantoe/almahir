<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Services\HrAlertService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Daftarkan HrAlertService sebagai Singleton agar hanya dihitung 1x per request
        $this->app->singleton(HrAlertService::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFour();

        // 🔥 Sync Pegawai to Guru
        \Modules\PegawaiManager\Models\Pegawai::observe(\Modules\PegawaiManager\Observers\PegawaiObserver::class);

        // 🔔 Share HR Alerts ke SEMUA view secara global (hanya jika user sudah login & adalah admin)
        View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->hasRole(['SUPER_ADMIN', 'STAF_TU'])) {
                try {
                    $hrAlerts = app(HrAlertService::class)->getAlerts();
                } catch (\Exception $e) {
                    $hrAlerts = collect();
                }
                $view->with('hrAlerts', $hrAlerts);
            } else {
                $view->with('hrAlerts', collect());
            }
        });
    }
}
