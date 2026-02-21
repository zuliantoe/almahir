<?php

/**
 * Permission definitions for Guru module.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => 'Data Master',
    'modules' => [
        'guru' => [
            'label' => 'Data Guru',
            'permissions' => ['guru.view', 'guru.create', 'guru.edit', 'guru.delete'],
        ],
    ],
];
