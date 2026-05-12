<?php

use App\Modules\Akademik\Models\Tingkat;
use App\Modules\Akademik\Models\Kelas;
use Modules\Siswa\Models\Siswa;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create Tingkat 12
$t12 = Tingkat::firstOrCreate(['kode_tingkat' => '12'], ['nama_tingkat' => '12']);
// Create Kelas 12 IPA 1
$k12 = Kelas::firstOrCreate(['kode_kelas' => '12-IPA'], ['nama_kelas' => 'IPA 1', 'tingkat_id' => $t12->id]);
// Update Raihan
Siswa::where('nama', 'Raihan')->update(['kelas_id' => $k12->id]);

// Create Tingkat 10
$t10 = Tingkat::firstOrCreate(['kode_tingkat' => '10'], ['nama_tingkat' => '10']);
// Create Kelas 10 A
$k10 = Kelas::firstOrCreate(['kode_kelas' => '10-A'], ['nama_kelas' => 'A', 'tingkat_id' => $t10->id]);
// Update Ahmad
Siswa::where('nama', 'Ahmad')->update(['kelas_id' => $k10->id]);

echo "Seed data akademik berhasil!";
