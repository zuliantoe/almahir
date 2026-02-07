<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Create a migration for a specific module
 * 
 * Usage: php artisan make:module-migration {module} {name} [--create=table] [--table=table]
 * Example: php artisan make:module-migration Siswa create_siswa_table --create=siswa
 */
class MakeModuleMigration extends Command
{
    protected $signature = 'make:module-migration 
                            {module : The module name (e.g., Siswa, Guru)}
                            {name : The migration name (e.g., create_siswa_table)}
                            {--create= : The table to be created}
                            {--table= : The table to be modified}';

    protected $description = 'Create a new migration file for a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = $this->argument('name');
        
        $modulePath = app_path("Modules/{$module}");
        
        // Check if module exists
        if (!is_dir($modulePath)) {
            $this->error("Module '{$module}' does not exist at {$modulePath}");
            $this->info("Available modules:");
            foreach (glob(app_path('Modules/*'), GLOB_ONLYDIR) as $dir) {
                $this->line("  - " . basename($dir));
            }
            return self::FAILURE;
        }
        
        // Create Migrations folder if not exists
        $migrationsPath = "{$modulePath}/Migrations";
        if (!is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0755, true);
            $this->info("Created directory: {$migrationsPath}");
        }
        
        // Build arguments for make:migration command
        $relativePath = "app/Modules/{$module}/Migrations";
        
        $arguments = [
            'name' => $name,
            '--path' => $relativePath,
        ];
        
        if ($this->option('create')) {
            $arguments['--create'] = $this->option('create');
        }
        
        if ($this->option('table')) {
            $arguments['--table'] = $this->option('table');
        }
        
        $this->call('make:migration', $arguments);
        
        $this->newLine();
        $this->info("✓ Migration created in module: {$module}");
        
        return self::SUCCESS;
    }
}
