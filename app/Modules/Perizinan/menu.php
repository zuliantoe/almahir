<?php

return [
    'header' => 'KEHADIRAN',
    'roles'  => ['SUPER_ADMIN', 'GURU', 'STAFF', 'STAF_TU'],
    'order'  => 21,
    'items'  => [
        [
            'label' => 'Perizinan Pegawai',
            'icon'  => 'fas fa-envelope-open-text',
            'route' => 'perizinan.index',
            'match' => 'perizinan*',
        ],
    ],
];
