<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$guruUsers = User::where('ref_type', 'Modules\Guru\Models\Guru')->get();

echo "NAMA | EMAIL\n";
foreach ($guruUsers as $user) {
    echo "{$user->name} | {$user->email}\n";
}
