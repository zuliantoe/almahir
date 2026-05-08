<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Modules\Guru\Models\Guru;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\TahunAjaran;

$gurus = Guru::with('user')->get()->map(fn($g) => [
    'id' => $g->id,
    'nama' => $g->nama,
    'email' => $g->user->email ?? 'no email'
]);

$rombels = Rombel::all()->map(fn($r) => [
    'id' => $r->id,
    'nama' => $r->nama_rombel
]);

$mapels = MataPelajaran::all()->map(fn($m) => [
    'id' => $m->id,
    'nama' => $m->nama_pelajaran
]);

$tahunAjaran = TahunAjaran::where('status', 1)->first();

echo json_encode([
    'gurus' => $gurus,
    'rombels' => $rombels,
    'mapels' => $mapels,
    'tahunAjaran' => $tahunAjaran
], JSON_PRETTY_PRINT);
