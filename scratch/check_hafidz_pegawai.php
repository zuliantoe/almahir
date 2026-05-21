<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Schema;

$email = 'hafidzkarim18@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found\n";
    exit;
}

$pegawai = $user->pegawai;
if (!$pegawai) {
    echo "Pegawai record not linked to user account!\n";
    exit;
}

echo "Pegawai Details:\n";
echo "- Nama: " . $pegawai->nama . "\n";

// Check if sisa_cuti column exists
if (Schema::hasColumn('pegawai', 'sisa_cuti')) {
    echo "- Kolom 'sisa_cuti' ada di database.\n";
    echo "- Nilai 'sisa_cuti' saat ini: " . var_export($pegawai->sisa_cuti, true) . "\n";
} else {
    echo "- Kolom 'sisa_cuti' TIDAK ditemukan di tabel pegawai database Anda!\n";
}
