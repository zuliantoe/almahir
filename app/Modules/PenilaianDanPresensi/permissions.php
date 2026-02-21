<?php

/**
 * Permission definitions for PenilaianDanPresensi module.
 * Auto-discovered by ModuleServiceProvider.
 */
return [
    'group' => 'Akademik',
    'modules' => [
        'nilai' => [
            'label' => 'Nilai',
            'permissions' => ['nilai.view', 'nilai.create', 'nilai.edit', 'nilai.delete'],
        ],
        'absensi' => [
            'label' => 'Absensi',
            'permissions' => ['absensi.view', 'absensi.create', 'absensi.edit', 'absensi.delete'],
        ],
        'penilaiantahfidz' => [
            'label' => 'Penilaian Tahfidz',
            'permissions' => ['penilaiantahfidz.view', 'penilaiantahfidz.create', 'penilaiantahfidz.edit', 'penilaiantahfidz.delete'],
        ],
        'izinsakit' => [
            'label' => 'Izin Sakit',
            'permissions' => ['izinsakit.view', 'izinsakit.create', 'izinsakit.edit', 'izinsakit.delete'],
        ],
    ],
];
