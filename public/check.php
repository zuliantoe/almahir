<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = \App\Models\User::where('email', 'irfanwahyudi@gmail.com')->first();
if (!$user) {
    echo "User not found";
    exit;
}

echo "Email: " . $user->email . "\n";
echo "Password Hash: " . $user->password . "\n";
echo "Check 'password123': " . (\Illuminate\Support\Facades\Hash::check('password123', $user->password) ? 'YES' : 'NO') . "\n";
echo "Check 'password': " . (\Illuminate\Support\Facades\Hash::check('password', $user->password) ? 'YES' : 'NO') . "\n";

// Let's create a test string, hash it, and check it
$testHash = \Illuminate\Support\Facades\Hash::make('password123');
echo "Test Hash: " . $testHash . "\n";
echo "Is test hash correct? " . (\Illuminate\Support\Facades\Hash::check('password123', $testHash) ? 'YES' : 'NO') . "\n";

