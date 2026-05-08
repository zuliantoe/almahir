<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use Modules\Siswa\Models\Siswa;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class FillRombelSiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $rombels = Rombel::all();

        if ($rombels->isEmpty()) {
            $this->command->error("Tidak ada data Rombel. Pastikan Anda sudah menjalankan seeder Rombel terlebih dahulu.");
            return;
        }

        $this->command->info("Memulai pengisian siswa ke dalam " . $rombels->count() . " Rombel...");

        foreach ($rombels as $rombel) {
            // Tentukan target jumlah siswa (antara 10 - 15)
            $targetSiswa = rand(10, 15);
            $currentSiswaCount = $rombel->siswa()->count();
            
            $needsAdding = $targetSiswa - $currentSiswaCount;

            if ($needsAdding <= 0) {
                $this->command->info("- Rombel {$rombel->nama_rombel} sudah memiliki {$currentSiswaCount} siswa. Skip.");
                continue;
            }

            $this->command->info("- Menambahkan {$needsAdding} siswa ke Rombel: {$rombel->nama_rombel}");

            for ($i = 0; $i < $needsAdding; $i++) {
                $jk = $faker->randomElement(['L', 'P']);
                
                // 1. Buat Data Siswa Baru (Cek email & NIS unik di DB)
                $email = $faker->unique()->safeEmail;
                while (User::where('email', $email)->exists() || Siswa::where('email', $email)->exists()) {
                    $email = $faker->unique()->safeEmail;
                }

                $nis = $faker->unique()->numerify('2425####');
                while (Siswa::where('nis', $nis)->exists()) {
                    $nis = $faker->unique()->numerify('2425####');
                }

                $siswa = Siswa::create([
                    'nis' => $nis,
                    'nama' => $faker->name($jk == 'L' ? 'male' : 'female'),
                    'email' => $email,
                    'jenis_kelamin' => $jk,
                    'tanggal_lahir' => $faker->date('Y-m-d', '-15 years'),
                    'tempat_lahir' => $faker->city,
                    'alamat' => $faker->address,
                    'tahun_masuk' => 2024,
                    'status' => 'aktif',
                ]);

                // 2. Hubungkan ke Rombel
                RombelSiswa::create([
                    'rombel_id' => $rombel->id,
                    'siswa_id' => $siswa->id,
                    'status' => 'aktif'
                ]);

                // 3. Buat Akun User Siswa
                $username = 'siswa.' . $siswa->nis;
                $user = User::create([
                    'name' => $siswa->nama,
                    'username' => $username,
                    'email' => $siswa->email,
                    'password' => Hash::make('password'),
                    'ref_type' => Siswa::class,
                    'ref_id' => $siswa->id,
                    'account_status' => 'active',
                ]);
                
                // Assign Role SISWA (jika trait HasRoles tersedia)
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('SISWA');
                }
            }
        }

        $this->command->info("Selesai! Seluruh Rombel kini memiliki 10-15 siswa.");
    }
}
