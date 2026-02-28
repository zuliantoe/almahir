<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Create a menu.php config for a specific module
 * 
 * Usage: php artisan make:module-menu {module}
 * Example: php artisan make:module-menu Kelas
 * Example: php artisan make:module-menu Kelas --header="DATA MASTER" --label="Data Kelas" --icon="fas fa-door-open" --roles="SUPER_ADMIN,GURU" --order=10
 */
class MakeModuleMenu extends Command
{
    protected $signature = 'make:module-menu 
                            {module : The module name (e.g., Kelas, Keuangan)}
                            {--header= : Sidebar section header (e.g., DATA MASTER)}
                            {--label= : Menu item label (e.g., Data Kelas)}
                            {--icon= : FontAwesome icon class (e.g., fas fa-list)}
                            {--roles= : Comma-separated roles (e.g., SUPER_ADMIN,GURU)}
                            {--order=50 : Display order (lower = higher position)}';

    protected $description = 'Create a menu.php configuration file for a module (auto-displays in sidebar)';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $modulePath = app_path("Modules/{$module}");

        // Check if module exists
        if (!is_dir($modulePath)) {
            $this->error("Module '{$module}' does not exist at {$modulePath}");
            return self::FAILURE;
        }

        $menuFile = "{$modulePath}/menu.php";

        // Check if menu.php already exists
        if (file_exists($menuFile)) {
            $this->error("Menu already exists: {$menuFile}");
            return self::FAILURE;
        }

        // Gather config via options or interactive prompts
        $header = $this->option('header') ?? $this->ask('Section header (e.g., DATA MASTER)', strtoupper(Str::headline($module)));
        $label  = $this->option('label') ?? $this->ask('Menu label', 'Data ' . Str::headline($module));
        $icon   = $this->option('icon') ?? $this->ask('Icon (FontAwesome class)', 'fas fa-list');
        $order  = (int) $this->option('order');

        $rolesOption = $this->option('roles');
        if ($rolesOption) {
            $roles = array_map('trim', explode(',', $rolesOption));
        } else {
            $rolesInput = $this->ask('Roles (comma-separated)', 'SUPER_ADMIN');
            $roles = array_map('trim', explode(',', $rolesInput));
        }

        $routePrefix = strtolower($module);
        $stub = $this->getMenuStub($header, $roles, $order, $label, $icon, $routePrefix);

        file_put_contents($menuFile, $stub);

        $this->newLine();
        $this->info("✓ Menu created: {$menuFile}");
        $this->line("  Header : {$header}");
        $this->line("  Label  : {$label}");
        $this->line("  Icon   : {$icon}");
        $this->line("  Roles  : " . implode(', ', $roles));
        $this->line("  Order  : {$order}");
        $this->line("  Route  : {$routePrefix}.index");
        $this->newLine();
        $this->info("The menu will auto-display in the sidebar for users with the specified roles.");

        return self::SUCCESS;
    }

    protected function getMenuStub(string $header, array $roles, int $order, string $label, string $icon, string $routePrefix): string
    {
        $rolesArray = "'" . implode("', '", $roles) . "'";

        return <<<PHP
<?php

/**
 * Sidebar menu configuration.
 * This file is auto-discovered by ModuleServiceProvider.
 */
return [
    'header' => '{$header}',
    'roles'  => [{$rolesArray}],
    'order'  => {$order},
    'items'  => [
        [
            'label' => '{$label}',
            'icon'  => '{$icon}',
            'route' => '{$routePrefix}.index',
            'match' => '{$routePrefix}*',
        ],
    ],
];

PHP;
    }
}
