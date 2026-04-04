<?php

/**
 * Sidebar menu configuration.
 * This file is auto-discovered by ModuleServiceProvider.
 */
return [
    'header' => 'PEGAWAI',
    'roles'  => ['SUPER_ADMIN', 'GURU', 'PEGAWAI'],
    'order'  => 10,
    'items'  => [
        [
            'label' => 'Data Pegawai',
            'icon'  => 'fas fa-users',
            'route' => 'pegawaimanager.index',
            'match' => 'pegawaimanager',
        ],
        [
            'label' => 'Tipe Pegawai',
            'icon'  => 'fas fa-tags',
            'route' => 'pegawaimanager.types.index',
            'match' => 'pegawaimanager/types*',
        ],
    ],
];
