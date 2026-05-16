<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class GuruSiswaUserSeeder extends Seeder
{
    public function run()
    {
        // 1. Create/Update Guru User
        $guru = Guru::first();
        if ($guru) {
            $userGuru = User::updateOrCreate(
                ['email' => 'guru@gmail.com'],
                [
                    'name' => $guru->nama,
                    'password' => Hash::make('password'),
                    'ref_id' => $guru->id,
                    'ref_type' => 'Modules\Guru\Models\Guru'
                ]
            );
            $userGuru->syncRoles(['GURU']);
            $this->command->info('Guru User: guru@gmail.com / password');
        }

        // 2. Create/Update Siswa User
        $siswa = Siswa::first();
        if ($siswa) {
            $userSiswa = User::updateOrCreate(
                ['email' => 'siswa@gmail.com'],
                [
                    'name' => $siswa->nama,
                    'password' => Hash::make('password'),
                    'ref_id' => $siswa->id,
                    'ref_type' => 'Modules\Siswa\Models\Siswa'
                ]
            );
            $userSiswa->syncRoles(['SISWA']);
            $this->command->info('Siswa User: siswa@gmail.com / password');
        }
    }
}
