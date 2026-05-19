<?php

namespace App\Modules\Akademik\Database\Seeders;

use App\Models\User;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\JenisKegiatan;
use App\Modules\Akademik\Models\KalenderAkademik;
use App\Modules\Akademik\Models\KategoriPelajaran;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Kurikulum;
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

class MassiveAkademikSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        $this->command->info('🚀 Memulai Massive Academic Seeding...');
        
        Schema::disableForeignKeyConstraints();
        $this->truncateTables();
        Schema::enableForeignKeyConstraints();

        // 1. Tahun Ajaran
        $this->command->info('1. Seeding Tahun Ajaran...');
        $taPast = TahunAjaran::create(['tahunajaran' => '2023/2024', 'status' => 0, 'keterangan' => 'Tahun Ajaran Masa Lalu']);
        $taActive = TahunAjaran::create(['tahunajaran' => '2024/2025', 'status' => 1, 'keterangan' => 'Tahun Ajaran Aktif Saat Ini']);
        $taFuture = TahunAjaran::create(['tahunajaran' => '2025/2026', 'status' => 0, 'keterangan' => 'Tahun Ajaran Masa Depan']);

        // 2. Tingkat & Kelas
        $this->command->info('2. Seeding Tingkat & Kelas...');
        $tingkats = [
            'X'   => 'Sepuluh',
            'XI'  => 'Sebelas',
            'XII' => 'Duabelas'
        ];
        
        $kelasData = [];
        foreach ($tingkats as $kode => $nama) {
            $t = Tingkat::create(['kode_tingkat' => $kode, 'nama_tingkat' => "Tingkat $nama"]);
            
            // Buat 2 kelas per tingkat
            for ($i = 1; $i <= 2; $i++) {
                $namaKelas = "$kode IPA $i";
                $kelasData[] = Kelas::create([
                    'nama_kelas' => $namaKelas,
                    'kode_kelas' => str_replace(' ', '', $namaKelas),
                    'tingkat_id' => $t->id
                ]);
            }
        }

        // 3. Kurikulum & Mata Pelajaran
        $this->command->info('3. Seeding Kurikulum & Mapel...');
        $mk = MasterKurikulum::create(['nama_kurikulum' => 'Kurikulum Merdeka Belajar', 'status' => 1]);
        
        $katUmum = KategoriPelajaran::create(['kategori' => 'Muatan Umum']);
        $katAgama = KategoriPelajaran::create(['kategori' => 'Muatan Agama']);
        $katLokal = KategoriPelajaran::create(['kategori' => 'Muatan Lokal']);

        $mapels = [
            ['nama' => 'Matematika', 'kode' => 'MTK', 'kat' => $katUmum->id],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'BIN', 'kat' => $katUmum->id],
            ['nama' => 'Bahasa Inggris', 'kode' => 'BIG', 'kat' => $katUmum->id],
            ['nama' => 'Fisika', 'kode' => 'FIS', 'kat' => $katUmum->id],
            ['nama' => 'Biologi', 'kode' => 'BIO', 'kat' => $katUmum->id],
            ['nama' => 'Pendidikan Agama Islam', 'kode' => 'PAI', 'kat' => $katAgama->id],
            ['nama' => 'Al-Qur\'an Hadits', 'kode' => 'AQH', 'kat' => $katAgama->id],
            ['nama' => 'Fiqh', 'kode' => 'FQH', 'kat' => $katAgama->id],
            ['nama' => 'Bahasa Arab', 'kode' => 'ARB', 'kat' => $katLokal->id],
            ['nama' => 'Tahfidz', 'kode' => 'THZ', 'kat' => $katLokal->id],
        ];

        $mapelIds = [];
        foreach ($mapels as $m) {
            $createdMapel = MataPelajaran::create([
                'nama' => $m['nama'],
                'kode' => $m['kode'],
                'kategori_id' => $m['kat'],
                'kelompok' => 'Wajib'
            ]);
            $mapelIds[] = $createdMapel->id;

            // Link to Kurikulum for all classes in active year
            foreach ($kelasData as $kelas) {
                Kurikulum::create([
                    'master_kurikulum_id' => $mk->id,
                    'kelas_id' => $kelas->id,
                    'mapel_id' => $createdMapel->id,
                    'tahunajaran_id' => $taActive->id,
                    'tingkat_id' => $kelas->tingkat_id,
                    'totaljam' => 2,
                    'kkm' => 75
                ]);
            }
        }

        // 4. Guru & Siswa
        $this->command->info('4. Seeding Guru & Siswa...');
        $guruIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $nip = '19' . $faker->numerify('#########');
            $guru = Guru::create([
                'nama' => $faker->name . ', S.Pd',
                'nip' => $nip,
                'email' => $faker->unique()->safeEmail,
                'status' => 'aktif',
                'jabatan' => 'Guru Pengajar'
            ]);
            $guruIds[] = $guru->id;

            User::create([
                'username' => $nip,
                'name' => $guru->nama,
                'email' => $guru->email,
                'password' => Hash::make('password'),
                'ref_type' => Guru::class,
                'ref_id' => $guru->id
            ])->assignRole('GURU');
        }

        $siswaIds = [];
        for ($i = 1; $i <= 60; $i++) {
            $nis = $faker->unique()->numerify('2425####');
            $siswa = Siswa::create([
                'nama' => $faker->name,
                'nis' => $nis,
                'email' => $faker->unique()->safeEmail,
                'status' => 'aktif',
                'tahun_masuk' => 2024
            ]);
            $siswaIds[] = $siswa->id;

            User::create([
                'username' => $nis,
                'name' => $siswa->nama,
                'email' => $siswa->email,
                'password' => Hash::make('password'),
                'ref_type' => Siswa::class,
                'ref_id' => $siswa->id
            ])->assignRole('SISWA');
        }

        // 5. Rombel & Jadwal
        $this->command->info('5. Seeding Rombel & Jadwal...');
        $rombels = [];
        foreach ($kelasData as $index => $kelas) {
            $rombel = Rombel::create([
                'nama_rombel' => "Rombel " . $kelas->nama_kelas,
                'kelas_id' => $kelas->id,
                'tahunajaran_id' => $taActive->id,
                'tingkat_id' => $kelas->tingkat_id,
                'guru_id' => $guruIds[$index % 10], // Wali kelas
                'keterangan' => 'Kelas reguler angkatan 2024'
            ]);
            $rombels[] = $rombel;

            // Masukkan 10 siswa per rombel
            $start = $index * 10;
            for ($j = 0; $j < 10; $j++) {
                if (isset($siswaIds[$start + $j])) {
                    RombelSiswa::create([
                        'rombel_id' => $rombel->id,
                        'siswa_id' => $siswaIds[$start + $j],
                        'tahunajaran_id' => $taActive->id,
                        'kelas_id' => $kelas->id,
                        'status' => 'aktif'
                    ]);
                }
            }

            // Buat Jadwal Pelajaran (Senin s/d Jumat, 2 jam per hari)
            $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            foreach ($haris as $hIndex => $hari) {
                JadwalPelajaran::create([
                    'rombel_id' => $rombel->id,
                    'hari' => $hari,
                    'jamke' => 1,
                    'jamawal' => '07:30:00',
                    'jamakhir' => '09:00:00',
                    'mapel_id' => $mapelIds[($index + $hIndex) % count($mapelIds)],
                    'guru_id' => $guruIds[($index + $hIndex) % 10]
                ]);
            }
        }

        // 6. Kalender Akademik
        $this->command->info('6. Seeding Kalender Akademik...');
        $jkKbm = JenisKegiatan::create(['jeniskegiatan' => 'Kegiatan Belajar Mengajar', 'warna' => '#4e73df', 'is_kbm' => 1]);
        $jkLibur = JenisKegiatan::create(['jeniskegiatan' => 'Libur Nasional', 'warna' => '#e74a3b', 'is_kbm' => 0]);
        $jkUjian = JenisKegiatan::create(['jeniskegiatan' => 'Ujian Akhir Semester', 'warna' => '#f6c23e', 'is_kbm' => 0]);

        $events = [
            ['nama' => 'Awal Semester Ganjil', 'jk' => $jkKbm->id, 'start' => '2024-07-15', 'end' => '2024-07-15'],
            ['nama' => 'HUT Kemerdekaan RI', 'jk' => $jkLibur->id, 'start' => '2024-08-17', 'end' => '2024-08-17'],
            ['nama' => 'Ujian Akhir Semester', 'jk' => $jkUjian->id, 'start' => '2024-12-01', 'end' => '2024-12-10'],
        ];

        foreach ($events as $ev) {
            KalenderAkademik::create([
                'nama_kegiatan' => $ev['nama'],
                'kegiatan_id' => $ev['jk'],
                'tahunajaran_id' => $taActive->id,
                'semester' => 'Ganjil',
                'tanggal_awal' => $ev['start'],
                'tanggal_akhir' => $ev['end'],
                'deskripsi' => 'Agenda rutin tahunan sekolah.'
            ]);
        }

        $this->command->info('✅ Massive Academic Seeding Selesai!');
    }

    private function truncateTables()
    {
        $this->command->warn('Cleaning old data...');
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
        
        DB::table('sys_users')->where(function($q) {
            $q->whereIn('username', DB::table('guru')->pluck('nip'))
              ->orWhereIn('username', DB::table('siswa')->pluck('nis'));
        })->delete();

        Guru::truncate();
        Siswa::truncate();
    }
}
