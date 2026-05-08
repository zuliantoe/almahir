<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;

class AcademicRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create a Demo Guru
        $guru = Guru::firstOrCreate(
            ['nip' => '198001012010011001'],
            [
                'nama' => 'Guru Demo Akademik',
                'email' => 'guru@siakad.com',
                'tanggal_lahir' => '1980-01-01',
                'tempat_lahir' => 'Jakarta',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Pendidikan No. 1',
                'telepon' => '081234567890',
                'status' => 'aktif',
            ]
        );

        $guruUser = User::firstOrCreate(
            ['email' => 'guru@siakad.com'],
            [
                'name' => 'Guru Demo Akademik',
                'password' => Hash::make('password'),
                'ref_type' => Guru::class,
                'ref_id' => $guru->id,
            ]
        );
        $guruUser->assignRole('GURU');

        // 2. Create a Demo Siswa
        $siswa = Siswa::firstOrCreate(
            ['nis' => '202401001'],
            [
                'nama' => 'Siswa Demo Akademik',
                'email' => 'siswa@siakad.com',
                'tanggal_lahir' => '2010-05-15',
                'tempat_lahir' => 'Bandung',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Pelajar No. 2',
                'telepon' => '089876543210',
                'tahun_masuk' => 2024,
                'status' => 'aktif',
            ]
        );

        $siswaUser = User::firstOrCreate(
            ['email' => 'siswa@siakad.com'],
            [
                'name' => 'Siswa Demo Akademik',
                'password' => Hash::make('password'),
                'ref_type' => Siswa::class,
                'ref_id' => $siswa->id,
            ]
        );
        $siswaUser->assignRole('SISWA');

        $this->command->info('Academic test accounts created successfully.');
        $this->command->info('Guru: guru@siakad.com | password');
        $this->command->info('Siswa: siswa@siakad.com | password');
    }
}
