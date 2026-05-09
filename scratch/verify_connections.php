<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Bukti Hubungan Akademik -> Siswa ---\n";
$rombel = \App\Modules\Akademik\Models\Rombel::with('siswa')->first();
if ($rombel) {
    echo "Rombel: " . $rombel->nama_rombel . "\n";
    echo "Jumlah Siswa Terhubung: " . $rombel->siswa->count() . " siswa\n";
    foreach($rombel->siswa->take(5) as $s) {
        echo " - Siswa: " . $s->nama . " (NIS: " . $s->nis . ")\n";
    }
} else {
    echo "Belum ada data Rombel.\n";
}

echo "\n--- Bukti Hubungan Akademik -> Guru ---\n";
$jadwal = \App\Modules\Akademik\Models\JadwalPelajaran::with(['guru', 'mataPelajaran'])->first();
if ($jadwal) {
    echo "Mata Pelajaran: " . ($jadwal->mataPelajaran ? $jadwal->mataPelajaran->nama : 'N/A') . "\n";
    echo "Guru Pengajar: " . ($jadwal->guru ? $jadwal->guru->nama : 'Belum diset') . "\n";
    echo "Rombel: " . ($jadwal->rombel ? $jadwal->rombel->nama_rombel : 'N/A') . "\n";
} else {
    echo "Belum ada data Jadwal Pelajaran.\n";
}

echo "\n--- Bukti Kesiapan Module Penilaian ---\n";
if (class_exists('Modules\PenilaianDanPresensi\Models\PenilaianAkademik')) {
    echo "Module Penilaian terdeteksi.\n";
    $penilaian = \Modules\PenilaianDanPresensi\Models\PenilaianAkademik::with(['siswa', 'mataPelajaran'])->first();
    if ($penilaian) {
        echo "Data Penilaian sudah ada dan terhubung ke:\n";
        echo " - Siswa: " . ($penilaian->siswa ? $penilaian->siswa->nama : 'N/A') . "\n";
        echo " - Mapel: " . ($penilaian->mataPelajaran ? $penilaian->mataPelajaran->nama : 'N/A') . "\n";
    } else {
        echo "Module ada, tapi belum ada data penilaian di DB.\n";
    }
} else {
    echo "Module Penilaian belum terinstall atau namespace salah.\n";
}
