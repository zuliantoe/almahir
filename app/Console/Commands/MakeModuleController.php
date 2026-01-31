<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Create a controller for a specific module
 * 
 * Usage: php artisan make:module-controller {module} {name} [--resource] [--model=]
 * Example: php artisan make:module-controller Siswa SiswaController --resource
 */
class MakeModuleController extends Command
{
    protected $signature = 'make:module-controller 
                            {module : The module name (e.g., Siswa, Guru)}
                            {name : The controller name (e.g., SiswaController)}
                            {--r|resource : Generate a resource controller}
                            {--model= : Generate a resource controller for the given model}';

    protected $description = 'Create a new controller for a module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $name = Str::studly($this->argument('name'));
        
        // Ensure name ends with Controller
        if (!Str::endsWith($name, 'Controller')) {
            $name .= 'Controller';
        }
        
        $modulePath = app_path("Modules/{$module}");
        
        // Check if module exists
        if (!is_dir($modulePath)) {
            $this->error("Module '{$module}' does not exist at {$modulePath}");
            return self::FAILURE;
        }
        
        // Create Controllers folder if not exists
        $controllersPath = "{$modulePath}/Controllers";
        if (!is_dir($controllersPath)) {
            mkdir($controllersPath, 0755, true);
            $this->info("Created directory: {$controllersPath}");
        }
        
        // Generate controller file
        $controllerFile = "{$controllersPath}/{$name}.php";
        
        if (file_exists($controllerFile)) {
            $this->error("Controller already exists: {$controllerFile}");
            return self::FAILURE;
        }
        
        $isResource = $this->option('resource') || $this->option('model');
        $modelName = $this->option('model');
        
        if ($isResource) {
            $stub = $this->getResourceControllerStub($module, $name, $modelName);
        } else {
            $stub = $this->getBasicControllerStub($module, $name);
        }
        
        file_put_contents($controllerFile, $stub);
        $this->info("Controller created: {$controllerFile}");
        
        $this->newLine();
        $this->info("✓ Controller created in module: {$module}");
        $this->line("  Namespace: Modules\\{$module}\\Controllers\\{$name}");
        
        if ($isResource) {
            $this->newLine();
            $this->line("Don't forget to add routes in app/Modules/{$module}/Routes/web.php:");
            $this->line("  Route::resource('/', {$name}::class);");
        }
        
        return self::SUCCESS;
    }
    
    protected function getBasicControllerStub(string $module, string $name): string
    {
        return <<<PHP
<?php

namespace Modules\\{$module}\\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * {$name}
 * 
 * Controller for {$module} module.
 */
class {$name} extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('{$this->getViewNamespace($module)}::index', [
            'title' => '{$module}',
        ]);
    }
}

PHP;
    }
    
    protected function getResourceControllerStub(string $module, string $name, ?string $modelName): string
    {
        $viewNs = $this->getViewNamespace($module);
        $varName = Str::camel($modelName ?? $module);
        $varPlural = Str::plural($varName);
        
        $modelImport = '';
        $modelType = 'string $id';
        
        if ($modelName) {
            $modelImport = "use Modules\\{$module}\\Models\\{$modelName};\n";
            $modelType = "{$modelName} \${$varName}";
        }
        
        return <<<PHP
<?php

namespace Modules\\{$module}\\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
{$modelImport}
/**
 * {$name}
 * 
 * CRUD operations for {$module} module.
 */
class {$name} extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request \$request): View
    {
        // TODO: Implement listing logic
        \${$varPlural} = collect(); // Replace with actual query
        
        return view('{$viewNs}::index', [
            'title' => 'Daftar {$module}',
            '{$varPlural}' => \${$varPlural},
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('{$viewNs}::create', [
            'title' => 'Tambah {$module}',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request \$request): RedirectResponse
    {
        \$validated = \$request->validate([
            // TODO: Add validation rules
        ]);

        // TODO: Create record

        return redirect()->route('{$viewNs}.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show({$modelType}): View
    {
        return view('{$viewNs}::show', [
            'title' => 'Detail {$module}',
            '{$varName}' => \${$varName},
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit({$modelType}): View
    {
        return view('{$viewNs}::edit', [
            'title' => 'Edit {$module}',
            '{$varName}' => \${$varName},
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request \$request, {$modelType}): RedirectResponse
    {
        \$validated = \$request->validate([
            // TODO: Add validation rules
        ]);

        // TODO: Update record

        return redirect()->route('{$viewNs}.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy({$modelType}): RedirectResponse
    {
        // TODO: Delete record

        return redirect()->route('{$viewNs}.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}

PHP;
    }
    
    protected function getViewNamespace(string $module): string
    {
        return strtolower($module);
    }
}
