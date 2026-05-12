<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WaliMurid\Models\WaliMurid;
use Modules\Siswa\Models\Siswa;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WaliMuridDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil 10 siswa pertama (yang sudah punya akun dari seeder pertama)
        $students = Siswa::whereHas('user')->limit(10)->get();

        if ($students->isEmpty()) {
            $this->command->error('Tidak ada siswa yang memiliki akun user. Silakan jalankan SiswaDataSeeder terlebih dahulu.');
            return;
        }

        $this->command->info('Menciptakan 10 data Wali Murid dan menghubungkannya dengan Siswa...');

        foreach ($students as $index => $siswa) {
            $namaWali = $faker->name('male');
            $emailWali = strtolower(str_replace(' ', '.', $namaWali)) . '@wali.local';

            $wali = WaliMurid::create([
                'nama' => $namaWali,
                'email' => $emailWali,
                'telepon' => $faker->phoneNumber,
                'alamat' => $siswa->alamat,
                'pekerjaan' => $faker->jobTitle,
                'hubungan' => 'ayah', // Hubungan ada di tabel wali_murid, bukan di pivot
            ]);

            // Hubungkan dengan siswa di tabel pivot (tanpa kolom hubungan karena sudah didrop)
            DB::table('siswa_wali')->insert([
                'id' => (string) Str::uuid(),
                'siswa_id' => $siswa->id,
                'wali_murid_id' => $wali->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info("✓ Wali: {$namaWali} -> Siswa: {$siswa->nama}");
        }
    }
}
