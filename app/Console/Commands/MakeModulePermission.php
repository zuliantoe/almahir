<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Create a permissions.php config for a specific module
 * 
 * Usage: php artisan make:module-permission {module}
 * Example: php artisan make:module-permission Kelas
 * Example: php artisan make:module-permission Kelas --group="Data Master" --label="Data Kelas" --key=kelas --actions="view,create,edit,delete"
 */
class MakeModulePermission extends Command
{
    protected $signature = 'make:module-permission 
                            {module : The module name (e.g., Kelas, Keuangan)}
                            {--group= : Permission group name (e.g., Data Master)}
                            {--label= : Display label (e.g., Data Kelas)}
                            {--key= : Permission key prefix (e.g., kelas)}
                            {--actions=view,create,edit,delete : Comma-separated actions}';

    protected $description = 'Create a permissions.php configuration file for a module (auto-registered in PermissionRegistry)';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $modulePath = app_path("Modules/{$module}");

        if (!is_dir($modulePath)) {
            $this->error("Module '{$module}' does not exist at {$modulePath}");
            return self::FAILURE;
        }

        $permFile = "{$modulePath}/permissions.php";

        if (file_exists($permFile)) {
            $this->error("Permissions already exist: {$permFile}");
            return self::FAILURE;
        }

        // Gather config via options or interactive prompts
        $group   = $this->option('group') ?? $this->ask('Permission group (e.g., Data Master)', Str::headline($module));
        $label   = $this->option('label') ?? $this->ask('Display label', 'Data ' . Str::headline($module));
        $key     = $this->option('key') ?? $this->ask('Permission key prefix', Str::snake(strtolower($module)));
        $actions = array_map('trim', explode(',', $this->option('actions')));

        // Build permissions array
        $permissions = array_map(fn($a) => "{$key}.{$a}", $actions);
        $stub = $this->getStub($group, $key, $label, $permissions);

        file_put_contents($permFile, $stub);

        $this->newLine();
        $this->info("✓ Permissions created: {$permFile}");
        $this->line("  Group       : {$group}");
        $this->line("  Label       : {$label}");
        $this->line("  Key         : {$key}");
        $this->line("  Permissions : " . implode(', ', $permissions));
        $this->newLine();
        $this->info("Permissions will auto-register in PermissionRegistry and appear in Role & Permission management.");

        return self::SUCCESS;
    }

    protected function getStub(string $group, string $key, string $label, array $permissions): string
    {
        $permArray = "'" . implode("', '", $permissions) . "'";

        return <<<PHP
<?php

/**
 * Permission definitions.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => '{$group}',
    'modules' => [
        '{$key}' => [
            'label' => '{$label}',
            'permissions' => [{$permArray}],
        ],
    ],
];

PHP;
    }
}
