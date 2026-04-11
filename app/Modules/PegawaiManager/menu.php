<?php

/**
 * Sidebar menu configuration.
 * This file is auto-discovered by ModuleServiceProvider.
 */
return [
    'header' => 'DATA MASTER',
    'roles'  => ['SUPER_ADMIN', 'GURU', 'STAFF'],
    'order'  => 10,
    'items'  => [
        [
            'label' => 'Dashboard Pegawai',
            'icon'  => 'fas fa-chart-pie',
            'route' => 'pegawaimanager.dashboard',
            'match' => 'pegawaimanager/dashboard',
        ],
        [
            'label' => 'Data Pegawai',
            'icon'  => 'fas fa-users-cog',
            'route' => 'pegawaimanager.index',
            'match' => 'pegawaimanager',
        ],
        [
            'label' => 'Jenis Pegawai',
            'icon'  => 'fas fa-tags',
            'route' => 'pegawaimanager.types.index',
            'match' => 'pegawaimanager/types*',
        ],
    ],
];
