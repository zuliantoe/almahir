<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Kelas;
use Modules\Siswa\Models\Siswa;
use Modules\Guru\Models\Guru;
use Faker\Factory as Faker;

class FinalRombelSiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        Schema::disableForeignKeyConstraints();

        // 1. Pastikan ada Tahun Ajaran Aktif
        $ta = TahunAjaran::where('status', true)->first();
        if (!$ta) {
            $ta = TahunAjaran::create([
                'tahunajaran' => '2025/2026',
                'semester' => 'Genap',
                'status' => true
            ]);
        }

        // 2. Ambil atau Buat Guru sebagai Wali Kelas
        $guru = Guru::first() ?: Guru::create([
            'nama' => 'Ustadz Ahmad',
            'nip' => 'G101',
            'status' => 'aktif'
        ]);

        // 3. Buat 2 Rombel Contoh
        $rombels = [];
        $kelasNames = ['Kelas 12-A', 'Kelas 12-B'];
        foreach ($kelasNames as $name) {
            $kelas = Kelas::updateOrCreate(['nama_kelas' => $name], ['kode_kelas' => str_replace(' ', '', $name)]);
            $rombels[] = Rombel::updateOrCreate(
                ['kelas_id' => $kelas->id, 'tahunajaran_id' => $ta->id],
                [
                    'nama_rombel' => 'Rombel ' . $name,
                    'guru_id' => $guru->id,
                    'keterangan' => 'Rombel untuk testing kelulusan'
                ]
            );
        }

        // 4. Buat Siswa dan Masukkan ke Rombel
        foreach ($rombels as $rombel) {
            $this->command->info("Seeding siswa untuk " . $rombel->nama_rombel);
            
            for ($i = 1; $i <= 10; $i++) {
                // Buat Siswa Baru
                $siswa = Siswa::create([
                    'nis' => $faker->unique()->numerify('2526####'),
                    'nama' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'status' => 'aktif',
                    'tahun_masuk' => 2023
                ]);

                // Acak status: 70% aktif, 30% lulus
                $status = ($i > 7) ? 'lulus' : 'aktif';
                
                // Jika di pivot 'lulus', di tabel siswa juga set 'lulus'
                if ($status == 'lulus') {
                    $siswa->update(['status' => 'lulus']);
                }

                RombelSiswa::create([
                    'rombel_id' => $rombel->id,
                    'siswa_id' => $siswa->id,
                    'status' => $status
                ]);
            }
        }

        Schema::enableForeignKeyConstraints();
        $this->command->info("Berhasil melakukan seeding Rombel dengan Siswa (Aktif & Lulus).");
    }
}
