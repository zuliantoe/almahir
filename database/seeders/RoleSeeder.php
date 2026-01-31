<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * RoleSeeder
 * 
 * Seeds the default roles for the SIAKAD system.
 * These roles are marked as system roles and cannot be deleted.
 * 
 * @author SIAKAD Development Team
 */
class RoleSeeder extends Seeder
{
    /**
     * Default roles configuration.
     * 
     * Each role has:
     * - name: Unique identifier (used in code)
     * - display_name: Human readable name (for UI)
     * - description: Role description
     * - permissions: Array of permission keys
     */
    protected array $roles = [
        [
            'name' => 'SUPER_ADMIN',
            'display_name' => 'Super Administrator',
            'description' => 'Full system access. Can manage all aspects of the system including users, roles, and configurations.',
            'permissions' => ['*'], // Wildcard = all permissions
        ],
        [
            'name' => 'GURU',
            'display_name' => 'Guru',
            'description' => 'Teacher role. Can manage classes, grades, attendance, and view student data.',
            'permissions' => [
                'siswa.view',
                'kelas.view', 'kelas.edit',
                'nilai.view', 'nilai.create', 'nilai.edit',
                'absensi.view', 'absensi.create', 'absensi.edit',
                'jadwal.view',
            ],
        ],
        [
            'name' => 'SISWA',
            'display_name' => 'Siswa',
            'description' => 'Student role. Can view own grades, attendance, schedule, and profile.',
            'permissions' => [
                'profile.view', 'profile.edit',
                'nilai.view.own',
                'absensi.view.own',
                'jadwal.view',
            ],
        ],
        [
            'name' => 'STAF_TU',
            'display_name' => 'Staf Tata Usaha',
            'description' => 'Administrative staff. Can manage student data, registration, and generate reports.',
            'permissions' => [
                'siswa.view', 'siswa.create', 'siswa.edit', 'siswa.delete',
                'guru.view', 'guru.create', 'guru.edit',
                'kelas.view', 'kelas.create', 'kelas.edit',
                'report.generate',
            ],
        ],
        [
            'name' => 'KEUANGAN',
            'display_name' => 'Staf Keuangan',
            'description' => 'Finance staff. Can manage payments, invoices, and financial reports.',
            'permissions' => [
                'siswa.view',
                'pembayaran.view', 'pembayaran.create', 'pembayaran.edit',
                'tagihan.view', 'tagihan.create', 'tagihan.edit',
                'laporan.keuangan',
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
                    'is_system' => true, // Mark as system role
                ]
            );
        }

        $this->command->info('✓ Default roles seeded successfully.');
        $this->command->table(
            ['Role', 'Display Name', 'Permissions Count'],
            collect($this->roles)->map(fn($r) => [
                $r['name'],
                $r['display_name'],
                count($r['permissions']),
            ])->toArray()
        );
    }
}
