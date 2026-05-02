<?php

/**
 * Sidebar menu configuration for Absensi module.
 * This file is auto-discovered by ModuleServiceProvider.
 */
return [
    'header' => 'KEHADIRAN',
    'roles'  => ['SUPER_ADMIN', 'STAFF'],
    'order'  => 20,
    'items'  => [
        [
            'label' => 'Presensi Harian',
            'icon'  => 'fas fa-fingerprint',
            'route' => 'absensi.create',
            'match' => 'absensi/scan',
            'roles' => ['SUPER_ADMIN', 'STAFF'],
        ],
        [
            'label' => 'Riwayat Absensi',
            'icon'  => 'fas fa-history',
            'route' => 'absensi.index',
            'match' => 'absensi',
            'roles' => ['SUPER_ADMIN', 'STAFF'],
        ],
        [
            'label' => 'Monitoring Kehadiran',
            'icon'  => 'fas fa-desktop',
            'route' => 'absensi.manage.index',
            'match' => 'absensi/manage*',
            'roles' => ['SUPER_ADMIN', 'STAFF'],
        ],
    ],
];

