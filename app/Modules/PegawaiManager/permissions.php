<?php

/**
 * Permission definitions for PegawaiManager module.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => 'Kepegawaian',
    'modules' => [
        'pegawai' => [
            'label' => 'Data Pegawai',
            'permissions' => ['pegawai.view', 'pegawai.create', 'pegawai.edit', 'pegawai.delete'],
        ],
    ],
];
