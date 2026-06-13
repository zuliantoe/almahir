<?php

namespace App\Modules\Akademik\Database\Seeders;

use App\Models\User;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\JenisKegiatan;
use App\Modules\Akademik\Models\KalenderAkademik;
use App\Modules\Akademik\Models\KategoriPelajaran;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Kurikulum;
use App\Modules\Akademik\Models\MasterJamPelajaran;
use App\Modules\Akademik\Models\MasterKurikulum;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Tingkat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use Faker\Factory as Faker;

class DemoAkademikSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        $this->command->info('=============================================');
        $this->command->info('🧹 Membersihkan data akademik lama...');
        $this->command->info('=============================================');

        Schema::disableForeignKeyConstraints();
        
        // Truncate all akademik tables
        JadwalPelajaran::truncate();
        RombelSiswa::truncate();
        Rombel::truncate();
        Kurikulum::truncate();
        MataPelajaran::truncate();
        KategoriPelajaran::truncate();
        MasterKurikulum::truncate();
        Kelas::truncate();
        Tingkat::truncate();
        KalenderAkademik::truncate();
        JenisKegiatan::truncate();
        TahunAjaran::truncate();
        MasterJamPelajaran::truncate();

        // Clean Guru and Siswa user accounts to avoid conflict
        DB::table('sys_users')->where(function($q) {
            $q->whereIn('username', DB::table('guru')->pluck('nip')->map(fn($val) => 'guru.' . $val))
              ->orWhereIn('username', DB::table('siswa')->pluck('nis')->map(fn($val) => 'siswa.' . $val));
        })->delete();

        Guru::truncate();
        Siswa::truncate();

        Schema::enableForeignKeyConstraints();

        $this->command->info('🚀 Memulai Seeding Data Demo Akademik...');
        $this->command->info('=============================================');

        // 1. Seed Tahun Ajaran
        $this->command->info('- Seeding 2 Tahun Ajaran...');
        $taPast = TahunAjaran::create([
            'tahunajaran' => '2024/2025', 
            'status' => 0, 
            'keterangan' => 'Tahun Ajaran Lalu'
        ]);
        $taActive = TahunAjaran::create([
            'tahunajaran' => '2025/2026', 
            'status' => 1, 
            'keterangan' => 'Tahun Ajaran Aktif Saat Ini'
        ]);

        // 2. Seed Tingkat
        $this->command->info('- Seeding Tingkat...');
        $tingkats = [
            '10' => Tingkat::create(['kode_tingkat' => '10', 'nama_tingkat' => 'Kelas 10']),
            '11' => Tingkat::create(['kode_tingkat' => '11', 'nama_tingkat' => 'Kelas 11']),
            '12' => Tingkat::create(['kode_tingkat' => '12', 'nama_tingkat' => 'Kelas 12'])
        ];

        // 3. Seed Kelas
        $this->command->info('- Seeding Kelas...');
        $kelases = [
            '10-A' => Kelas::create(['kode_kelas' => '10-A', 'nama_kelas' => '10-A', 'tingkat_id' => $tingkats['10']->id]),
            '10-B' => Kelas::create(['kode_kelas' => '10-B', 'nama_kelas' => '10-B', 'tingkat_id' => $tingkats['10']->id]),
            '11-A' => Kelas::create(['kode_kelas' => '11-A', 'nama_kelas' => '11-A', 'tingkat_id' => $tingkats['11']->id]),
            '11-B' => Kelas::create(['kode_kelas' => '11-B', 'nama_kelas' => '11-B', 'tingkat_id' => $tingkats['11']->id]),
            '12-A' => Kelas::create(['kode_kelas' => '12-A', 'nama_kelas' => '12-A', 'tingkat_id' => $tingkats['12']->id]),
            '12-B' => Kelas::create(['kode_kelas' => '12-B', 'nama_kelas' => '12-B', 'tingkat_id' => $tingkats['12']->id])
        ];

        // 4. Seed Kurikulum & Mata Pelajaran
        $this->command->info('- Seeding Kurikulum & Mapel...');
        $mk = MasterKurikulum::create(['nama_kurikulum' => 'Kurikulum Merdeka', 'status' => 1]);
        $katNasional = KategoriPelajaran::create(['kategori' => 'Nasional']);
        $katLokal = KategoriPelajaran::create(['kategori' => 'Muatan Lokal']);

        $mapelList = [
            ['nama' => 'Matematika', 'kode' => 'MTK', 'kat' => $katNasional->id],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'BIN', 'kat' => $katNasional->id],
            ['nama' => 'Bahasa Inggris', 'kode' => 'BIG', 'kat' => $katNasional->id],
            ['nama' => 'Fisika', 'kode' => 'FIS', 'kat' => $katNasional->id],
            ['nama' => 'Pendidikan Agama Islam', 'kode' => 'PAI', 'kat' => $katNasional->id],
            ['nama' => 'Bahasa Arab', 'kode' => 'ARB', 'kat' => $katLokal->id],
            ['nama' => 'Tahfidz Al-Qur\'an', 'kode' => 'THZ', 'kat' => $katLokal->id],
        ];

        $mapels = collect();
        foreach ($mapelList as $m) {
            $mapel = MataPelajaran::create([
                'nama' => $m['nama'],
                'kode' => $m['kode'],
                'kategori_id' => $m['kat'],
                'kelompok' => 'Wajib'
            ]);
            $mapels->push($mapel);

            // Detail Kurikulum untuk kedua tahun ajaran
            foreach ($kelases as $kelas) {
                foreach ([$taPast, $taActive] as $ta) {
                    Kurikulum::create([
                        'master_kurikulum_id' => $mk->id,
                        'kelas_id' => $kelas->id,
                        'mapel_id' => $mapel->id,
                        'tahunajaran_id' => $ta->id,
                        'tingkat_id' => $kelas->tingkat_id,
                        'totaljam' => 4,
                        'kkm' => 75
                    ]);
                }
            }
        }

        // 5. Seed Master Jam Pelajaran (Senin s/d Sabtu)
        $this->command->info('- Seeding Master Jam Pelajaran...');
        $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jams = [
            ['jamke' => 1, 'jamawal' => '07:30:00', 'jamakhir' => '08:15:00', 'is_istirahat' => false],
            ['jamke' => 2, 'jamawal' => '08:15:00', 'jamakhir' => '09:00:00', 'is_istirahat' => false],
            ['jamke' => 3, 'jamawal' => '09:00:00', 'jamakhir' => '09:45:00', 'is_istirahat' => false],
            ['jamke' => 4, 'jamawal' => '09:45:00', 'jamakhir' => '10:15:00', 'is_istirahat' => true],
            ['jamke' => 5, 'jamawal' => '10:15:00', 'jamakhir' => '11:00:00', 'is_istirahat' => false],
            ['jamke' => 6, 'jamawal' => '11:00:00', 'jamakhir' => '11:45:00', 'is_istirahat' => false],
            ['jamke' => 7, 'jamawal' => '11:45:00', 'jamakhir' => '12:30:00', 'is_istirahat' => false],
        ];
        foreach ($haris as $hari) {
            foreach ($jams as $jam) {
                MasterJamPelajaran::create([
                    'hari' => $hari,
                    'jamke' => $jam['jamke'],
                    'jamawal' => $jam['jamawal'],
                    'jamakhir' => $jam['jamakhir'],
                    'is_istirahat' => $jam['is_istirahat'],
                ]);
            }
        }

        // 6. Seed Guru (15 orang)
        $this->command->info('- Seeding Guru dan Akun Pengguna...');
        $gurus = collect();
        for ($i = 1; $i <= 15; $i++) {
            $nip = '198' . $faker->unique()->numerify('#######') . str_pad($i, 2, '0', STR_PAD_LEFT);
            $nama = $faker->name . ', S.Pd';
            $email = 'guru' . $i . '@almahir.sch.id';
            $jk = $faker->randomElement(['L', 'P']);

            $guru = Guru::create([
                'nama' => $nama,
                'nip' => $nip,
                'tanggal_lahir' => $faker->date('Y-m-d', '1995-01-01'),
                'tempat_lahir' => $faker->city,
                'jenis_kelamin' => $jk,
                'alamat' => $faker->address,
                'status' => 'aktif',
            ]);
            $gurus->push($guru);

            $user = User::create([
                'name' => $guru->nama,
                'username' => 'guru.' . $guru->nip,
                'email' => $email,
                'password' => Hash::make('password'),
                'ref_type' => Guru::class,
                'ref_id' => $guru->id,
                'account_status' => 'active'
            ]);
            $user->assignRole('GURU');
        }

        // 7. Seed Siswa (80 orang)
        $this->command->info('- Seeding Siswa dan Akun Pengguna...');
        $siswas = collect();
        for ($i = 1; $i <= 80; $i++) {
            $nis = '2024' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $nama = $faker->name;
            $email = 'siswa' . $i . '@student.almahir.sch.id';
            $jk = $faker->randomElement(['L', 'P']);

            $siswa = Siswa::create([
                'nama' => $nama,
                'nis' => $nis,
                'email' => $email,
                'tanggal_lahir' => $faker->date('Y-m-d', '2010-01-01'),
                'tempat_lahir' => $faker->city,
                'jenis_kelamin' => $jk,
                'alamat' => $faker->address,
                'telepon' => $faker->phoneNumber,
                'tahun_masuk' => 2024,
                'status' => 'aktif',
            ]);
            $siswas->push($siswa);

            $user = User::create([
                'name' => $siswa->nama,
                'username' => 'siswa.' . $siswa->nis,
                'email' => $email,
                'password' => Hash::make('password'),
                'ref_type' => Siswa::class,
                'ref_id' => $siswa->id,
                'account_status' => 'active'
            ]);
            $user->assignRole('SISWA');
        }

        // 8. Seed Rombel untuk kedua Tahun Ajaran
        $this->command->info('- Seeding Rombel...');
        
        // Rombel 2024/2025 (Tahun Lalu)
        $rombelPast = [
            '10-A' => Rombel::create(['nama_rombel' => 'X-A (2024)', 'kelas_id' => $kelases['10-A']->id, 'tingkat_id' => $tingkats['10']->id, 'tahunajaran_id' => $taPast->id, 'guru_id' => $gurus[0]->id]),
            '10-B' => Rombel::create(['nama_rombel' => 'X-B (2024)', 'kelas_id' => $kelases['10-B']->id, 'tingkat_id' => $tingkats['10']->id, 'tahunajaran_id' => $taPast->id, 'guru_id' => $gurus[1]->id]),
            '11-A' => Rombel::create(['nama_rombel' => 'XI-A (2024)', 'kelas_id' => $kelases['11-A']->id, 'tingkat_id' => $tingkats['11']->id, 'tahunajaran_id' => $taPast->id, 'guru_id' => $gurus[2]->id]),
            '11-B' => Rombel::create(['nama_rombel' => 'XI-B (2024)', 'kelas_id' => $kelases['11-B']->id, 'tingkat_id' => $tingkats['11']->id, 'tahunajaran_id' => $taPast->id, 'guru_id' => $gurus[3]->id]),
            '12-A' => Rombel::create(['nama_rombel' => 'XII-A (2024)', 'kelas_id' => $kelases['12-A']->id, 'tingkat_id' => $tingkats['12']->id, 'tahunajaran_id' => $taPast->id, 'guru_id' => $gurus[4]->id]),
            '12-B' => Rombel::create(['nama_rombel' => 'XII-B (2024)', 'kelas_id' => $kelases['12-B']->id, 'tingkat_id' => $tingkats['12']->id, 'tahunajaran_id' => $taPast->id, 'guru_id' => $gurus[5]->id])
        ];

        // Rombel 2025/2026 (Tahun Sekarang)
        $rombelActive = [
            '10-A' => Rombel::create(['nama_rombel' => 'X-A', 'kelas_id' => $kelases['10-A']->id, 'tingkat_id' => $tingkats['10']->id, 'tahunajaran_id' => $taActive->id, 'guru_id' => $gurus[6]->id]),
            '10-B' => Rombel::create(['nama_rombel' => 'X-B', 'kelas_id' => $kelases['10-B']->id, 'tingkat_id' => $tingkats['10']->id, 'tahunajaran_id' => $taActive->id, 'guru_id' => $gurus[7]->id]),
            '11-A' => Rombel::create(['nama_rombel' => 'XI-A', 'kelas_id' => $kelases['11-A']->id, 'tingkat_id' => $tingkats['11']->id, 'tahunajaran_id' => $taActive->id, 'guru_id' => $gurus[8]->id]),
            '11-B' => Rombel::create(['nama_rombel' => 'XI-B', 'kelas_id' => $kelases['11-B']->id, 'tingkat_id' => $tingkats['11']->id, 'tahunajaran_id' => $taActive->id, 'guru_id' => $gurus[9]->id]),
            '12-A' => Rombel::create(['nama_rombel' => 'XII-A', 'kelas_id' => $kelases['12-A']->id, 'tingkat_id' => $tingkats['12']->id, 'tahunajaran_id' => $taActive->id, 'guru_id' => $gurus[10]->id]),
            '12-B' => Rombel::create(['nama_rombel' => 'XII-B', 'kelas_id' => $kelases['12-B']->id, 'tingkat_id' => $tingkats['12']->id, 'tahunajaran_id' => $taActive->id, 'guru_id' => $gurus[11]->id])
        ];

        // 9. Distribusi Siswa ke Rombel (RombelSiswa)
        $this->command->info('- Distribusi Siswa & Setup Simulasi Kenaikan Kelas...');
        
        // == TAHUN LALU (2024/2025) ==
        // Siswa 1 s/d 10 -> X-A 2024
        // Siswa 11 s/d 20 -> X-B 2024
        // Siswa 21 s/d 30 -> XI-A 2024
        // Siswa 31 s/d 40 -> XI-B 2024
        // Siswa 41 s/d 50 -> XII-A 2024 (Lulus)
        // Siswa 51 s/d 60 -> XII-B 2024 (Lulus)
        for ($i = 0; $i < 60; $i++) {
            $siswa = $siswas[$i];
            if ($i < 10)       $r = $rombelPast['10-A'];
            elseif ($i < 20)  $r = $rombelPast['10-B'];
            elseif ($i < 30)  $r = $rombelPast['11-A'];
            elseif ($i < 40)  $r = $rombelPast['11-B'];
            elseif ($i < 50)  $r = $rombelPast['12-A'];
            else              $r = $rombelPast['12-B'];

            RombelSiswa::create([
                'rombel_id' => $r->id,
                'siswa_id' => $siswa->id,
                'tahunajaran_id' => $taPast->id,
                'kelas_id' => $r->kelas_id,
                'status' => 'aktif'
            ]);
        }

        // == TAHUN SEKARANG (2025/2026) ==
        // Siswa Baru (Siswa 61 s/d 70) -> X-A
        // Siswa Baru (Siswa 71 s/d 80) -> X-B
        for ($i = 60; $i < 70; $i++) {
            RombelSiswa::create([
                'rombel_id' => $rombelActive['10-A']->id,
                'siswa_id' => $siswas[$i]->id,
                'tahunajaran_id' => $taActive->id,
                'kelas_id' => $rombelActive['10-A']->kelas_id,
                'status' => 'aktif'
            ]);
        }
        for ($i = 70; $i < 80; $i++) {
            RombelSiswa::create([
                'rombel_id' => $rombelActive['10-B']->id,
                'siswa_id' => $siswas[$i]->id,
                'tahunajaran_id' => $taActive->id,
                'kelas_id' => $rombelActive['10-B']->kelas_id,
                'status' => 'aktif'
            ]);
        }

        // Kenaikan Kelas dari Tahun Lalu ke Tahun Sekarang:
        // 1. Siswa 1-10 dari X-A (2024) naik kelas ke XI-A (2025)
        for ($i = 0; $i < 10; $i++) {
            RombelSiswa::create([
                'rombel_id' => $rombelActive['11-A']->id,
                'siswa_id' => $siswas[$i]->id,
                'tahunajaran_id' => $taActive->id,
                'kelas_id' => $rombelActive['11-A']->kelas_id,
                'status' => 'aktif'
            ]);
        }

        // 2. ⚠️ ⚠️ TINGGALKAN XI-B (2025) KOSONG ⚠️ ⚠️
        // Ini disengaja agar pengguna dapat mempraktikkan fitur Kenaikan Kelas
        // dengan menaikkan Siswa 11-20 dari X-B (2024) ke XI-B (2025) secara langsung saat demo!

        // 3. Siswa 21-30 dari XI-A (2024) naik kelas ke XII-A (2025)
        for ($i = 20; $i < 30; $i++) {
            RombelSiswa::create([
                'rombel_id' => $rombelActive['12-A']->id,
                'siswa_id' => $siswas[$i]->id,
                'tahunajaran_id' => $taActive->id,
                'kelas_id' => $rombelActive['12-A']->kelas_id,
                'status' => 'aktif'
            ]);
        }

        // 4. Siswa 31-40 dari XI-B (2024) naik kelas ke XII-B (2025)
        for ($i = 30; $i < 40; $i++) {
            RombelSiswa::create([
                'rombel_id' => $rombelActive['12-B']->id,
                'siswa_id' => $siswas[$i]->id,
                'tahunajaran_id' => $taActive->id,
                'kelas_id' => $rombelActive['12-B']->kelas_id,
                'status' => 'aktif'
            ]);
        }

        // 10. Seed Jadwal Pelajaran untuk kedua Tahun Ajaran (Senin s/d Jumat)
        $this->command->info('- Seeding Jadwal Pelajaran (Senin s/d Jumat)...');
        $activeDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        // Jamke efektif (skipping istirahat jam 4)
        $effectiveJamKes = [1, 2, 3, 5, 6, 7];

        $allRombels = array_merge(array_values($rombelPast), array_values($rombelActive));
        foreach ($allRombels as $rIndex => $rombel) {
            foreach ($activeDays as $dIndex => $hari) {
                // Berikan 3 jam pelajaran per hari untuk demo schedule terisi rapi
                for ($k = 0; $k < 3; $k++) {
                    $jamke = $effectiveJamKes[$k];
                    $jamData = collect($jams)->firstWhere('jamke', $jamke);
                    
                    $randomMapel = $mapels[($rIndex + $dIndex + $k) % $mapels->count()];
                    $randomGuru = $gurus[($rIndex + $dIndex + $k) % $gurus->count()];

                    JadwalPelajaran::create([
                        'rombel_id' => $rombel->id,
                        'hari' => $hari,
                        'jamke' => $jamke,
                        'jamawal' => $jamData['jamawal'],
                        'jamakhir' => $jamData['jamakhir'],
                        'mapel_id' => $randomMapel->id,
                        'guru_id' => $randomGuru->id
                    ]);
                }
            }
        }

        // 11. Seed Kalender Akademik
        $this->command->info('- Seeding Agenda Kalender Akademik...');
        $jkKbm = JenisKegiatan::create(['jeniskegiatan' => 'Hari Efektif KBM', 'warna' => '#4e73df', 'is_kbm' => 1]);
        $jkLibur = JenisKegiatan::create(['jeniskegiatan' => 'Libur Nasional', 'warna' => '#e74a3b', 'is_kbm' => 0]);
        $jkUjian = JenisKegiatan::create(['jeniskegiatan' => 'Ujian Tengah/Akhir Semester', 'warna' => '#f6c23e', 'is_kbm' => 0]);

        // Events 2024/2025
        KalenderAkademik::create([
            'nama_kegiatan' => 'Awal KBM Ganjil 2024',
            'kegiatan_id' => $jkKbm->id,
            'tahunajaran_id' => $taPast->id,
            'semester' => 'Ganjil',
            'tanggal_awal' => '2024-07-15',
            'tanggal_akhir' => '2024-07-17',
            'deskripsi' => 'MPLS & Awal Pembelajaran Efektif.'
        ]);
        KalenderAkademik::create([
            'nama_kegiatan' => 'Ujian Akhir Semester Ganjil 2024',
            'kegiatan_id' => $jkUjian->id,
            'tahunajaran_id' => $taPast->id,
            'semester' => 'Ganjil',
            'tanggal_awal' => '2024-12-02',
            'tanggal_akhir' => '2024-12-07',
            'deskripsi' => 'UAS Semester Ganjil.'
        ]);

        // Events 2025/2026
        KalenderAkademik::create([
            'nama_kegiatan' => 'Awal KBM Ganjil 2025',
            'kegiatan_id' => $jkKbm->id,
            'tahunajaran_id' => $taActive->id,
            'semester' => 'Ganjil',
            'tanggal_awal' => '2025-07-14',
            'tanggal_akhir' => '2025-07-16',
            'deskripsi' => 'MPLS & Awal Pembelajaran Efektif.'
        ]);
        KalenderAkademik::create([
            'nama_kegiatan' => 'Ujian Akhir Semester Ganjil 2025',
            'kegiatan_id' => $jkUjian->id,
            'tahunajaran_id' => $taActive->id,
            'semester' => 'Ganjil',
            'tanggal_awal' => '2025-12-01',
            'tanggal_akhir' => '2025-12-06',
            'deskripsi' => 'UAS Semester Ganjil.'
        ]);

        $this->command->info('=============================================');
        $this->command->info('✅ DemoAkademikSeeder berhasil dijalankan!');
        $this->command->info('=============================================');
    }
}
