<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Lokasi Kantor (GPS)
    |--------------------------------------------------------------------------
    |
    | Latitude dan Longitude pusat kantor untuk divalidasi dengan rumus Haversine.
    | Default koordinat di bawah bisa diubah langsung via .env
    |
    */
    'office_latitude' => env('OFFICE_LATITUDE', '-6.2088'), 
    'office_longitude' => env('OFFICE_LONGITUDE', '106.8456'),
    
    /*
    | Radius Toleransi (dalam satuan METER)
    */
    'office_radius' => env('OFFICE_RADIUS', 50000000),

    /*
    | Secret Key untuk membuat QR Code lebih aman
    */
    'qr_secret' => env('ABSENSI_QR_SECRET', 'ALMAHIRA_SECURE_KEY_2026'),
];
