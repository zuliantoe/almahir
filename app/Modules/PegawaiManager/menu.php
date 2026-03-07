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
            'label' => 'Data Manager',
            'icon'  => 'fas fa-file-alt',
            'route' => 'pegawaimanager.index',
            'match' => 'pegawaimanager*',
        ],
    ],
];
