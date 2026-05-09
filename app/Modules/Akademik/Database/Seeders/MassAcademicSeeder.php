<?php

namespace App\Modules\Akademik\Database\Seeders;

use App\Models\User;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Tingkat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use Faker\Factory as Faker;

class MassAcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Pastikan ada Tahun Ajaran & Tingkat
        $ta = TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2025/2026', 'semester' => 'Ganjil'],
            ['status' => 1]
        );

        $levels = ['X', 'XI', 'XII'];
        $tingkatIds = [];
        foreach ($levels as $l) {
            $t = Tingkat::updateOrCreate(['kode_tingkat' => $l], ['nama_tingkat' => "Tingkat $l"]);
            $tingkatIds[] = $t->id;
        }

        // 2. Buat 10 Guru
        $this->command->info('Menciptakan 10 Guru...');
        $guruIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $jk = $faker->randomElement(['L', 'P']);
            $nama = $faker->name($jk == 'L' ? 'male' : 'female') . ', S.Pd';
            $nip = '199' . $faker->numerify('##########');
            
            $guru = Guru::updateOrCreate(
                ['nip' => $nip],
                [
                    'nama' => $nama,
                    'email' => $faker->unique()->email,
                    'tanggal_lahir' => $faker->date('Y-m-d', '1995-01-01'),
                    'tempat_lahir' => $faker->city,
                    'jenis_kelamin' => $jk,
                    'status' => 'aktif',
                    'jabatan' => 'Guru Pengajar',
                ]
            );
            $guruIds[] = $guru->id;

            // Create User Account
            $username = 'guru.' . $nip;
            $user = User::where('username', $username)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $nama,
                    'username' => $username,
                    'email' => $guru->email,
                    'password' => Hash::make('password'),
                    'ref_type' => Guru::class,
                    'ref_id' => $guru->id,
                    'account_status' => 'active',
                ]);
                $user->assignRole('GURU');
            }
        }

        // 3. Buat 50 Siswa
        $this->command->info('Menciptakan 50 Siswa...');
        $siswaIds = [];
        for ($i = 1; $i <= 50; $i++) {
            $jk = $faker->randomElement(['L', 'P']);
            $nama = $faker->name($jk == 'L' ? 'male' : 'female');
            $nis = '2025' . str_pad($i, 4, '0', STR_PAD_LEFT);
            
            $siswa = Siswa::updateOrCreate(
                ['nis' => $nis],
                [
                    'nama' => $nama,
                    'email' => $faker->unique()->email,
                    'tanggal_lahir' => $faker->date('Y-m-d', '2010-01-01'),
                    'tempat_lahir' => $faker->city,
                    'jenis_kelamin' => $jk,
                    'status' => 'aktif',
                    'tahun_masuk' => 2025,
                ]
            );
            $siswaIds[] = $siswa->id;

            // Create User Account
            $username = 'siswa.' . $nis;
            $user = User::where('username', $username)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $nama,
                    'username' => $username,
                    'email' => $siswa->email,
                    'password' => Hash::make('password'),
                    'ref_type' => Siswa::class,
                    'ref_id' => $siswa->id,
                    'account_status' => 'active',
                ]);
                $user->assignRole('SISWA');
            }
        }

        // 4. Buat 5 Rombel
        $this->command->info('Menciptakan 5 Rombel...');
        $classNames = ['A', 'B', 'C', 'D', 'E'];
        
        // Chunk students to distribute them (10 students per rombel)
        $siswaChunks = array_chunk($siswaIds, 10);

        for ($i = 0; $i < 5; $i++) {
            $tingkatId = $faker->randomElement($tingkatIds);
            $tingkatObj = Tingkat::find($tingkatId);
            
            $kelas = Kelas::updateOrCreate(
                ['nama_kelas' => $tingkatObj->kode_tingkat . '-' . $classNames[$i]],
                ['kode_kelas' => $tingkatObj->kode_tingkat . $classNames[$i], 'tingkat_id' => $tingkatId]
            );

            $rombel = Rombel::updateOrCreate(
                ['nama_rombel' => 'Rombel ' . $kelas->nama_kelas],
                [
                    'kelas_id' => $kelas->id,
                    'tahunajaran_id' => $ta->id,
                    'guru_id' => $faker->randomElement($guruIds), // Wali Kelas
                    'keterangan' => 'Seeded by MassAcademicSeeder (Module Version)'
                ]
            );

            // Assign 10 students to this rombel
            if (isset($siswaChunks[$i])) {
                foreach ($siswaChunks[$i] as $sId) {
                    RombelSiswa::updateOrCreate(
                        ['rombel_id' => $rombel->id, 'siswa_id' => $sId, 'tahunajaran_id' => $ta->id],
                        ['kelas_id' => $kelas->id, 'status' => 'aktif']
                    );
                }
            }
        }

        $this->command->info('✓ MassAcademicSeeder (Module) selesai!');
    }
}
