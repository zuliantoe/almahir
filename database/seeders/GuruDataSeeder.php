<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Guru\Models\Guru;
use Faker\Factory as Faker;

class GuruDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $this->command->info('Menciptakan data Guru dan akunnya...');

        $gurus = [
            [
                'nama' => 'Budi Santoso, S.Pd',
                'nip' => '199001012015011001',
                'email' => 'budi.guru@siakad.local',
                'jk' => 'L'
            ],
            [
                'nama' => 'Siti Aminah, M.Pd',
                'nip' => '199205122017052002',
                'email' => 'siti.guru@siakad.local',
                'jk' => 'P'
            ],
            [
                'nama' => 'Agus Hermawan, S.T',
                'nip' => '198508202010081003',
                'email' => 'agus.guru@siakad.local',
                'jk' => 'L'
            ],
            [
                'nama' => 'Dewi Lestari, S.S',
                'nip' => '198803152012032004',
                'email' => 'dewi.guru@siakad.local',
                'jk' => 'P'
            ],
            [
                'nama' => 'Ahmad Fauzi, M.Ag',
                'nip' => '198211302008111005',
                'email' => 'ahmad.guru@siakad.local',
                'jk' => 'L'
            ],
        ];

        foreach ($gurus as $data) {
            // Tentukan email jika belum ada
            $email = $data['email'] ?? (strtolower(str_replace(' ', '.', $data['nama'])) . '@siakad.local');
            $username = 'guru.' . $data['nip'];

            // 1. Buat Data Guru (Orang)
            $guru = Guru::updateOrCreate(
                ['nip' => $data['nip']],
                [
                    'nama' => $data['nama'],
                    'tanggal_lahir' => $faker->date('Y-m-d', '1995-01-01'),
                    'tempat_lahir' => $faker->city,
                    'jenis_kelamin' => $data['jk'],
                    'alamat' => $faker->address,
                    'status' => 'aktif',
                ]
            );

            // 2. Buat Akun User
            $user = User::where('ref_type', Guru::class)
                ->where('ref_id', $guru->id)
                ->first();

            if (!$user) {
                // Pastikan email user unik
                $userEmail = $email;
                if (User::where('email', $userEmail)->exists()) {
                    $userEmail = 'user.' . $email;
                }

                $user = new User();
                $user->forceFill([
                    'name' => $guru->nama,
                    'username' => $username,
                    'email' => $userEmail,
                    'password' => Hash::make('password'),
                    'ref_type' => Guru::class,
                    'ref_id' => $guru->id,
                    'account_status' => 'active',
                ]);
                $user->save();

                // Assign Role GURU
                $user->assignRole('GURU');
                $this->command->info("✓ Akun Guru dibuat: {$userEmail} | Username: {$username} | pass: password");
            } else {
                $this->command->warn("! Akun untuk Guru {$guru->nama} sudah ada.");
            }
        }
    }
}
