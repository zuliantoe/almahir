<?php

/**
 * Sidebar menu configuration.
 * This file is auto-discovered by ModuleServiceProvider.
 */
return [
    'header' => 'KEPEGAWAIAN',
    'roles'  => ['SUPER_ADMIN', 'STAFF'],
    'order'  => 15,
    'items'  => [
        [
            'label' => 'Dashboard Pegawai',
            'icon'  => 'fas fa-chart-pie',
            'route' => 'pegawaimanager.dashboard',
            'match' => 'pegawaimanager/dashboard',
            'roles' => ['SUPER_ADMIN', 'STAFF'],
        ],
        [
            'label' => 'Data Pegawai',
            'icon'  => 'fas fa-users-cog',
            'route' => 'pegawaimanager.index',
            'match' => 'pegawaimanager',
            'roles' => ['SUPER_ADMIN', 'STAFF'],
        ],
        [
            'label' => 'Calon Pegawai',
            'icon'  => 'fas fa-user-plus',
            'route' => 'pegawaimanager.calon-pegawai.index',
            'match' => 'pegawaimanager/calon-pegawai*',
            'roles' => ['SUPER_ADMIN', 'STAFF'],
        ],
        [
            'label' => 'Jenis Pegawai',
            'icon'  => 'fas fa-tags',
            'route' => 'pegawaimanager.types.index',
            'match' => 'pegawaimanager/types*',
            'roles' => ['SUPER_ADMIN', 'STAFF'],
        ],
    ],
];

