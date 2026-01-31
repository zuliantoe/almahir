<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Create a model for a specific module
 * 
 * Usage: php artisan make:module-model {module} {name} [-m|--migration]
 * Example: php artisan make:module-model Siswa Siswa -m
 */
class MakeModuleModel extends Command
{
    protected $signature = 'make:module-model 
                            {module : The module name (e.g., Siswa, Guru)}
                            {name : The model name (e.g., Siswa)}
                            {--m|migration : Create a migration for the model}
                            {--table= : Custom table name}';

    protected $description = 'Create a new model for a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));
        
        $modulePath = app_path("Modules/{$module}");
        
        // Check if module exists
        if (!is_dir($modulePath)) {
            $this->error("Module '{$module}' does not exist at {$modulePath}");
            return self::FAILURE;
        }
        
        // Create Models folder if not exists
        $modelsPath = "{$modulePath}/Models";
        if (!is_dir($modelsPath)) {
            mkdir($modelsPath, 0755, true);
            $this->info("Created directory: {$modelsPath}");
        }
        
        // Generate model file
        $modelFile = "{$modelsPath}/{$name}.php";
        
        if (file_exists($modelFile)) {
            $this->error("Model already exists: {$modelFile}");
            return self::FAILURE;
        }
        
        $tableName = $this->option('table') ?? Str::snake(Str::pluralStudly($name));
        $stub = $this->getModelStub($module, $name, $tableName);
        
        file_put_contents($modelFile, $stub);
        $this->info("Model created: {$modelFile}");
        
        // Create migration if requested
        if ($this->option('migration')) {
            $migrationName = 'create_' . Str::snake($name) . '_table';
            $this->call('make:module-migration', [
                'module' => $module,
                'name' => $migrationName,
                '--create' => $tableName,
            ]);
        }
        
        $this->newLine();
        $this->info("✓ Model created in module: {$module}");
        $this->line("  Namespace: Modules\\{$module}\\Models\\{$name}");
        
        return self::SUCCESS;
    }
    
    protected function getModelStub(string $module, string $name, string $table): string
    {
        return <<<PHP
<?php

namespace Modules\\{$module}\\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * {$name} Model
 * 
 * @property string \$id UUID primary key
 */
class {$name} extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected \$table = '{$table}';

    /**
     * The attributes that are mass assignable.
     */
    protected \$fillable = [
        // Add your fillable fields here
    ];

    /**
     * The attributes that should be cast.
     */
    protected \$casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

PHP;
    }
}
