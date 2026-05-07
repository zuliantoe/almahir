<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Guru\Models\Guru;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        Schema::disableForeignKeyConstraints();
        Guru::truncate();
        Schema::enableForeignKeyConstraints();

        for ($i = 1; $i <= 10; $i++) {
            Guru::create([
                'nip' => 'GUR' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama' => $faker->name,
                'status' => 'aktif',
                'jenis_kelamin' => $i % 2 == 0 ? 'L' : 'P',
                'telepon' => $faker->phoneNumber,
                'alamat' => $faker->address,
            ]);
        }
    }
}
