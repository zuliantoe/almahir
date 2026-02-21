<?php

/**
 * Permission definitions for Siswa module.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => 'Data Master',
    'modules' => [
        'siswa' => [
            'label' => 'Data Siswa',
            'permissions' => ['siswa.view', 'siswa.create', 'siswa.edit', 'siswa.delete'],
        ],
    ],
];
