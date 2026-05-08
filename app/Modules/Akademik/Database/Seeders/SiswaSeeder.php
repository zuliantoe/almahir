<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Siswa\Models\Siswa;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        Schema::disableForeignKeyConstraints();
        Siswa::truncate();
        Schema::enableForeignKeyConstraints();

        for ($i = 1; $i <= 40; $i++) {
            Siswa::create([
                'nis' => 'SIS' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama' => $faker->name,
                'status' => 'aktif',
                'jenis_kelamin' => $i % 2 == 0 ? 'L' : 'P',
                'tanggal_lahir' => '2010-01-01',
                'tempat_lahir' => $faker->city,
                'alamat' => $faker->address,
                'tahun_masuk' => 2024,
            ]);
        }
    }
}
