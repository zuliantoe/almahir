<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\MasterKurikulum;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\JenisKegiatan;
use App\Modules\Akademik\Models\KalenderAkademik;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Tingkat;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\KategoriPelajaran;
use Modules\Guru\Models\Guru;
use Faker\Factory as Faker;

class SpecificAcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        Schema::disableForeignKeyConstraints();

        // 1. Tahun Ajaran (2025/2026 Genap & 2026/2027)
        $ta2526 = TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2025/2026'],
            ['status' => true]
        );

        $ta2627 = TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2026/2027'],
            ['status' => false]
        );

        // 2. Master Kurikulum (5 items)
        $kurikulumNames = ['Kurikulum Merdeka v1', 'Kurikulum 2013 Revisi', 'Kurikulum Internal Pesantren', 'Kurikulum Tahfidz Intensif', 'Kurikulum Bahasa Arab'];
        foreach ($kurikulumNames as $name) {
            MasterKurikulum::updateOrCreate(['nama_kurikulum' => $name], ['status' => true]);
        }

        // 3. Mata Pelajaran (3 items)
        $katDiniyyah = KategoriPelajaran::updateOrCreate(['kategori' => 'Diniyyah'], []);
        $mapelData = [
            ['kode' => 'MAT', 'nama' => 'Matematika'],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'THZ', 'nama' => 'Tahfidz Al-Quran'],
        ];
        $mapelObjects = [];
        foreach ($mapelData as $m) {
            $mapelObjects[] = MataPelajaran::updateOrCreate(
                ['kode' => $m['kode']],
                ['nama' => $m['nama'], 'kategori_id' => $katDiniyyah->id]
            );
        }

        // 4. Jenis Kegiatan (5 items)
        $jenisKegiatanData = [
            ['nama' => 'KBM', 'is_kbm' => true, 'warna' => '#4CAF50'],
            ['nama' => 'Ujian', 'is_kbm' => false, 'warna' => '#F44336'],
            ['nama' => 'Libur', 'is_kbm' => false, 'warna' => '#9E9E9E'],
            ['nama' => 'Ekskul', 'is_kbm' => false, 'warna' => '#2196F3'],
            ['nama' => 'Rapat', 'is_kbm' => false, 'warna' => '#FF9800'],
        ];
        $jkObjects = [];
        foreach ($jenisKegiatanData as $jk) {
            $jkObjects[] = JenisKegiatan::updateOrCreate(
                ['jeniskegiatan' => $jk['nama']],
                ['is_kbm' => $jk['is_kbm'], 'warna' => $jk['warna']]
            );
        }

        // 5. Kalender Akademik (3 events specific dates)
        // Event 1: 6 Mei - 10 Mei 2026
        // Event 2: 11 Mei - 13 Mei 2026
        // Event 3: 15 Mei - 20 Mei 2026
        $events = [
            [
                'nama' => 'Pekan Kreativitas Siswa',
                'jk' => $jkObjects[3]->id, // Ekskul
                'awal' => '2026-05-06',
                'akhir' => '2026-05-10',
                'ket' => 'Kegiatan lomba dan kreativitas siswa'
            ],
            [
                'nama' => 'Persiapan Ujian Akhir',
                'jk' => $jkObjects[0]->id, // KBM
                'awal' => '2026-05-11',
                'akhir' => '2026-05-13',
                'ket' => 'Review materi pelajaran'
            ],
            [
                'nama' => 'Libur Akhir Semester',
                'jk' => $jkObjects[2]->id, // Libur
                'awal' => '2026-05-15',
                'akhir' => '2026-05-20',
                'ket' => 'Libur kenaikan kelas'
            ],
        ];

        foreach ($events as $e) {
            KalenderAkademik::updateOrCreate(
                [
                    'tahunajaran_id' => $ta2526->id,
                    'nama_kegiatan' => $e['nama'],
                    'tanggal_awal' => $e['awal']
                ],
                [
                    'kegiatan_id' => $e['jk'],
                    'tanggal_akhir' => $e['akhir'],
                    'deskripsi' => $e['ket']
                ]
            );
        }

        // 6. Kelas (20 items)
        $tingkatCodes = ['10', '11', '12'];
        $tingkatObjects = [];
        foreach ($tingkatCodes as $code) {
            $tingkatObjects[] = Tingkat::updateOrCreate(['kode_tingkat' => $code], ['nama_tingkat' => "Kelas $code"]);
        }

        // Get one guru for seeding
        $guru = Guru::first() ?: Guru::create([
            'nama' => 'Guru Testing',
            'nip' => 'TEST001',
            'status' => 'aktif'
        ]);

        $kelasObjects = [];
        for ($i = 1; $i <= 20; $i++) {
            $tingkat = $tingkatObjects[$i % 3];
            $kelas = Kelas::updateOrCreate(
                ['nama_kelas' => "Kelas " . $tingkat->kode_tingkat . " - " . chr(64 + ($i > 13 ? $i-13 : $i))],
                [
                    'kode_kelas' => "KLS" . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'tingkat_id' => $tingkat->id
                ]
            );
            $kelasObjects[] = $kelas;

            // Create Rombel for the class
            $rombel = Rombel::updateOrCreate(
                ['kelas_id' => $kelas->id, 'tahunajaran_id' => $ta2526->id],
                [
                    'nama_rombel' => 'Rombel ' . $kelas->nama_kelas,
                    'guru_id' => $guru->id,
                ]
            );

            // 7. Jadwal Pelajaran (for each rombel)
            $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            foreach ($hariList as $hari) {
                JadwalPelajaran::updateOrCreate(
                    ['rombel_id' => $rombel->id, 'hari' => $hari, 'jamke' => 1],
                    [
                        'jamawal' => '07:00:00',
                        'jamakhir' => '08:30:00',
                        'mapel_id' => $mapelObjects[array_rand($mapelObjects)]->id,
                        'guru_id' => $guru->id
                    ]
                );
            }
        }

        Schema::enableForeignKeyConstraints();
        echo "Successfully seeded specific academic data requested.\n";
    }
}
