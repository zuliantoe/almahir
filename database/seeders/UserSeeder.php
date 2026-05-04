<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 * 
 * Create default admin account for initial setup
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        if (User::where('email', 'admin@siakad.local')->exists()) {
            $this->command->info('✓ Super Admin already exists: admin@siakad.local');
            return;
        }

        // Create Super Admin user
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@siakad.local',
            'password' => Hash::make('password'),
            'account_status' => 'active',
        ]);

        // Assign SUPER_ADMIN role
        $superAdminRole = Role::where('name', 'SUPER_ADMIN')->first();
        if ($superAdminRole) {
            $admin->assignRole($superAdminRole->id);
        }

        $this->command->info('✓ Super Admin created: admin@siakad.local / password');
    }
}
