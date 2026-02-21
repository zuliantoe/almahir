<?php

/**
 * Permission definitions for Keuangan module.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => 'Keuangan',
    'modules' => [
        'pembayaran' => [
            'label' => 'Pembayaran',
            'permissions' => ['pembayaran.view', 'pembayaran.create', 'pembayaran.edit', 'pembayaran.delete'],
        ],
        'laporan_keuangan' => [
            'label' => 'Laporan Keuangan',
            'permissions' => ['laporan_keuangan.view', 'laporan_keuangan.export'],
        ],
    ],
];
