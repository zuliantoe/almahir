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
            'label' => 'Beban Mengajar',
            'icon' => 'fas fa-chalkboard-teacher',
            'route' => 'akademik.beban-mengajar.index',
            'match' => 'akademik/beban-mengajar*',
            'roles' => ['SUPER_ADMIN', 'STAFF']
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
            'roles' => ['SUPER_ADMIN', 'STAFF'],
            'children' => [
                [
                    'label' => 'Daftar Rombel',
                    'route' => 'akademik.rombel.index',
                    'match' => 'akademik/rombel',
                    'roles' => ['SUPER_ADMIN', 'STAFF']
                ],
                [
                    'label' => 'Riwayat Perpindahan',
                    'route' => 'akademik.rombel.history',
                    'match' => 'akademik/rombel/history',
                    'roles' => ['SUPER_ADMIN', 'STAFF']
                ],
                [
                    'label' => 'Kenaikan/Perpindahan',
                    'route' => 'akademik.kenaikan-kelas.index',
                    'match' => 'akademik/kenaikan-kelas*',
                    'roles' => ['SUPER_ADMIN', 'STAFF']
                ],
            ]
        ],
        [
            'label' => 'Data Master',
            'icon' => 'fas fa-database',
            'roles' => ['SUPER_ADMIN', 'STAFF'],
            'children' => [
                [
                    'label' => 'Master Kurikulum',
                    'route' => 'akademik.master-kurikulum.index',
                    'match' => 'akademik/master-kurikulum*',
                    'roles' => ['SUPER_ADMIN', 'STAFF']
                ],
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
        ],
        [
            'label' => 'Laporan Akademik',
            'icon' => 'fas fa-chart-bar',
            'route' => 'akademik.laporan.index',
            'match' => 'akademik/laporan*',
            'roles' => ['SUPER_ADMIN', 'STAFF', 'KEPALA_SEKOLAH', 'GURU', 'SISWA']
        ]
    ]
];
