<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;

$today = Carbon::now()->locale('id')->translatedFormat('l');
echo "today: " . var_export($today, true) . "\n";

$ta = \App\Modules\Akademik\Models\TahunAjaran::aktif()->first();
echo "TA Aktif: " . ($ta ? $ta->id . " - " . $ta->tahunajaran . " - status=" . $ta->status : "NONE") . "\n";

foreach (\App\Modules\Akademik\Models\TahunAjaran::all() as $t) {
    echo "TA: id={$t->id}, {$t->tahunajaran}, status={$t->status}\n";
}

$guru = \Modules\Guru\Models\Guru::first();
echo "\nGuru: " . $guru?->nama . " id=" . $guru?->id . "\n";

// Check jadwal with guru_id directly
$j = \App\Modules\Akademik\Models\JadwalPelajaran::with(['mataPelajaran','rombel'])
    ->where('guru_id', $guru?->id)->get();
echo "All jadwal for this guru: " . $j->count() . "\n";
foreach ($j as $jj) {
    echo "  hari={$jj->hari}, jamke={$jj->jamke}, mapel=" . ($jj->mataPelajaran?->nama ?? 'NULL') . ", rombel=" . ($jj->rombel?->nama_rombel ?? 'NULL') . ", rombel_ta=" . ($jj->rombel?->tahunajaran_id ?? 'NULL') . "\n";
}

echo "\n=== Cek filter hari=Kamis untuk guru ini ===\n";
$jk = \App\Modules\Akademik\Models\JadwalPelajaran::with(['mataPelajaran','rombel'])
    ->where('guru_id', $guru?->id)->where('hari', 'Kamis')->get();
echo "Count: " . $jk->count() . "\n";
foreach ($jk as $jj) {
    echo "  mapel=" . ($jj->mataPelajaran?->nama ?? 'NULL') . "\n";
}
