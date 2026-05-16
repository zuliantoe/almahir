<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Modules\Akademik\Models\Rombel;

$rombels = Rombel::with('walikelas')->get();
foreach ($rombels as $r) {
    $wali = $r->walikelas->nama ?? 'Belum Diatur';
    echo "Rombel: {$r->nama_rombel} | Wali Kelas: {$wali}\n";
}
