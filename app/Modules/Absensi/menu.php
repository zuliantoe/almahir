<?php

/**
 * Sidebar menu configuration for Absensi module.
 * This file is auto-discovered by ModuleServiceProvider.
 */
return [
    'header' => 'KEHADIRAN',
    'roles'  => ['SUPER_ADMIN', 'GURU', 'STAFF'], // Roles allowed to see this menu
    'order'  => 20,
    'items'  => [
        [
            'label' => 'Presensi Harian',
            'icon'  => 'fas fa-fingerprint',
            'route' => 'absensi.create',
            'match' => 'absensi/scan',
        ],
        [
            'label' => 'Riwayat Absensi',
            'icon'  => 'fas fa-history',
            'route' => 'absensi.index',
            'match' => 'absensi',
        ],
        [
            'label' => 'Monitoring Kehadiran',
            'icon'  => 'fas fa-desktop',
            'route' => 'absensi.manage.index',
            'match' => 'absensi/manage*',
        ],
    ],
];
