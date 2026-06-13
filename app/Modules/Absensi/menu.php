<?php

/**
 * Sidebar menu configuration for Absensi module.
 * This file is auto-discovered by ModuleServiceProvider.
 */
return [
    'header' => 'KEHADIRAN',
    'roles'  => ['SUPER_ADMIN', 'STAFF', 'PEGAWAI'],
    'order'  => 20,
    'items'  => [
        [
            'label' => 'Presensi Harian',
            'icon'  => 'fas fa-fingerprint',
            'route' => 'absensi.create',
            'match' => 'absensi/scan',
            'roles' => ['SUPER_ADMIN', 'STAFF', 'PEGAWAI'],
        ],
        [
            'label' => 'Riwayat Absensi',
            'icon'  => 'fas fa-history',
            'route' => 'absensi.index',
            'match' => 'absensi',
            'roles' => ['SUPER_ADMIN', 'STAFF', 'PEGAWAI'],
        ],
        [
            'label' => 'Monitoring Kehadiran',
            'icon'  => 'fas fa-desktop',
            'route' => 'absensi.manage.index',
            'match' => 'absensi/manage',
            'roles' => ['SUPER_ADMIN'],
        ],
        [
            'label' => 'Setting Hari Libur',
            'icon'  => 'fas fa-calendar-times',
            'route' => 'absensi.hari-libur.index',
            'match' => 'absensi/hari-libur',
            'roles' => ['SUPER_ADMIN'],
        ],
        [
            'label' => 'Layar Lobi (QR Code)',
            'icon'  => 'fas fa-qrcode',
            'route' => 'absensi.manage.qr-generator',
            'match' => 'absensi/manage/qr-generator',
            'roles' => ['SUPER_ADMIN'],
        ],
    ],
];

