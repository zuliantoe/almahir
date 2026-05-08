<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Siswa\Models\Siswa;
use Faker\Factory as Faker;

class SiswaDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $this->command->info('Menciptakan data Siswa dan akunnya...');

        // Kita buat 10 siswa dummy
        for ($i = 1; $i <= 10; $i++) {
            $jk = $faker->randomElement(['L', 'P']);
            $nama = $faker->name($jk == 'L' ? 'male' : 'female');
            $nis = '2425' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $email = strtolower(str_replace(' ', '.', $nama)) . '@siakad.local';

            // 1. Buat Data Siswa (Orang)
            $siswa = Siswa::updateOrCreate(
                ['nis' => $nis],
                [
                    'nama' => $nama,
                    'email' => $email,
                    'tanggal_lahir' => $faker->date('Y-m-d', '2010-01-01'),
                    'tempat_lahir' => $faker->city,
                    'jenis_kelamin' => $jk,
                    'alamat' => $faker->address,
                    'telepon' => $faker->phoneNumber,
                    'tahun_masuk' => 2024,
                    'status' => 'aktif',
                ]
            );

            $username = 'siswa.' . $siswa->nis;

            // 2. Buat Akun User
            $user = User::where('ref_type', Siswa::class)
                ->where('ref_id', $siswa->id)
                ->first();

            if (!$user) {
                // Pastikan email unik
                $userEmail = $email;
                if (User::where('email', $userEmail)->exists()) {
                    $userEmail = 'user.' . $email;
                }

                $user = new User();
                $user->forceFill([
                    'name' => $siswa->nama,
                    'username' => $username,
                    'email' => $userEmail,
                    'password' => Hash::make('password'),
                    'ref_type' => Siswa::class,
                    'ref_id' => $siswa->id,
                    'account_status' => 'active',
                ]);
                $user->save();

                // Assign Role SISWA
                $user->assignRole('SISWA');
                $this->command->info("✓ Akun Siswa dibuat: {$userEmail} | Username: {$username} | pass: password");
            } else {
                $this->command->info("- Akun untuk Siswa {$siswa->nama} sudah ada.");
            }
        }
    }
}
