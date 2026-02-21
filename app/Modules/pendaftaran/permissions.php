<?php

/**
 * Permission definitions for Pendaftaran module.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => 'Pendaftaran',
    'modules' => [
        'pendaftaran' => [
            'label' => 'PPDB Online',
            'permissions' => ['pendaftaran.view', 'pendaftaran.create', 'pendaftaran.edit', 'pendaftaran.delete'],
        ],
    ],
];
