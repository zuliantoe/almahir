<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Modules\PegawaiManager\Models\Pegawai;
use Modules\PegawaiManager\Models\TypePegawai;
use Illuminate\Support\Str;

$email = 'admin@siakad.local';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "Error: User with email $email not found.\n";
    exit(1);
}

// Cek apakah sudah terhubung
if ($user->pegawai) {
    echo "User $email is already linked to Pegawai: {$user->pegawai->nama}\n";
    exit(0);
}

// Cari type pegawai default
$type = TypePegawai::where('nama_type', 'Pegawai')->first() 
        ?? TypePegawai::first();

if (!$type) {
    echo "Error: No TypePegawai found. Please seed the database first.\n";
    exit(1);
}

try {
    $pegawai = Pegawai::create([
        'nama' => $user->name,
        'user_id' => $user->id,
        'type_pegawai_id' => $type->id,
        'nip' => '1234567890',
        'tempat_lahir' => 'Surabaya',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Alamat Kantor',
        'tanggal_masuk' => date('Y-m-d'),
        'status' => 'aktif',
        'sisa_cuti' => 12
    ]);

    echo "Success! Successfully linked Super Admin ($email) to new Pegawai record:\n";
    echo "Pegawai Name: {$pegawai->nama}\n";
    echo "Pegawai ID: {$pegawai->id}\n";
} catch (\Exception $e) {
    echo "Error linking user: " . $e->getMessage() . "\n";
}
