<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Perizinan\Models\Perizinan;
use Carbon\Carbon;

$today = Carbon::today()->toDateString();
echo "Today date: $today\n";

$all = Perizinan::all();
echo "Total permissions rows: " . $all->count() . "\n";
foreach ($all as $item) {
    echo "ID: {$item->id}, User ID: {$item->user_id}, Status: {$item->status}, Mulai: {$item->tanggal_mulai->toDateString()}, Selesai: {$item->tanggal_selesai->toDateString()}, Jenis: {$item->jenis_izin}\n";
}

$disetujuiCount = Perizinan::where('status', 'disetujui')
    ->whereDate('tanggal_mulai', '<=', $today)
    ->whereDate('tanggal_selesai', '>=', $today)->count();
echo "Disetujui Count today: $disetujuiCount\n";

$pendingOrDisetujuiCount = Perizinan::whereIn('status', ['menunggu', 'disetujui'])
    ->whereDate('tanggal_mulai', '<=', $today)
    ->whereDate('tanggal_selesai', '>=', $today)->count();
echo "Pending or Disetujui Count today: $pendingOrDisetujuiCount\n";
