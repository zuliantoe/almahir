<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Siswa\Models\Siswa;
use App\Modules\Akademik\Models\Kelas;
use Illuminate\Support\Facades\Schema;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Don't disable foreign key constraints globally, just be careful
        
        $faker = \Faker\Factory::create('id_ID');
        
        // Get some classes if they exist
        $kelasIds = Kelas::pluck('id')->toArray();
        
        $this->command->info('Seeding 50 students...');

        for ($i = 0; $i < 50; $i++) {
            $gender = $faker->randomElement(['L', 'P']);
            $firstName = ($gender == 'L') ? $faker->firstNameMale() : $faker->firstNameFemale();
            $lastName = $faker->lastName();
            $name = $firstName . ' ' . $lastName;
            
            Siswa::create([
                'nis' => $faker->unique()->numerify('##########'), // 10 digit NIS
                'nama' => $name,
                'email' => strtolower($firstName . '.' . $lastName . $faker->numberBetween(10, 99) . '@student.almahir.sch.id'),
                'tanggal_lahir' => $faker->date('Y-m-d', '2012-12-31'),
                'tempat_lahir' => $faker->city(),
                'jenis_kelamin' => $gender,
                'alamat' => $faker->address(),
                'telepon' => $faker->phoneNumber(),
                'kelas_id' => !empty($kelasIds) ? $faker->randomElement($kelasIds) : null,
                'tahun_masuk' => $faker->numberBetween(2022, 2024),
                'status' => 'aktif',
            ]);
        }

        $this->command->info('✓ 50 students seeded successfully.');
    }
}
