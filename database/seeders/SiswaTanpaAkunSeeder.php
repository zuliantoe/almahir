<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Siswa\Models\Siswa;
use Faker\Factory as Faker;

class SiswaTanpaAkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $this->command->info('Menciptakan 10 data Siswa TANPA akun user...');

        for ($i = 1; $i <= 10; $i++) {
            $jk = $faker->randomElement(['L', 'P']);
            $nama = $faker->name($jk == 'L' ? 'male' : 'female');
            // Gunakan NIS yang berbeda agar tidak bentrok (start dari 9000)
            $nis = '24259' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $email = strtolower(str_replace(' ', '.', $nama)) . '.baru@siakad.local';

            Siswa::create([
                'nis' => $nis,
                'nama' => $nama,
                'email' => $email,
                'tanggal_lahir' => $faker->date('Y-m-d', '2010-01-01'),
                'tempat_lahir' => $faker->city,
                'jenis_kelamin' => $jk,
                'alamat' => $faker->address,
                'telepon' => $faker->phoneNumber,
                'tahun_masuk' => 2024,
                'status' => 'aktif',
            ]);
            
            $this->command->info("✓ Siswa berhasil dibuat: {$nama} ({$nis})");
        }
    }
}
