<?php

return [
    'header' => 'MANAJEMEN ASET & ASRAMA',
    'roles'  => ['SUPER_ADMIN', 'STAF_TU', 'GURU'],
    'order'  => 40,
    'items'  => [
        [
            'label'    => 'Dashboard Asrama',
            'icon'     => 'fas fa-tachometer-alt',
            'route'    => 'manajemenasetdanasrama.index',
            'match'    => 'manajemenasetdanasrama',
        ],
        [
            'label'    => 'Manajemen Aset',
            'icon'     => 'fas fa-boxes',
            'url'      => '#',
            'children' => [
                [
                    'label' => 'Pengajuan Aset',
                    'icon'  => 'fas fa-file-alt',
                    'route' => 'manajemenasetdanasrama.pengajuan.index',
                    'match' => 'manajemenasetdanasrama/pengajuan*',
                ],
                [
                    'label' => 'Persetujuan',
                    'icon'  => 'fas fa-check-double',
                    'route' => 'manajemenasetdanasrama.persetujuan.index',
                    'match' => 'manajemenasetdanasrama/persetujuan*',
                    'roles' => ['SUPER_ADMIN', 'STAF_TU'],
                ],
                [
                    'label' => 'Pengadaan',
                    'icon'  => 'fas fa-truck',
                    'route' => 'manajemenasetdanasrama.pengadaan.index',
                    'match' => 'manajemenasetdanasrama/pengadaan*',
                    'roles' => ['SUPER_ADMIN', 'STAF_TU'],
                ],
                [
                    'label' => 'Master Aset',
                    'icon'  => 'fas fa-boxes',
                    'route' => 'manajemenasetdanasrama.aset.index',
                    'match' => 'manajemenasetdanasrama/aset*',
                ],
                [
                    'label' => 'Kerusakan',
                    'icon'  => 'fas fa-exclamation-triangle',
                    'route' => 'manajemenasetdanasrama.kerusakan.index',
                    'match' => 'manajemenasetdanasrama/kerusakan*',
                ],
                [
                    'label' => 'Pemeliharaan',
                    'icon'  => 'fas fa-wrench',
                    'route' => 'manajemenasetdanasrama.pemeliharaan.index',
                    'match' => 'manajemenasetdanasrama/pemeliharaan*',
                    'roles' => ['SUPER_ADMIN', 'STAF_TU'],
                ],
            ],
        ],
        [
            'label'    => 'Asrama',
            'icon'     => 'fas fa-building',
            'url'      => '#',
            'children' => [
                [
                    'label' => 'Data Kamar',
                    'icon'  => 'fas fa-door-open',
                    'route' => 'manajemenasetdanasrama.kamar.index',
                    'match' => 'manajemenasetdanasrama/kamar*',
                ],
                [
                    'label' => 'Penghuni',
                    'icon'  => 'fas fa-users',
                    'route' => 'manajemenasetdanasrama.penghuni.index',
                    'match' => 'manajemenasetdanasrama/penghuni*',
                ],
                [
                    'label' => 'Jadwal Piket',
                    'icon'  => 'fas fa-calendar-alt',
                    'route' => 'manajemenasetdanasrama.jadwal-piket.index',
                    'match' => 'manajemenasetdanasrama/jadwal-piket*',
                ],
                [
                    'label' => 'Evaluasi Piket',
                    'icon'  => 'fas fa-chart-line',
                    'route' => 'manajemenasetdanasrama.jadwal-piket.evaluasi',
                    'match' => 'manajemenasetdanasrama/jadwal-piket/evaluasi*',
                ],
            ],
        ],
        [
            'label' => 'Trash',
            'icon'  => 'fas fa-trash-restore',
            'route' => 'manajemenasetdanasrama.trash.index',
            'match' => 'manajemenasetdanasrama/trash*',
            'roles' => ['SUPER_ADMIN', 'STAF_TU'],
        ],
    ],
];
