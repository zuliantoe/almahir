# SIAKAD - Sistem Informasi Akademik

A modular monolith Academic Information System built with Laravel 12 and AdminLTE 3.

## 🚀 Quick Start

```bash
# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations and seed roles
php artisan migrate
php artisan db:seed --class=RoleSeeder

# Start development server
php artisan serve
```

Visit: http://localhost:8000

### Default Login
| Email | Password | Role |
|-------|----------|------|
| `admin@siakad.local` | `password` | Super Admin |

---

## 📁 Project Structure

```
app/
├── Http/Controllers/        # Core controllers
├── Models/                  # Core models (User, Role)
│   └── Traits/             # Model traits (HasRoles)
├── Services/               # Application services
│   ├── PermissionRegistry.php  # Centralized permission definitions
│   └── MenuRegistry.php       # Dynamic sidebar menu registry
├── Modules/                 # 📦 MODULAR MONOLITH - All modules here
│   ├── Auth/               # Authentication (login/logout)
│   ├── Siswa/              # Student data management
│   ├── Guru/               # Teacher data management
│   ├── WaliMurid/          # Parent/Guardian management
│   ├── UserManager/        # User CRUD (Super Admin only)
│   └── RolePermission/     # Role & Permission management
└── Providers/
    └── ModuleServiceProvider.php  # Auto-discovers modules

resources/views/
├── components/              # 🎨 UI COMPONENTS - Use these!
│   ├── alert.blade.php
│   ├── btn.blade.php
│   ├── card.blade.php
│   ├── input.blade.php
│   └── modal.blade.php
├── layouts/
│   ├── app.blade.php        # Main AdminLTE layout
│   └── partials/
│       ├── navbar.blade.php
│       ├── sidebar.blade.php
│       └── footer.blade.php
└── dev/
    └── ui-guide.blade.php   # Component documentation
```

---

## 📦 Available Modules

| Module | Route Prefix | Description |
|--------|--------------|-------------|
| Auth | `/login`, `/logout` | Authentication |
| Siswa | `/siswa` | Student data CRUD |
| Guru | `/guru` | Teacher data CRUD |
| WaliMurid | `/walimurid` | Parent/Guardian CRUD |
| UserManager | `/users` | User management (SUPER_ADMIN) |
| RolePermission | `/rolepermission` | Role & permission management |

---

## 🛠 Module Generator Commands

### Quick Start - Create Complete Module

```bash
# Create a new module with all files (recommended)
php artisan make:module Kelas --all

# Or step by step:
php artisan make:module Kelas           # Basic structure only
php artisan make:module Kelas --model   # With model
php artisan make:module Kelas -m -g     # With model and migration
```

This creates:
- `Controllers/{Module}Controller.php` - Full CRUD controller
- `Routes/web.php` - RESTful routes with auth middleware
- `Views/index.blade.php` - List view with table
- `Views/create.blade.php` - Create form stub
- `Views/edit.blade.php` - Edit form stub
- `Views/show.blade.php` - Detail view stub
- `Models/{Module}.php` - Model with UUID & SoftDeletes (with `-m`)
- `Migrations/create_{table}_table.php` - Migration (with `-g`)

### Individual File Commands

```bash
# Add migration to existing module
php artisan make:module-migration Siswa add_status_to_siswa --table=siswa

# Add model to existing module
php artisan make:module-model Siswa NilaiSiswa -m

# Add controller to existing module
php artisan make:module-controller Siswa ReportController --resource

# Add sidebar menu to existing module
php artisan make:module-menu Kelas --header="DATA MASTER" --label="Data Kelas" --icon="fas fa-door-open" --roles="SUPER_ADMIN,GURU" --order=10
```

| Command | Description |
|---------|-------------|
| `make:module {name} [-a\|--all]` | Create complete module with all files |
| `make:module-migration {module} {name}` | Add migration to existing module |
| `make:module-model {module} {name} [-m]` | Add model (with optional migration) |
| `make:module-controller {module} {name} [-r]` | Add controller (`-r` for resource) |
| `make:module-menu {module}` | Add sidebar menu (auto-displays in sidebar) |

---


## 📦 Creating a New Module

Follow these steps to create a new module (e.g., `Kelas`):

### 1. Create Folder Structure

```bash
mkdir -p app/Modules/Kelas/{Controllers,Models,Migrations,Routes,Views}
```

### 2. Create Routes

**File:** `app/Modules/Kelas/Routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Kelas\Controllers\KelasController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [KelasController::class, 'index'])->name('index');
    Route::get('/create', [KelasController::class, 'create'])->name('create');
    Route::post('/', [KelasController::class, 'store'])->name('store');
    // ... more routes
});
```

> Routes are automatically prefixed with the module name (e.g., `/kelas/`)  
> Route names are prefixed with `kelas.` (e.g., `kelas.index`)

### 3. Create Controller

**File:** `app/Modules/Kelas/Controllers/KelasController.php`

```php
<?php

namespace Modules\Kelas\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(): View
    {
        return view('kelas::index', [
            'title' => 'Data Kelas',
        ]);
    }
}
```

> Use `view('modulename::viewname')` to load module views

### 4. Create Model

**File:** `app/Modules/Kelas/Models/Kelas.php`

```php
<?php

namespace Modules\Kelas\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasUuids;
    
    protected $table = 'kelas';
    protected $fillable = ['nama', 'tingkat', 'wali_kelas_id'];
}
```

### 5. Create Migration

Put migrations in: `database/migrations/` (global) or `app/Modules/Kelas/Migrations/` (module)

Module migrations are auto-discovered!

### 6. Create Views

**File:** `app/Modules/Kelas/Views/index.blade.php`

```blade
@extends('layouts.app')

@section('title', $title)

@section('content')
    <x-card title="Daftar Kelas" icon="fas fa-door-open">
        <x-slot name="tools">
            <a href="{{ route('kelas.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Kelas
            </a>
        </x-slot>
        
        {{-- Your table content here --}}
    </x-card>
@endsection
```

### 7. Add Sidebar Menu

Create `app/Modules/Kelas/menu.php` — the sidebar will auto-discover and display it:

```php
<?php

return [
    'header' => 'DATA MASTER',           // Section header di sidebar
    'roles'  => ['SUPER_ADMIN', 'GURU'], // Roles yang bisa melihat menu
    'order'  => 10,                      // Urutan tampil (kecil = atas)
    'items'  => [
        [
            'label' => 'Data Kelas',
            'icon'  => 'fas fa-door-open',
            'route' => 'kelas.index',    // Named route
            'match' => 'kelas*',         // Pattern untuk active state
        ],
    ],
];
```

> **Tips:**
> - Gunakan `header` yang sama di beberapa modul untuk mengelompokkan di satu section
> - Tambah `children` array untuk sub-menu (treeview)
> - Gunakan `url` instead of `route` untuk link manual

**Contoh dengan sub-menu:**
```php
[
    'label' => 'Pembayaran',
    'icon'  => 'fas fa-money-bill-wave',
    'url'   => '#',
    'children' => [
        ['label' => 'SPP', 'route' => 'keuangan.spp.index', 'match' => 'keuangan/spp*'],
        ['label' => 'Biaya Lain', 'route' => 'keuangan.biaya.index', 'match' => 'keuangan/biaya*'],
    ],
]
```

### 8. Refresh Autoloader

```bash
composer dump-autoload
```

---

## 🎨 UI Component Usage (MANDATORY!)

> ⚠️ **CRITICAL:** Team members MUST use these components. Raw HTML for basic elements is PROHIBITED.

### View Full Documentation
Visit: http://localhost:8000/dev/ui-guide (only in debug mode)

### Quick Reference

#### Alerts
```blade
<x-alert type="success" message="Data berhasil disimpan!" dismissible />
<x-alert type="danger" message="Terjadi kesalahan!" />
<x-alert type="warning">Custom content with <strong>HTML</strong></x-alert>
```

#### Buttons
```blade
<x-btn variant="primary" icon="fas fa-save" type="submit">Simpan</x-btn>
<x-btn variant="success" icon="fas fa-plus" href="/create">Tambah</x-btn>
<x-btn variant="danger" size="sm" icon="fas fa-trash">Hapus</x-btn>
```

#### Cards
```blade
<x-card title="Card Title" icon="fas fa-list">
    Content here
    <x-slot name="tools">
        <button class="btn btn-sm btn-primary">Action</button>
    </x-slot>
    <x-slot name="footer">Footer content</x-slot>
</x-card>
```

#### Input Fields
```blade
<x-input label="Nama" name="nama" required />
<x-input label="Email" name="email" type="email" help="Masukkan email valid" />
<x-input label="Price" name="price" prepend="Rp" append=".00" />
```

#### Modals
```blade
<x-modal id="myModal" title="Modal Title" size="lg">
    Content
    <x-slot name="footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary">Simpan</button>
    </x-slot>
</x-modal>

<button data-toggle="modal" data-target="#myModal">Open Modal</button>
```

---

## 🔐 Role-Based Access Control (RBAC)

### Default Roles

| Role | Description |
|------|-------------|
| `SUPER_ADMIN` | Full system access |
| `GURU` | Teacher - manage grades, attendance |
| `SISWA` | Student - view own data |
| `STAF_TU` | Admin staff - manage student/teacher data |
| `KEUANGAN` | Finance - manage payments |

### Permission Format

Permissions use `module.action` format:
```
siswa.view, siswa.create, siswa.edit, siswa.delete
guru.view, guru.create, guru.edit, guru.delete
users.view, users.create, users.edit, users.delete
roles.view, roles.create, roles.edit, roles.delete
```

### Usage in Code

```php
// Check single role
if ($user->hasRole('SUPER_ADMIN')) { ... }

// Check multiple roles (any)
if ($user->hasRole(['GURU', 'STAF_TU'])) { ... }

// Check permission
if ($user->hasPermission('siswa.create')) { ... }

// Assign role
$user->assignRole('GURU');

// In Blade
@if(Auth::user()->hasRole('SUPER_ADMIN'))
    {{-- Admin only content --}}
@endif
```

### PermissionRegistry Service

Centralized permission definitions in `app/Services/PermissionRegistry.php`:

```php
use App\Services\PermissionRegistry;

// Get all permission groups
$groups = PermissionRegistry::all();

// Get all flat permissions
$all = PermissionRegistry::allPermissions();

// Get total count
$count = PermissionRegistry::count();
```

---

## 🗃️ Database

The project uses SQLite by default for development:
- Database file: `database/database.sqlite`
- Configure in `.env`: `DB_CONNECTION=sqlite`

For production, configure MySQL/PostgreSQL in `.env`.

---

## 📋 Development Workflow

1. **Pull latest changes** from master
2. **Create feature branch** for your module
3. **Use components** from `/resources/views/components/`
4. **Add sidebar menu** with proper active state
5. **Test locally** with `php artisan serve`
6. **Run migrations** if you added new tables
7. **Submit PR** for code review

---

## 🛠 Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# List all routes
php artisan route:list

# List module routes only
php artisan route:list --path=siswa

# Fresh migrate with seeding
php artisan migrate:fresh --seed

# Refresh autoloader after adding module
composer dump-autoload

# Create model with migration
php artisan make:model ModuleName -m
```

---

## 📞 Support

Contact the Lead Developer or Senior Architect for:
- Architecture questions
- New module creation guidance
- Component extension requests

---

**Built with ❤️ by SIAKAD Development Team**

