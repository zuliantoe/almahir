<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Role Model
 *
 * Represents a system role in the RBAC system.
 * Roles can have permissions stored as JSON and can be marked as system roles
 * (which cannot be deleted).
 *
 * @property string $id UUID
 * @property string $name Role identifier (SUPER_ADMIN, GURU, etc.)
 * @property string $display_name Human readable name
 * @property string|null $description Role description
 * @property array|null $permissions JSON array of permission keys
 * @property bool $is_system If true, role cannot be deleted
 *
 * @author SIAKAD Development Team
 */
class Role extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'sys_roles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_system',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    /**
     * Get users with this role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'sys_user_roles', 'role_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Check if role has a specific permission.
     *
     * @param string $permission Permission key to check
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Add a permission to this role.
     *
     * @param string $permission Permission key to add
     * @return void
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];

        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * Remove a permission from this role.
     *
     * @param string $permission Permission key to remove
     * @return void
     */
    public function removePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->update(['permissions' => array_values($permissions)]);
    }

    /**
     * Sync permissions for this role.
     *
     * @param array $permissions Array of permission keys
     * @return void
     */
    public function syncPermissions(array $permissions): void
    {
        $this->update(['permissions' => $permissions]);
    }

    /**
     * Find role by name.
     *
     * @param string $name Role name
     * @return static|null
     */
    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }
}
