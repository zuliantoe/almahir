<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * RoleSeeder
 * 
 * Seeds ONLY the standard roles for the SIAKAD system.
 */
class RoleSeeder extends Seeder
{
    /**
     * Default roles configuration.
     */
    protected array $roles = [
        [
            'name' => 'SUPER_ADMIN',
            'display_name' => 'Super Administrator',
            'description' => 'Full system access.',
            'permissions' => ['*'],
        ],
        [
            'name' => 'GURU',
            'display_name' => 'Guru',
            'description' => 'Teacher role.',
            'permissions' => [
                'siswa.view',
                'nilai.view', 'nilai.create', 'nilai.edit',
                'absensi.view', 'absensi.create', 'absensi.edit',
            ],
        ],
        [
            'name' => 'STAFF',
            'display_name' => 'Staf Pegawai',
            'description' => 'General staff role.',
            'permissions' => [
                'profile.view', 'profile.edit',
            ],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'permissions' => $roleData['permissions'],
                    'is_system' => true,
                ]
            );
        }
    }
}
