<?php

return [
    'header' => 'KEHADIRAN',
    'roles'  => ['SUPER_ADMIN', 'STAFF', 'STAF_TU', 'PEGAWAI'],
    'order'  => 21,
    'items'  => [
        [
            'label' => 'Perizinan Pegawai',
            'icon'  => 'fas fa-envelope-open-text',
            'route' => 'perizinan.index',
            'match' => 'perizinan*',
            'roles' => ['SUPER_ADMIN', 'STAFF', 'STAF_TU', 'PEGAWAI'],
        ],
    ],
];

