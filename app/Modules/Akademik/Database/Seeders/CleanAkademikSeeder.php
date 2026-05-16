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
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;

class CleanAkademikSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('=============================================');
        $this->command->info('Membersihkan data lama...');
        
        \Schema::disableForeignKeyConstraints();
        
        // Truncate tables
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
        
        // Optional: Clean Guru & Siswa if you want a TRULY fresh start
        // Warning: This will delete users as well
        DB::table('sys_users')->where(function($q) {
            $q->whereIn('username', DB::table('guru')->pluck('nip'))
              ->orWhereIn('username', DB::table('siswa')->pluck('nis'));
        })->delete();
        
        Guru::truncate();
        Siswa::truncate();

        \Schema::enableForeignKeyConstraints();

        $this->command->info('Memulai Seeding Akademik (Clean & Terstruktur)');
        $this->command->info('=============================================');

        $taActive = $this->seedTahunAjaran();
        $tingkats = $this->seedTingkat();
        $kelases  = $this->seedKelas($tingkats);
        
        $masterKurikulum = $this->seedMasterKurikulum();
        $kategoriMapel   = $this->seedKategoriPelajaran();
        $mapels          = $this->seedMataPelajaran($kategoriMapel);
        
        $this->seedKurikulum($kelases, $mapels, $masterKurikulum, $taActive);
        
        $jenisKegiatans = $this->seedJenisKegiatan();
        $this->seedKalenderAkademik($jenisKegiatans, $taActive);

        list($gurus, $siswas) = $this->seedGuruDanSiswa();

        $rombels = $this->seedRombel($kelases, $taActive, $gurus);
        $this->seedRombelSiswa($rombels, $siswas, $taActive);
        
        $this->seedJadwalPelajaran($rombels, $mapels, $gurus);

        $this->command->info('✅ Seeding Akademik Selesai! Semua data terisi dengan rapi.');
    }

    private function seedTahunAjaran()
    {
        $this->command->info('- Seeding Tahun Ajaran...');
        // Non-aktif
        TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2023/2024'],
            ['status' => 0, 'keterangan' => 'Tahun Ajaran Lalu']
        );
        // Aktif
        $ta = TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2024/2025'],
            ['status' => 1, 'keterangan' => 'Tahun Ajaran Saat Ini']
        );
        // Depan
        TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2025/2026'],
            ['status' => 0, 'keterangan' => 'Tahun Ajaran Mendatang']
        );
        return $ta;
    }

    private function seedTingkat()
    {
        $this->command->info('- Seeding Tingkat (Jenjang)...');
        $data = [
            'X'   => 'Sepuluh',
            'XI'  => 'Sebelas',
            'XII' => 'Duabelas'
        ];
        $result = [];
        foreach ($data as $kode => $nama) {
            $result[$kode] = Tingkat::updateOrCreate(
                ['kode_tingkat' => $kode],
                ['nama_tingkat' => "Tingkat $nama"]
            );
        }
        return $result;
    }

    private function seedKelas($tingkats)
    {
        $this->command->info('- Seeding Kelas...');
        $result = [];
        $jurusans = ['IPA 1', 'IPS 1'];
        foreach ($tingkats as $kode => $tingkat) {
            foreach ($jurusans as $jurusan) {
                $namaKelas = "$kode $jurusan";
                $result[] = Kelas::updateOrCreate(
                    ['nama_kelas' => $namaKelas],
                    [
                        'kode_kelas' => str_replace(' ', '', $namaKelas),
                        'tingkat_id' => $tingkat->id
                    ]
                );
            }
        }
        return collect($result);
    }

    private function seedMasterKurikulum()
    {
        $this->command->info('- Seeding Master Kurikulum...');
        return MasterKurikulum::updateOrCreate(
            ['nama_kurikulum' => 'Kurikulum Merdeka Belajar'],
            ['status' => 1]
        );
    }

    private function seedKategoriPelajaran()
    {
        $this->command->info('- Seeding Kategori Pelajaran...');
        $kategoriNasional = KategoriPelajaran::updateOrCreate(
            ['kategori' => 'Nasional'], []
        );
        $kategoriLokal = KategoriPelajaran::updateOrCreate(
            ['kategori' => 'Muatan Lokal'], []
        );
        return ['nasional' => $kategoriNasional, 'lokal' => $kategoriLokal];
    }

    private function seedMataPelajaran($kategori)
    {
        $this->command->info('- Seeding Mata Pelajaran...');
        $data = [
            ['kode' => 'PAI', 'nama' => 'Pendidikan Agama Islam', 'kat' => $kategori['nasional']->id],
            ['kode' => 'PKN', 'nama' => 'Pendidikan Pancasila', 'kat' => $kategori['nasional']->id],
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia', 'kat' => $kategori['nasional']->id],
            ['kode' => 'MTK', 'nama' => 'Matematika', 'kat' => $kategori['nasional']->id],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris', 'kat' => $kategori['nasional']->id],
            ['kode' => 'BDH', 'nama' => 'Bahasa Daerah', 'kat' => $kategori['lokal']->id],
        ];

        $result = [];
        foreach ($data as $item) {
            $result[] = MataPelajaran::updateOrCreate(
                ['kode' => $item['kode']],
                ['nama' => $item['nama'], 'kategori_id' => $item['kat'], 'kelompok' => 'A']
            );
        }
        return collect($result);
    }

    private function seedKurikulum($kelases, $mapels, $masterKurikulum, $ta)
    {
        $this->command->info('- Seeding Detail Kurikulum...');
        foreach ($kelases as $kelas) {
            foreach ($mapels as $mapel) {
                Kurikulum::updateOrCreate(
                    [
                        'master_kurikulum_id' => $masterKurikulum->id,
                        'kelas_id' => $kelas->id,
                        'mapel_id' => $mapel->id,
                        'tahunajaran_id' => $ta->id
                    ],
                    [
                        'tingkat_id' => $kelas->tingkat_id,
                        'totaljam' => 4, // 4 JP per minggu
                        'kkm' => 75
                    ]
                );
            }
        }
    }

    private function seedJenisKegiatan()
    {
        $this->command->info('- Seeding Jenis Kegiatan...');
        $kbm = JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'Hari Efektif KBM'], ['warna' => '#4e73df', 'is_kbm' => 1]);
        $libur = JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'Libur Nasional'], ['warna' => '#e74a3b', 'is_kbm' => 0]);
        $ujian = JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'Ujian Semester'], ['warna' => '#f6c23e', 'is_kbm' => 0]);
        
        return ['kbm' => $kbm, 'libur' => $libur, 'ujian' => $ujian];
    }

    private function seedKalenderAkademik($jk, $ta)
    {
        $this->command->info('- Seeding Kalender Akademik...');
        // Simulasi beberapa event di bulan Juli/Agustus
        $year = substr($ta->tahunajaran, 0, 4);
        
        KalenderAkademik::updateOrCreate(
            ['nama_kegiatan' => 'Hari Pertama Masuk Sekolah', 'tahunajaran_id' => $ta->id, 'semester' => 'Ganjil'],
            ['kegiatan_id' => $jk['kbm']->id, 'tanggal_awal' => "$year-07-15", 'tanggal_akhir' => "$year-07-15", 'deskripsi' => 'MPLS dan perkenalan.']
        );
        KalenderAkademik::updateOrCreate(
            ['nama_kegiatan' => 'HUT Kemerdekaan RI', 'tahunajaran_id' => $ta->id, 'semester' => 'Ganjil'],
            ['kegiatan_id' => $jk['libur']->id, 'tanggal_awal' => "$year-08-17", 'tanggal_akhir' => "$year-08-17", 'deskripsi' => 'Upacara bendera & libur nasional.']
        );
        KalenderAkademik::updateOrCreate(
            ['nama_kegiatan' => 'Ujian Tengah Semester', 'tahunajaran_id' => $ta->id, 'semester' => 'Ganjil'],
            ['kegiatan_id' => $jk['ujian']->id, 'tanggal_awal' => "$year-09-20", 'tanggal_akhir' => "$year-09-25", 'deskripsi' => 'Pelaksanaan UTS Ganjil.']
        );
    }

    private function seedGuruDanSiswa()
    {
        $this->command->info('- Menyiapkan Data Guru & Siswa...');
        
        // Buat 6 Guru (karena kita punya 6 Kelas)
        $gurus = collect();
        for ($i = 1; $i <= 6; $i++) {
            $nip = '198001012010011' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $guru = Guru::updateOrCreate(
                ['nip' => $nip],
                [
                    'nama' => "Guru Teladan $i, S.Pd",
                    'email' => "guruteladan$i@sekolah.sch.id",
                    'status' => 'aktif',
                    'jabatan' => 'Guru Tetap'
                ]
            );
            $gurus->push($guru);

            User::updateOrCreate(
                ['username' => $nip],
                ['name' => $guru->nama, 'email' => $guru->email, 'password' => Hash::make('password'), 'ref_type' => Guru::class, 'ref_id' => $guru->id]
            )->assignRole('GURU');
        }

        // Buat 60 Siswa (10 per rombel)
        $siswas = collect();
        for ($i = 1; $i <= 60; $i++) {
            $nis = '2425' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $siswa = Siswa::updateOrCreate(
                ['nis' => $nis],
                [
                    'nama' => "Siswa Prestasi $i",
                    'email' => "siswaprestasi$i@siswa.sch.id",
                    'status' => 'aktif',
                    'tahun_masuk' => 2024
                ]
            );
            $siswas->push($siswa);

            User::updateOrCreate(
                ['username' => $nis],
                ['name' => $siswa->nama, 'email' => $siswa->email, 'password' => Hash::make('password'), 'ref_type' => Siswa::class, 'ref_id' => $siswa->id]
            )->assignRole('SISWA');
        }

        return [$gurus, $siswas];
    }

    private function seedRombel($kelases, $ta, $gurus)
    {
        $this->command->info('- Seeding Rombongan Belajar (Rombel)...');
        $rombels = collect();
        $guruIndex = 0;
        
        foreach ($kelases as $kelas) {
            $rombel = Rombel::updateOrCreate(
                [
                    'kelas_id' => $kelas->id,
                    'tahunajaran_id' => $ta->id
                ],
                [
                    'nama_rombel' => "Rombel " . $kelas->nama_kelas,
                    'tingkat_id' => $kelas->tingkat_id,
                    'guru_id' => $gurus[$guruIndex]->id ?? $gurus[0]->id, // Wali kelas
                    'keterangan' => 'Rombel reguler'
                ]
            );
            $rombels->push($rombel);
            $guruIndex++;
        }
        return $rombels;
    }

    private function seedRombelSiswa($rombels, $siswas, $ta)
    {
        $this->command->info('- Memasukkan Siswa ke dalam Rombel...');
        
        // Membagi siswa secara merata ke rombel
        $chunks = $siswas->chunk(ceil($siswas->count() / $rombels->count()));
        
        foreach ($rombels as $index => $rombel) {
            if (isset($chunks[$index])) {
                foreach ($chunks[$index] as $siswa) {
                    RombelSiswa::updateOrCreate(
                        [
                            'rombel_id' => $rombel->id,
                            'siswa_id' => $siswa->id,
                            'tahunajaran_id' => $ta->id
                        ],
                        [
                            'kelas_id' => $rombel->kelas_id,
                            'status' => 'aktif'
                        ]
                    );
                }
            }
        }
    }

    private function seedJadwalPelajaran($rombels, $mapels, $gurus)
    {
        $this->command->info('- Seeding Jadwal Pelajaran...');
        
        // Buat jadwal sederhana di hari Senin & Selasa untuk tiap rombel
        $hari = ['Senin', 'Selasa'];
        $sesi = [
            ['jamke' => 1, 'jamawal' => '07:30:00', 'jamakhir' => '08:15:00'],
            ['jamke' => 2, 'jamawal' => '08:15:00', 'jamakhir' => '09:00:00'],
        ];

        foreach ($rombels as $rombel) {
            foreach ($hari as $h) {
                foreach ($sesi as $s) {
                    $randomMapel = $mapels->random();
                    $randomGuru = $gurus->random();
                    
                    JadwalPelajaran::updateOrCreate(
                        [
                            'rombel_id' => $rombel->id,
                            'hari' => $h,
                            'jamke' => $s['jamke']
                        ],
                        [
                            'jamawal' => $s['jamawal'],
                            'jamakhir' => $s['jamakhir'],
                            'mapel_id' => $randomMapel->id,
                            'guru_id' => $randomGuru->id
                        ]
                    );
                }
            }
        }
    }
}
