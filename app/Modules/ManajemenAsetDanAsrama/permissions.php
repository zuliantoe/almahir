<?php

/**
 * Permission definitions for ManajemenAsetDanAsrama module.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => 'Sarana & Prasarana',
    'modules' => [
        'aset' => [
            'label' => 'Manajemen Aset',
            'permissions' => ['aset.view', 'aset.create', 'aset.edit', 'aset.delete'],
        ],
        'asrama' => [
            'label' => 'Manajemen Asrama',
            'permissions' => ['asrama.view', 'asrama.create', 'asrama.edit', 'asrama.delete'],
        ],
    ],
];
