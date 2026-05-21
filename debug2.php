<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== GURU USERS ===\n";
$gurus = User::whereHas('roles', fn($q) => $q->where('name', 'GURU'))->get();
foreach ($gurus as $u) {
    $ref = $u->ref;
    echo "User: {$u->name} | Email: {$u->email} | ref_type: {$u->ref_type} | ref_id: {$u->ref_id} | Ref Exists: " . ($ref ? 'YES' : 'NO (NULL!)') . "\n";
}

echo "\n=== SISWA USERS ===\n";
$siswas = User::whereHas('roles', fn($q) => $q->where('name', 'SISWA'))->get();
foreach ($siswas as $u) {
    $ref = $u->ref;
    echo "User: {$u->name} | Email: {$u->email} | ref_type: {$u->ref_type} | ref_id: {$u->ref_id} | Ref Exists: " . ($ref ? 'YES' : 'NO (NULL!)') . "\n";
}
