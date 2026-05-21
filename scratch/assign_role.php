<?php
$user = App\Models\User::where('email', 'admin@siakad.local')->first();
if ($user && !$user->hasRole('SUPER_ADMIN')) {
    $user->assignRole('SUPER_ADMIN');
    echo "Role SUPER_ADMIN berhasil ditambahkan ke " . $user->email . "\n";
} else {
    echo "User sudah memiliki role atau tidak ditemukan.\n";
}
