<?php
$users = App\Models\User::with('roles')->whereIn('email', ['admin@siakad.local', 'budisuper@gmail.com'])->get();
foreach ($users as $u) {
    echo "Email: " . $u->email . "\n";
    echo "Roles: " . json_encode($u->roles->pluck('name')) . "\n\n";
}
