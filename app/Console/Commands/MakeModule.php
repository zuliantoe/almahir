<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Create a new module with complete folder structure and boilerplate files
 * 
 * Usage: php artisan make:module {name} [--model] [--migration]
 * Example: php artisan make:module Kelas --model --migration
 */
class MakeModule extends Command
{
    protected $signature = 'make:module 
                            {name : The module name (e.g., Kelas, Jadwal)}
                            {--m|model : Create a model for the module}
                            {--g|migration : Create a migration for the module}
                            {--a|all : Create model, migration, and resource controller}';

    protected $description = 'Create a new module with complete folder structure';

    protected string $moduleName;
    protected string $modulePath;

    public function handle(): int
    {
        $this->moduleName = Str::studly($this->argument('name'));
        $this->modulePath = app_path("Modules/{$this->moduleName}");
        
        // Check if module already exists
        if (is_dir($this->modulePath)) {
            $this->error("Module '{$this->moduleName}' already exists at {$this->modulePath}");
            return self::FAILURE;
        }
        
        $this->info("Creating module: {$this->moduleName}");
        $this->newLine();
        
        // Create folder structure
        $this->createFolderStructure();
        
        // Create boilerplate files
        $this->createRouteFile();
        $this->createController();
        $this->createIndexView();
        
        // Optional: Create model and migration
        $createAll = $this->option('all');
        
        if ($createAll || $this->option('model')) {
            $this->createModel();
        }
        
        if ($createAll || $this->option('migration')) {
            $this->createMigration();
        }
        
        $this->newLine();
        $this->info("✓ Module '{$this->moduleName}' created successfully!");
        $this->newLine();
        
        // Display next steps
        $this->displayNextSteps();
        
        return self::SUCCESS;
    }
    
    protected function createFolderStructure(): void
    {
        $folders = ['Controllers', 'Models', 'Migrations', 'Routes', 'Views'];
        
        foreach ($folders as $folder) {
            $path = "{$this->modulePath}/{$folder}";
            File::makeDirectory($path, 0755, true);
            $this->line("  <info>Created:</info> {$folder}/");
        }
    }
    
    protected function createRouteFile(): void
    {
        $lowerName = strtolower($this->moduleName);
        $content = <<<PHP
<?php

use Illuminate\Support\Facades\Route;
use Modules\\{$this->moduleName}\\Controllers\\{$this->moduleName}Controller;

/*
|--------------------------------------------------------------------------
| {$this->moduleName} Module Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with '/{$lowerName}' and named '{$lowerName}.*'
| Middleware: web (auto-applied by ModuleServiceProvider)
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [{$this->moduleName}Controller::class, 'index'])->name('index');
    Route::get('/create', [{$this->moduleName}Controller::class, 'create'])->name('create');
    Route::post('/', [{$this->moduleName}Controller::class, 'store'])->name('store');
    Route::get('/{id}', [{$this->moduleName}Controller::class, 'show'])->name('show');
    Route::get('/{id}/edit', [{$this->moduleName}Controller::class, 'edit'])->name('edit');
    Route::put('/{id}', [{$this->moduleName}Controller::class, 'update'])->name('update');
    Route::delete('/{id}', [{$this->moduleName}Controller::class, 'destroy'])->name('destroy');
});

PHP;
        
        File::put("{$this->modulePath}/Routes/web.php", $content);
        $this->line("  <info>Created:</info> Routes/web.php");
    }
    
    protected function createController(): void
    {
        $lowerName = strtolower($this->moduleName);
        $varName = Str::camel($this->moduleName);
        $varPlural = Str::plural($varName);
        
        $content = <<<PHP
<?php

namespace Modules\\{$this->moduleName}\\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * {$this->moduleName}Controller
 * 
 * CRUD operations for {$this->moduleName} module.
 */
class {$this->moduleName}Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request \$request): View
    {
        // TODO: Implement listing logic
        \${$varPlural} = collect();
        
        return view('{$lowerName}::index', [
            'title' => 'Daftar {$this->moduleName}',
            '{$varPlural}' => \${$varPlural},
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('{$lowerName}::create', [
            'title' => 'Tambah {$this->moduleName}',
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

        return redirect()->route('{$lowerName}.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string \$id): View
    {
        // TODO: Find record
        \${$varName} = null;
        
        return view('{$lowerName}::show', [
            'title' => 'Detail {$this->moduleName}',
            '{$varName}' => \${$varName},
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string \$id): View
    {
        // TODO: Find record
        \${$varName} = null;
        
        return view('{$lowerName}::edit', [
            'title' => 'Edit {$this->moduleName}',
            '{$varName}' => \${$varName},
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request \$request, string \$id): RedirectResponse
    {
        \$validated = \$request->validate([
            // TODO: Add validation rules
        ]);

        // TODO: Update record

        return redirect()->route('{$lowerName}.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string \$id): RedirectResponse
    {
        // TODO: Delete record

        return redirect()->route('{$lowerName}.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}

PHP;
        
        File::put("{$this->modulePath}/Controllers/{$this->moduleName}Controller.php", $content);
        $this->line("  <info>Created:</info> Controllers/{$this->moduleName}Controller.php");
    }
    
    protected function createIndexView(): void
    {
        $lowerName = strtolower($this->moduleName);
        $varPlural = Str::plural(Str::camel($this->moduleName));
        
        $content = <<<BLADE
@extends('layouts.app')

@section('title', \$title)

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <x-card title="Daftar {$this->moduleName}" icon="fas fa-list">
        <x-slot name="tools">
            <a href="{{ route('{$lowerName}.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah {$this->moduleName}
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(\${$varPlural} as \$index => \$item)
                    <tr>
                        <td>{{ \$index + 1 }}</td>
                        <td>{{ \$item->nama ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('{$lowerName}.edit', \$item->id) }}" 
                                   class="btn btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('{$lowerName}.destroy', \$item->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            Belum ada data. <a href="{{ route('{$lowerName}.create') }}">Tambah data pertama</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection

BLADE;
        
        File::put("{$this->modulePath}/Views/index.blade.php", $content);
        $this->line("  <info>Created:</info> Views/index.blade.php");
        
        // Create stub views
        $this->createStubView('create', 'Tambah');
        $this->createStubView('edit', 'Edit');
        $this->createStubView('show', 'Detail');
    }
    
    protected function createStubView(string $name, string $prefix): void
    {
        $lowerName = strtolower($this->moduleName);
        
        $content = <<<BLADE
@extends('layouts.app')

@section('title', \$title)

@section('content')
<div class="container-fluid">
    <x-card title="{$prefix} {$this->moduleName}" icon="fas fa-edit">
        {{-- TODO: Add form content --}}
        <p class="text-muted">Form content here...</p>
        
        <div class="mt-4">
            <a href="{{ route('{$lowerName}.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </x-card>
</div>
@endsection

BLADE;
        
        File::put("{$this->modulePath}/Views/{$name}.blade.php", $content);
        $this->line("  <info>Created:</info> Views/{$name}.blade.php");
    }
    
    protected function createModel(): void
    {
        $tableName = Str::snake(Str::plural($this->moduleName));
        
        $content = <<<PHP
<?php

namespace Modules\\{$this->moduleName}\\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * {$this->moduleName} Model
 * 
 * @property string \$id UUID primary key
 */
class {$this->moduleName} extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected \$table = '{$tableName}';

    /**
     * The attributes that are mass assignable.
     */
    protected \$fillable = [
        'nama',
        // TODO: Add your fillable fields here
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
        
        File::put("{$this->modulePath}/Models/{$this->moduleName}.php", $content);
        $this->line("  <info>Created:</info> Models/{$this->moduleName}.php");
    }
    
    protected function createMigration(): void
    {
        $tableName = Str::snake(Str::plural($this->moduleName));
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_create_{$tableName}_table.php";
        
        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->string('nama');
            // TODO: Add your columns here
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};

PHP;
        
        File::put("{$this->modulePath}/Migrations/{$filename}", $content);
        $this->line("  <info>Created:</info> Migrations/{$filename}");
    }
    
    protected function displayNextSteps(): void
    {
        $lowerName = strtolower($this->moduleName);
        
        $this->line('<comment>Next steps:</comment>');
        $this->line("  1. Add menu to sidebar: resources/views/layouts/partials/sidebar.blade.php");
        $this->newLine();
        $this->line("     <fg=cyan><li class=\"nav-item\"></>");
        $this->line("         <fg=cyan><a href=\"{{ route('{$lowerName}.index') }}\" class=\"nav-link {{ request()->is('{$lowerName}*') ? 'active' : '' }}\"></>");
        $this->line("             <fg=cyan><i class=\"nav-icon fas fa-list\"></i></>");
        $this->line("             <fg=cyan><p>{$this->moduleName}</p></>");
        $this->line("         <fg=cyan></a></>");
        $this->line("     <fg=cyan></li></>");
        $this->newLine();
        $this->line("  2. Run migration: <fg=yellow>php artisan migrate</>");
        $this->line("  3. Clear cache: <fg=yellow>php artisan optimize:clear</>");
        $this->line("  4. Visit: <fg=yellow>http://localhost:8000/{$lowerName}</>");
    }
}
