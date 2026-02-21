<?php

/**
 * Sidebar menu configuration.
 * This file is auto-discovered by ModuleServiceProvider.
 */
return [
    'header' => 'Akademik',
    'roles'  => ['SUPER_ADMIN'],
    'order'  => 50,
    'items'  => [
        [
            'label' => 'Tahun Ajaran',
            'icon'  => 'fas fa-calendar',
            'route' => 'akademik.tahun-ajaran.index',
            'match' => 'akademik.tahun-ajaran.*',
        ],
    ],
];
