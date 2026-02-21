<?php

/**
 * Permission definitions for WaliMurid module.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => 'Data Master',
    'modules' => [
        'walimurid' => [
            'label' => 'Data Wali Murid',
            'permissions' => ['walimurid.view', 'walimurid.create', 'walimurid.edit', 'walimurid.delete'],
        ],
    ],
];
