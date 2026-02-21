<?php

namespace App\Providers;

use App\Services\MenuRegistry;
use App\Services\PermissionRegistry;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
/**
 * ModuleServiceProvider
 *
 * This service provider automatically discovers and registers all modules
 * located in the app/Modules directory. It handles:
 * - Route registration (with module prefix)
 * - View namespace registration
 * - Migration path registration
 *
 * @author SIAKAD Development Team
 */
class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * This method is called during the registration phase of Laravel's
     * service container. We use it to register migrations from all modules.
     */
    public function register(): void
    {
        // Register MenuRegistry as singleton
        $this->app->singleton(MenuRegistry::class, function () {
            return new MenuRegistry();
        });

        $this->registerModuleMigrations();
    }

    /**
     * Bootstrap services.
     *
     * This method is called after all service providers have been registered.
     * We use it to register routes and views from all modules.
     */
    public function boot(): void
    {
        $this->registerModuleRoutes();
        $this->registerModuleViews();
        $this->registerModuleMenus();
        $this->registerModulePermissions();

        // Share module menus with all views for sidebar rendering
        View::composer('layouts.partials.sidebar', function ($view) {
            $menuRegistry = app(MenuRegistry::class);
            $view->with('moduleMenus', $menuRegistry->getMenusForUser());
        });
    }

    /**
     * Get all module directories.
     *
     * Scans the app/Modules directory and returns an array of module information.
     *
     * @return array Array of ['name' => 'ModuleName', 'path' => '/full/path/to/module']
     */
    protected function getModules(): array
    {
        $modulesPath = app_path('Modules');

        if (!File::isDirectory($modulesPath)) {
            return [];
        }

        $modules = [];
        $directories = File::directories($modulesPath);

        foreach ($directories as $directory) {
            $modules[] = [
                'name' => basename($directory),
                'path' => $directory,
            ];
        }

        return $modules;
    }

    /**
     * Register routes from all modules.
     *
     * Each module can have its own routes defined in:
     * - Routes/web.php (web routes with module prefix)
     * - Routes/api.php (API routes with api/module prefix)
     */
    protected function registerModuleRoutes(): void
    {
        foreach ($this->getModules() as $module) {
            $moduleName = $module['name'];
            $modulePath = $module['path'];

            // Register web routes
            $webRoutesPath = $modulePath . '/Routes/web.php';
            if (File::exists($webRoutesPath)) {
                Route::middleware('web')
                    ->prefix(strtolower($moduleName))
                    ->name(strtolower($moduleName) . '.')
                    ->group($webRoutesPath);
            }

            // Register API routes
            $apiRoutesPath = $modulePath . '/Routes/api.php';
            if (File::exists($apiRoutesPath)) {
                Route::middleware('api')
                    ->prefix('api/' . strtolower($moduleName))
                    ->name('api.' . strtolower($moduleName) . '.')
                    ->group($apiRoutesPath);
            }
        }
    }

    /**
     * Register views from all modules.
     *
     * Each module's views are registered with a namespace matching the module name.
     * Usage: @include('siswa::partials.header') or view('siswa::index')
     */
    protected function registerModuleViews(): void
    {
        foreach ($this->getModules() as $module) {
            $moduleName = $module['name'];
            $modulePath = $module['path'];

            $viewsPath = $modulePath . '/Views';
            if (File::isDirectory($viewsPath)) {
                $this->loadViewsFrom($viewsPath, strtolower($moduleName));
            }
        }
    }

    /**
     * Register migrations from all modules.
     *
     * This allows each module to have its own migrations that will be
     * automatically discovered and run with `php artisan migrate`.
     */
    protected function registerModuleMigrations(): void
    {
        foreach ($this->getModules() as $module) {
            $modulePath = $module['path'];

            $migrationsPath = $modulePath . '/Migrations';
            if (File::isDirectory($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }
        }
    }

    /**
     * Register menus from all modules.
     *
     * Each module can define a menu.php file in its root directory
     * that returns an array of menu configuration.
     */
    protected function registerModuleMenus(): void
    {
        $menuRegistry = app(MenuRegistry::class);

        foreach ($this->getModules() as $module) {
            $menuFile = $module['path'] . '/menu.php';

            if (File::exists($menuFile)) {
                $menuConfig = require $menuFile;

                if (is_array($menuConfig)) {
                    $menuRegistry->register($menuConfig);
                }
            }
        }
    }

    /**
     * Register permissions from all modules.
     *
     * Each module can define a permissions.php file in its root directory
     * that returns an array of permission configuration.
     */
    protected function registerModulePermissions(): void
    {
        foreach ($this->getModules() as $module) {
            $permFile = $module['path'] . '/permissions.php';

            if (File::exists($permFile)) {
                $permConfig = require $permFile;

                if (is_array($permConfig)) {
                    PermissionRegistry::register($permConfig);
                }
            }
        }
    }
}
