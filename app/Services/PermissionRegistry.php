<?php

namespace App\Services;

/**
 * PermissionRegistry
 * 
 * Centralized permission definitions for the RBAC system.
 * All available permissions are defined here for consistency.
 * 
 * Permission format: module.action
 * Actions: view, create, edit, delete
 * 
 * @author SIAKAD Development Team
 */
class PermissionRegistry
{
    /**
     * All permission groups with their permissions
     */
    protected static array $groups = [
        'Data Master' => [
            'siswa' => [
                'label' => 'Data Siswa',
                'permissions' => ['siswa.view', 'siswa.create', 'siswa.edit', 'siswa.delete'],
            ],
            'guru' => [
                'label' => 'Data Guru',
                'permissions' => ['guru.view', 'guru.create', 'guru.edit', 'guru.delete'],
            ],
            'walimurid' => [
                'label' => 'Data Wali Murid',
                'permissions' => ['walimurid.view', 'walimurid.create', 'walimurid.edit', 'walimurid.delete'],
            ],
            'kelas' => [
                'label' => 'Data Kelas',
                'permissions' => ['kelas.view', 'kelas.create', 'kelas.edit', 'kelas.delete'],
            ],
        ],
        'Akademik' => [
            'jadwal' => [
                'label' => 'Jadwal Pelajaran',
                'permissions' => ['jadwal.view', 'jadwal.create', 'jadwal.edit', 'jadwal.delete'],
            ],
            'nilai' => [
                'label' => 'Nilai',
                'permissions' => ['nilai.view', 'nilai.create', 'nilai.edit', 'nilai.delete'],
            ],
            'absensi' => [
                'label' => 'Absensi',
                'permissions' => ['absensi.view', 'absensi.create', 'absensi.edit', 'absensi.delete'],
            ],
            'perizinan' => [
                'label' => 'Perizinan Pegawai',
                'permissions' => ['perizinan.view', 'perizinan.create', 'perizinan.manage'],
            ],
        ],
        'Keuangan' => [
            'pembayaran' => [
                'label' => 'Pembayaran',
                'permissions' => ['pembayaran.view', 'pembayaran.create', 'pembayaran.edit', 'pembayaran.delete'],
            ],
            'laporan_keuangan' => [
                'label' => 'Laporan Keuangan',
                'permissions' => ['laporan_keuangan.view', 'laporan_keuangan.export'],
            ],
        ],
        'Pengaturan' => [
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
    ];

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
     * Get all permission groups with their definitions
     */
    public static function all(): array
    {
        return self::$groups;
    }

    /**
     * Get all available permissions as a flat array
     */
    public static function allPermissions(): array
    {
        $permissions = [];
        
        foreach (self::$groups as $groupName => $modules) {
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
}
