<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use Modules\Siswa\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MassiveSiswaPromotionSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        
        // 1. Dapatkan Tahun Ajaran 2024/2025 (Asal)
        $pastYear = TahunAjaran::where('tahunajaran', '2024/2025')->first();
        if (!$pastYear) {
            $pastYear = TahunAjaran::create(['tahunajaran' => '2024/2025', 'status' => 0]);
        }

        // 2. Dapatkan Rombel di Tahun 2024/2025
        $rombel = Rombel::where('tahunajaran_id', $pastYear->id)->first();
        if (!$rombel) {
            $this->command->error('Rombel untuk tahun 2024/2025 tidak ditemukan. Jalankan YearOverYearPromotionSeeder dulu.');
            return;
        }

        $this->command->info("Creating 30 students and assigning them to Rombel: {$rombel->nama_rombel} ($pastYear->tahunajaran)");

        for ($i = 0; $i < 30; $i++) {
            $gender = $faker->randomElement(['L', 'P']);
            $firstName = ($gender == 'L') ? $faker->firstNameMale : $faker->firstNameFemale;
            $lastName = $faker->lastName;
            
            $siswa = Siswa::create([
                'nis' => 'SNTR' . str_pad($i + 500, 4, '0', STR_PAD_LEFT),
                'nama' => $firstName . ' ' . $lastName,
                'email' => $faker->unique()->safeEmail,
                'jenis_kelamin' => $gender,
                'status' => 'aktif',
                'tahun_masuk' => 2024
            ]);

            // Hubungkan ke Rombel Tahun Lalu
            RombelSiswa::create([
                'rombel_id' => $rombel->id,
                'siswa_id' => $siswa->id,
                'tahunajaran_id' => $pastYear->id,
                'status' => 'aktif'
            ]);
        }

        $this->command->info('Successfully created and assigned 30 students.');
    }
}
