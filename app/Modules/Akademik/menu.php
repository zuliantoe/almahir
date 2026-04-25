<?php

return [
    'header' => 'SISTEM AKADEMIK',
    'order' => 10,
    'roles' => ['SUPER_ADMIN', 'STAFF', 'GURU', 'SISWA'],
    'items' => [
        [
            'label' => 'Dashboard Akademik',
            'icon' => 'fas fa-graduation-cap',
            'route' => 'akademik.index',
            'match' => 'akademik',
            'roles' => ['SUPER_ADMIN', 'STAFF', 'GURU', 'SISWA']
        ],
        [
            'label' => 'Jadwal Pelajaran',
            'icon' => 'fas fa-book-reader',
            'route' => 'akademik.jadwal-pelajaran.index',
            'match' => 'akademik/jadwal-pelajaran*',
            'roles' => ['SUPER_ADMIN', 'STAFF', 'GURU', 'SISWA']
        ],
        [
            'label' => 'Kalender Akademik',
            'icon' => 'fas fa-calendar-alt',
            'route' => 'akademik.kalender-akademik.index',
            'match' => 'akademik/kalender-akademik*',
            'roles' => ['SUPER_ADMIN', 'STAFF', 'GURU', 'SISWA']
        ],
        [
            'label' => 'Kurikulum',
            'icon' => 'fas fa-scroll',
            'route' => 'akademik.kurikulum.index',
            'match' => 'akademik/kurikulum*',
            'roles' => ['SUPER_ADMIN', 'STAFF']
        ],
        [
            'label' => 'Data Kelas',
            'icon' => 'fas fa-door-open',
            'route' => 'akademik.kelas.index',
            'match' => 'akademik/kelas*',
            'roles' => ['SUPER_ADMIN', 'STAFF']
        ],
        [
            'label' => 'Rombongan Belajar',
            'icon' => 'fas fa-users',
            'route' => 'akademik.rombel.index',
            'match' => 'akademik/rombel*',
            'roles' => ['SUPER_ADMIN', 'STAFF']
        ],
        [
            'label' => 'Data Master',
            'icon' => 'fas fa-database',
            'roles' => ['SUPER_ADMIN', 'STAFF'],
            'children' => [
                [
                    'label' => 'Tahun Ajaran',
                    'route' => 'akademik.tahun-ajaran.index',
                    'match' => 'akademik/tahun-ajaran*',
                    'roles' => ['SUPER_ADMIN', 'STAFF']
                ],
                [
                    'label' => 'Mata Pelajaran',
                    'route' => 'akademik.mata-pelajaran.index',
                    'match' => 'akademik/mata-pelajaran*',
                    'roles' => ['SUPER_ADMIN', 'STAFF']
                ],
                [
                    'label' => 'Jenis Kegiatan',
                    'route' => 'akademik.jenis-kegiatan.index',
                    'match' => 'akademik/jenis-kegiatan*',
                    'roles' => ['SUPER_ADMIN', 'STAFF']
                ],
            ]
        ]
    ]
];
