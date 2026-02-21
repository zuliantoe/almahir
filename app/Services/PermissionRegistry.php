<?php

namespace App\Services;

/**
 * PermissionRegistry
 * 
 * Dynamic permission registry for the RBAC system.
 * Permissions are auto-discovered from each module's permissions.php file.
 * Core permissions (Pengaturan) are registered as defaults.
 * 
 * Permission format: module.action
 * Actions: view, create, edit, delete, export
 * 
 * @author SIAKAD Development Team
 */
class PermissionRegistry
{
    /**
     * All permission groups with their permissions
     */
    protected static array $groups = [];

    /**
     * Whether default (core) permissions have been loaded
     */
    protected static bool $defaultsLoaded = false;

    /**
     * Action labels for display
     */
    protected static array $actionLabels = [
        'view' => 'Lihat',
        'create' => 'Tambah',
        'edit' => 'Edit',
        'delete' => 'Hapus',
        'export' => 'Export',
    ];

    /**
     * Register a permission group from a module.
     *
     * @param array $config Permission config with keys: group, modules
     */
    public static function register(array $config): void
    {
        $group = $config['group'] ?? 'Lainnya';
        $modules = $config['modules'] ?? [];

        if (!isset(self::$groups[$group])) {
            self::$groups[$group] = [];
        }

        self::$groups[$group] = array_merge(self::$groups[$group], $modules);
    }

    /**
     * Load default core permissions (Pengaturan).
     * These are not part of any module.
     */
    public static function loadDefaults(): void
    {
        if (self::$defaultsLoaded) {
            return;
        }

        self::register([
            'group' => 'Pengaturan',
            'modules' => [
                'users' => [
                    'label' => 'Manajemen User',
                    'permissions' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
                ],
                'roles' => [
                    'label' => 'Roles & Permissions',
                    'permissions' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
                ],
                'settings' => [
                    'label' => 'Konfigurasi Sistem',
                    'permissions' => ['settings.view', 'settings.edit'],
                ],
            ],
        ]);

        self::$defaultsLoaded = true;
    }

    /**
     * Get all permission groups with their definitions
     */
    public static function all(): array
    {
        self::loadDefaults();
        return self::$groups;
    }

    /**
     * Get all available permissions as a flat array
     */
    public static function allPermissions(): array
    {
        $permissions = [];
        
        foreach (self::all() as $groupName => $modules) {
            foreach ($modules as $moduleKey => $module) {
                foreach ($module['permissions'] as $permission) {
                    $permissions[] = $permission;
                }
            }
        }
        
        return $permissions;
    }

    /**
     * Get permissions for a specific group
     */
    public static function getGroup(string $groupName): array
    {
        self::loadDefaults();
        return self::$groups[$groupName] ?? [];
    }

    /**
     * Get action label for display
     */
    public static function getActionLabel(string $action): string
    {
        return self::$actionLabels[$action] ?? ucfirst($action);
    }

    /**
     * Parse permission key to get module and action
     */
    public static function parsePermission(string $permission): array
    {
        $parts = explode('.', $permission);
        return [
            'module' => $parts[0] ?? '',
            'action' => $parts[1] ?? '',
        ];
    }

    /**
     * Get total permission count
     */
    public static function count(): int
    {
        return count(self::allPermissions());
    }

    /**
     * Reset registry (useful for testing)
     */
    public static function reset(): void
    {
        self::$groups = [];
        self::$defaultsLoaded = false;
    }
}
