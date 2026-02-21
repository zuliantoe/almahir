<?php

/**
 * Permission definitions for Akademik module.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => 'Akademik',
    'modules' => [
        'tahunajaran' => [
            'label' => 'Tahun Ajaran',
            'permissions' => ['tahunajaran.view', 'tahunajaran.create', 'tahunajaran.edit', 'tahunajaran.delete'],
        ],
        'jadwal' => [
            'label' => 'Jadwal Pelajaran',
            'permissions' => ['jadwal.view', 'jadwal.create', 'jadwal.edit', 'jadwal.delete'],
        ],
        'kelas' => [
            'label' => 'Data Kelas',
            'permissions' => ['kelas.view', 'kelas.create', 'kelas.edit', 'kelas.delete'],
        ],
    ],
];
