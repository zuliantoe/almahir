<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Modules\PegawaiManager\Models\Pegawai;

$email = 'admin@siakad.local';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "Error: User with email $email not found.\n";
    exit(1);
}

// Cek apakah terhubung dengan Pegawai
$pegawai = $user->pegawai;

if (!$pegawai) {
    echo "Success: User $email is already decoupled (no Pegawai record linked).\n";
    exit(0);
}

try {
    // Hapus data pegawai dummy yang terhubung dengan user admin
    // Karena foreign key menggunakan onDelete('cascade'), data absensi/izin terkait juga akan otomatis terhapus
    $pegawaiName = $pegawai->nama;
    $pegawaiId = $pegawai->id;
    $pegawai->forceDelete(); // forceDelete jika menggunakan softDeletes di model

    echo "Success! Successfully decoupled Admin ($email) by deleting its dummy Pegawai record:\n";
    echo "Pegawai Name: {$pegawaiName}\n";
    echo "Pegawai ID: {$pegawaiId}\n";
    echo "Account admin@siakad.local is now a pure Administrator account without daily employee duties.\n";
} catch (\Exception $e) {
    echo "Error decoupling user: " . $e->getMessage() . "\n";
}
