<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\PegawaiManager\Models\Pegawai;
use Modules\PegawaiManager\Models\TypePegawai;
use Illuminate\Support\Str;

class ExtraSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds to add 3 extra superadmins.
     */
    public function run(): void
    {
        $typePegawai = TypePegawai::where('nama_type', 'Admin (Super Admin)')->first();
        $superAdminRole = Role::where('name', 'SUPER_ADMIN')->first();

        if (!$typePegawai) {
            $this->command->error('TypePegawai "Admin (Super Admin)" not found!');
            return;
        }

        if (!$superAdminRole) {
            $this->command->error('Role "SUPER_ADMIN" not found!');
            return;
        }

        $admins = [
            [
                'name' => 'Ahmad Superadmin',
                'username' => 'ahmad.super',
                'email' => 'ahmad.super@siakad.local',
                'no_hp' => '081211112222',
            ],
            [
                'name' => 'Siti Superadmin',
                'username' => 'siti.super',
                'email' => 'siti.super@siakad.local',
                'no_hp' => '081233334444',
            ],
            [
                'name' => 'Budi Superadmin',
                'username' => 'budi.super',
                'email' => 'budi.super@siakad.local',
                'no_hp' => '081255556666',
            ],
        ];

        foreach ($admins as $data) {
            // 1. Create or Update User (search by username)
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'account_status' => 'active',
                    'phone' => $data['no_hp'],
                ]
            );

            // 2. Assign SUPER_ADMIN role
            $user->assignRole('SUPER_ADMIN');

            // 3. Create or Update Pegawai record
            Pegawai::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $data['name'],
                    'type_pegawai_id' => $typePegawai->id,
                    'no_hp' => $data['no_hp'],
                    'email' => $data['email'],
                    'alamat' => 'Alamat Superadmin ' . $data['name'],
                    'tanggal_masuk' => now(),
                ]
            );

            $this->command->info("✓ Super Admin updated/created: {$data['email']} | username: {$data['username']}");
        }
    }
}
