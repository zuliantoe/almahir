<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Siswa\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MassiveSiswaSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        
        $this->command->info('Creating 30 new students...');

        for ($i = 0; $i < 30; $i++) {
            $gender = $faker->randomElement(['L', 'P']);
            $firstName = ($gender == 'L') ? $faker->firstNameMale : $faker->firstNameFemale;
            $lastName = $faker->lastName;
            
            Siswa::create([
                'nis' => '2425' . str_pad($i + 100, 4, '0', STR_PAD_LEFT),
                'nama' => $firstName . ' ' . $lastName,
                'email' => $faker->unique()->safeEmail,
                'tanggal_lahir' => $faker->date('Y-m-d', '2010-12-31'),
                'tempat_lahir' => $faker->city,
                'jenis_kelamin' => $gender,
                'alamat' => $faker->address,
                'telepon' => '08' . $faker->numerify('##########'),
                'status' => 'aktif',
                'tahun_masuk' => 2024
            ]);
        }

        $this->command->info('Successfully created 30 students using Faker ID.');
    }
}
