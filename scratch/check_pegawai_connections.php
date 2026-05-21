<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\PegawaiManager\Models\TypePegawai;

$types = TypePegawai::all();
echo "Total Types: " . $types->count() . "\n";
foreach ($types as $t) {
    echo "ID: {$t->id} - Nama: {$t->nama_type}\n";
}
