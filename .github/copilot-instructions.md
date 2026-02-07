# AI Coding Agent Instructions for SIAKAD

## Project Overview
SIAKAD is a modular monolith Academic Information System built with Laravel 12, featuring AdminLTE 3 UI and role-based access control. The system manages student, teacher, and administrative data for educational institutions.

## Architecture
- **Modular Structure**: All business logic organized in `app/Modules/` directory
- **Auto-Discovery**: `ModuleServiceProvider` automatically registers routes, views, and migrations from modules
- **UUID Primary Keys**: All models use UUIDs instead of auto-incrementing IDs
- **Soft Deletes**: Models include soft delete functionality by default

## Module Structure
Each module follows this pattern:
```
app/Modules/{ModuleName}/
├── Controllers/          # HTTP controllers
├── Models/              # Eloquent models
├── Routes/web.php       # Web routes (auto-prefixed with module name)
├── Views/               # Blade templates (namespaced as 'modulename::')
└── Migrations/          # Database migrations (auto-discovered)
```

### Route Registration
- Routes are automatically prefixed: `/{modulename}/`
- Route names prefixed: `{modulename}.`
- All module routes use `auth` middleware by default
- Example: `app/Modules/Siswa/Routes/web.php` → routes accessible at `/siswa/*`

### View Namespacing
- Views loaded with `view('modulename::viewname')`
- Example: `@include('siswa::index')` loads `app/Modules/Siswa/Views/index.blade.php`

## Model Patterns
```php
<?php
namespace Modules\{ModuleName}\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class {ModelName} extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $table = '{table_name}';
    protected $fillable = ['field1', 'field2'];
    protected $casts = ['date_field' => 'date'];
}
```

## Permission System
- Centralized in `app/Services/PermissionRegistry.php`
- Format: `{module}.{action}` (e.g., `siswa.view`, `guru.create`)
- Actions: `view`, `create`, `edit`, `delete`
- Super admin role has all permissions automatically

## UI Components
Use AdminLTE-styled Blade components from `resources/views/components/`:
- `<x-card>` - Main content container with header/tools
- `<x-btn>` - Styled buttons
- `<x-alert>` - Notification messages
- `<x-input>` - Form inputs
- `<x-modal>` - Modal dialogs

## Development Workflow
```bash
# Create complete module
php artisan make:module {ModuleName} --all

# Individual components
php artisan make:module-controller {Module} {ControllerName} --resource
php artisan make:module-model {Module} {ModelName} -m
php artisan make:module-migration {Module} {migration_name} --table={table}

# Build assets
npm run dev    # Development with hot reload
npm run build  # Production build

# Database
php artisan migrate
php artisan db:seed --class=RoleSeeder
```

## Key Files
- `app/Providers/ModuleServiceProvider.php` - Module auto-discovery
- `app/Services/PermissionRegistry.php` - Permission definitions
- `app/Models/Traits/HasRoles.php` - RBAC functionality
- `resources/views/layouts/app.blade.php` - Main AdminLTE layout
- `resources/views/layouts/partials/sidebar.blade.php` - Navigation menu

## Conventions
- Controllers extend `App\Http\Controllers\Controller`
- Models always include `HasUuids` and `SoftDeletes`
- Routes grouped with `auth` middleware
- Views extend `layouts.app` and use component slots
- Permissions checked via `$user->hasPermission('{module}.{action}')`
- Sidebar navigation updated manually in `sidebar.blade.php`

## Testing
- Use PHPUnit with standard Laravel testing structure
- Feature tests in `tests/Feature/`
- Unit tests in `tests/Unit/`

## Deployment Notes
- Environment file required: `.env` (copy from `.env.example`)
- Default admin: `admin@siakad.local` / `password`
- Run migrations and role seeder on fresh installs</content>
<parameter name="filePath">d:\laragon\www\almahir\.github\copilot-instructions.md