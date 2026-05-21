<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'hafidzkarim18@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "USER_NOT_FOUND: Akun dengan email '$email' tidak ditemukan di database.\n";
} else {
    echo "USER_FOUND:\n";
    echo "- Nama: " . $user->name . "\n";
    echo "- Email: " . $user->email . "\n";
    echo "- Status Akun: " . $user->account_status . "\n";
    
    // Reset password to Almahir@2026! to guarantee it matches
    $user->password = Hash::make('Almahir@2026!');
    $user->save();
    echo "\n[SUKSES] Password untuk akun '$email' telah direset ulang ke: Almahir@2026!\n";
}
