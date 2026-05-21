<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;

$email = 'admin@siakad.local';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User $email not found.\n";
    exit(1);
}

// Log in the user in the session/guard so we can check can()
auth()->login($user);

echo "Checking user: $email\n";
echo "Roles: " . json_encode($user->roles->pluck('name')) . "\n";

// Check the permissions
$permissions = ['guru.view', 'guru.create', 'guru.edit'];
foreach ($permissions as $permission) {
    $hasPerm = Gate::allows($permission);
    echo "Can '$permission'? " . ($hasPerm ? 'YES' : 'NO') . "\n";
}

// Let's also check all permissions of the SUPER_ADMIN role
$role = \App\Models\Role::where('name', 'SUPER_ADMIN')->first();
if ($role) {
    echo "SUPER_ADMIN role permissions: " . json_encode($role->permissions->pluck('name')) . "\n";
} else {
    echo "SUPER_ADMIN role not found.\n";
}
