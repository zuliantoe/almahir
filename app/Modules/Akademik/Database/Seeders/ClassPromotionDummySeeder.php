<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Tingkat;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Rombel;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use App\Modules\Akademik\Models\RombelSiswa;
use Faker\Factory as Faker;

class ClassPromotionDummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        Schema::disableForeignKeyConstraints();

        $this->command->info('Creating dummy Rombel data for multiple years...');

        // 1. Tahun Ajaran
        $ta2024 = TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2024/2025', 'semester' => 'Genap'],
            ['status' => true] // Active
        );

        $ta2025 = TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2025/2026', 'semester' => 'Ganjil'],
            ['status' => true] // Active
        );

        // 2. Tingkat
        $tingkat10 = Tingkat::updateOrCreate(['kode_tingkat' => '10'], ['nama_tingkat' => 'Kelas 10']);
        $tingkat11 = Tingkat::updateOrCreate(['kode_tingkat' => '11'], ['nama_tingkat' => 'Kelas 11']);
        $tingkat12 = Tingkat::updateOrCreate(['kode_tingkat' => '12'], ['nama_tingkat' => 'Kelas 12']);

        // 3. Guru (ensure some exist)
        if (Guru::count() < 10) {
            for ($i = 0; $i < 10; $i++) {
                Guru::create([
                    'nip' => 'G' . $faker->unique()->numberBetween(1000, 9999),
                    'nama' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'status' => 'aktif'
                ]);
            }
        }
        $gurus = Guru::all();

        // 4. Create Rombels for 2024/2025 (Source Year)
        $this->createRombels($ta2024, [$tingkat10, $tingkat11, $tingkat12], $gurus, $faker);

        // 5. Create Rombels for 2025/2026 (Target Year)
        $this->createRombels($ta2025, [$tingkat10, $tingkat11, $tingkat12], $gurus, $faker);

        Schema::enableForeignKeyConstraints();
        $this->command->info('Rombel dummy seeding completed.');
    }

    private function createRombels($ta, $tingkats, $gurus, $faker)
    {
        foreach ($tingkats as $tingkat) {
            foreach (['A', 'B'] as $label) {
                $namaKelas = $tingkat->nama_tingkat . ' - ' . $label;
                $kelas = Kelas::updateOrCreate(
                    ['nama_kelas' => $namaKelas, 'tingkat_id' => $tingkat->id],
                    ['kode_kelas' => $tingkat->kode_tingkat . $label]
                );

                $rombel = Rombel::updateOrCreate(
                    ['kelas_id' => $kelas->id, 'tahunajaran_id' => $ta->id],
                    [
                        'nama_rombel' => '[' . $tingkat->nama_tingkat . '] ' . $namaKelas,
                        'guru_id' => $gurus->random()->id,
                        'keterangan' => 'Rombel ' . $ta->tahunajaran
                    ]
                );

                // Add some students if none exist in this rombel
                if ($rombel->siswa()->count() == 0) {
                    for ($i = 0; $i < 10; $i++) {
                        $siswa = Siswa::create([
                            'nis' => $faker->unique()->numberBetween(10000, 99999),
                            'nama' => $faker->name,
                            'email' => $faker->unique()->safeEmail,
                            'status' => 'aktif',
                            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                            'tanggal_lahir' => $faker->date('Y-m-d', '-15 years'),
                            'tempat_lahir' => $faker->city,
                            'tahun_masuk' => 2024
                        ]);

                        RombelSiswa::create([
                            'rombel_id' => $rombel->id,
                            'siswa_id' => $siswa->id,
                            'tahunajaran_id' => $ta->id,
                            'kelas_id' => $kelas->id,
                            'status' => 'aktif'
                        ]);
                    }
                }
            }
        }
    }
}
