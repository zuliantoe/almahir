<?php

return [
    'header' => 'DATA MASTER',
    'order' => 10,
    'roles' => ['SUPER_ADMIN', 'STAFF', 'GURU'],
    'items' => [
        [
            'label' => 'Dashboard Akademik',
            'icon' => 'fas fa-tachometer-alt',
            'route' => 'akademik.index',
            'match' => 'akademik'
        ],
        [
            'label' => 'Jadwal Pelajaran',
            'icon' => 'fas fa-clock',
            'route' => 'akademik.jadwal-pelajaran.index',
            'match' => 'akademik/jadwal-pelajaran*'
        ],
        [
            'label' => 'Kalender Akademik',
            'icon' => 'fas fa-calendar-day',
            'route' => 'akademik.kalender-akademik.index',
            'match' => 'akademik/kalender-akademik*'
        ],
        [
            'label' => 'Kurikulum',
            'icon' => 'fas fa-scroll',
            'route' => 'akademik.kurikulum.index',
            'match' => 'akademik/kurikulum*'
        ],
        [
            'label' => 'Data Kelas',
            'icon' => 'fas fa-door-open',
            'route' => 'akademik.kelas.index',
            'match' => 'akademik/kelas*'
        ],
        [
            'label' => 'Data Master Lainnya',
            'icon' => 'fas fa-database',
            'children' => [
                [
                    'label' => 'Tahun Ajaran',
                    'route' => 'akademik.tahun-ajaran.index',
                    'match' => 'akademik/tahun-ajaran*'
                ],
                [
                    'label' => 'Mata Pelajaran',
                    'route' => 'akademik.mata-pelajaran.index',
                    'match' => 'akademik/mata-pelajaran*'
                ],
                [
                    'label' => 'Jenis Kegiatan',
                    'route' => 'akademik.jenis-kegiatan.index',
                    'match' => 'akademik/jenis-kegiatan*'
                ],
            ]
        ]
    ]
];
