<?php

namespace App\Models\Traits;

use App\Models\Role;

/**
 * HasRoles Trait
 *
 * Provides role-based access control functionality to the User model.
 * Include this trait in any model that needs role-based permissions.
 *
 * @author SIAKAD Development Team
 */
trait HasRoles
{
    /**
     * Get all roles assigned to this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'sys_user_roles', 'user_id', 'role_id')
                    ->withTimestamps()
                    ->withPivot('assigned_by');
    }

    /**
     * Check if user has a specific role.
     *
     * @param string|array $roles Single role name or array of role names
     * @return bool
     *
     * Usage:
     *   $user->hasRole('SUPER_ADMIN')
     *   $user->hasRole(['GURU', 'STAF_TU'])
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if (is_array($roles)) {
            return $this->roles->whereIn('name', $roles)->isNotEmpty();
        }

        return false;
    }

    /**
     * Check if user has ALL of the specified roles.
     *
     * @param array $roles Array of role names
     * @return bool
     */
    public function hasAllRoles(array $roles): bool
    {
        $userRoleNames = $this->roles->pluck('name')->toArray();
        return count(array_intersect($roles, $userRoleNames)) === count($roles);
    }

    /**
     * Check if user has a specific permission.
     *
     * Permissions are stored as JSON array in each role.
     *
     * @param string $permission Permission key to check
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->hasRole('SUPER_ADMIN')) {
            return true;
        }

        foreach ($this->roles as $role) {
            $permissions = $role->permissions ?? [];
            if (in_array($permission, $permissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign a role to the user.
     *
     * @param string|Role $role Role name or Role model instance
     * @param string|null $assignedBy UUID of user who assigned this role
     * @return void
     */
    public function assignRole($role, ?string $assignedBy = null): void
    {
        $roleModel = $role instanceof Role ? $role : Role::where('name', $role)->orWhere('id', $role)->first();

        if ($roleModel && !$this->hasRole($roleModel->name)) {
            $this->roles()->attach($roleModel->id, [
                'id' => \Illuminate\Support\Str::uuid(),
                'assigned_by' => $assignedBy,
            ]);
        }
    }

    /**
     * Remove a role from the user.
     *
     * @param string|Role $role Role name or Role model instance
     * @return void
     */
    public function removeRole($role): void
    {
        $roleModel = $role instanceof Role ? $role : Role::where('name', $role)->first();

        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
        }
    }

    /**
     * Sync user roles (remove all existing and assign new ones).
     *
     * @param array $roleNames Array of role names
     * @param string|null $assignedBy UUID of user who assigned these roles
     * @return void
     */
    public function syncRoles(array $roleIdsOrNames, ?string $assignedBy = null): void
    {
        $roles = Role::whereIn('name', $roleIdsOrNames)->orWhereIn('id', $roleIdsOrNames)->pluck('id')->toArray();

        $syncData = [];
        foreach ($roles as $roleId) {
            $syncData[$roleId] = [
                'id' => \Illuminate\Support\Str::uuid(),
                'assigned_by' => $assignedBy,
            ];
        }

        $this->roles()->sync($syncData);
    }

    /**
     * Get array of role names for this user.
     *
     * @return array
     */
    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Check if user is a Super Admin.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('SUPER_ADMIN');
    }
}
