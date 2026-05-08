<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Tingkat;
use App\Modules\Akademik\Models\KategoriPelajaran;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\JenisKegiatan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\JadwalPelajaran;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use Faker\Factory as Faker;

class MassiveAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        Schema::disableForeignKeyConstraints();

        $this->command->info('Memulai seeding data akademik masif...');

        // 1. Tahun Ajaran
        $tahun = TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2025/2026'],
            ['semester' => 'Genap', 'status' => true]
        );

        // 2. Tingkat
        $tingkatCodes = ['10', '11', '12'];
        $tingkatObjects = [];
        foreach ($tingkatCodes as $code) {
            $tingkatObjects[] = Tingkat::updateOrCreate(
                ['kode_tingkat' => $code],
                ['nama_tingkat' => "Kelas $code"]
            );
        }

        // 3. Kategori Pelajaran
        $katMandiri = KategoriPelajaran::updateOrCreate(['kategori' => 'Internal'], []);
        $katNasional = KategoriPelajaran::updateOrCreate(['kategori' => 'Nasional'], []);

        // 4. Mata Pelajaran (15 Mata Pelajaran)
        $mapelData = [
            ['kode' => 'MP001', 'nama' => 'Matematika', 'kat' => $katNasional, 'kel' => 'A'],
            ['kode' => 'MP002', 'nama' => 'Bahasa Inggris', 'kat' => $katNasional, 'kel' => 'A'],
            ['kode' => 'MP003', 'nama' => 'Fisika', 'kat' => $katNasional, 'kel' => 'C'],
            ['kode' => 'MP004', 'nama' => 'Pendidikan Agama', 'kat' => $katMandiri, 'kel' => 'A'],
            ['kode' => 'MP005', 'nama' => 'Bahasa Arab', 'kat' => $katMandiri, 'kel' => 'B'],
            ['kode' => 'MP006', 'nama' => 'Tahfidz Al-Quran', 'kat' => $katMandiri, 'kel' => 'B'],
            ['kode' => 'MP007', 'nama' => 'Biologi', 'kat' => $katNasional, 'kel' => 'C'],
            ['kode' => 'MP008', 'nama' => 'Kimia', 'kat' => $katNasional, 'kel' => 'C'],
            ['kode' => 'MP009', 'nama' => 'Sejarah', 'kat' => $katNasional, 'kel' => 'A'],
            ['kode' => 'MP010', 'nama' => 'Ekonomi', 'kat' => $katNasional, 'kel' => 'C'],
            ['kode' => 'MP011', 'nama' => 'Geografi', 'kat' => $katNasional, 'kel' => 'C'],
            ['kode' => 'MP012', 'nama' => 'Sosiologi', 'kat' => $katNasional, 'kel' => 'C'],
            ['kode' => 'MP013', 'nama' => 'Bahasa Indonesia', 'kat' => $katNasional, 'kel' => 'A'],
            ['kode' => 'MP014', 'nama' => 'PJOK', 'kat' => $katNasional, 'kel' => 'B'],
            ['kode' => 'MP015', 'nama' => 'Seni Budaya', 'kat' => $katNasional, 'kel' => 'B'],
        ];

        $mapelObjects = [];
        foreach ($mapelData as $m) {
            $mapelObjects[] = MataPelajaran::updateOrCreate(
                ['kode' => $m['kode']],
                [
                    'nama' => $m['nama'], 
                    'kategori_id' => $m['kat']->id,
                    'kelompok' => $m['kel']
                ]
            );
        }

        // 5. Jenis Kegiatan
        $jkKbm = JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'KBM'], ['deskripsi' => 'Kegiatan Belajar Mengajar', 'is_kbm' => true, 'warna' => '#4CAF50']);
        $jkUjian = JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'Ujian'], ['deskripsi' => 'Ujian Akhir/Tengah Semester', 'is_kbm' => false, 'warna' => '#F44336']);
        $jkEkskul = JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'Ekskul'], ['deskripsi' => 'Ekstrakurikuler', 'is_kbm' => false, 'warna' => '#2196F3']);
        $jkLibur = JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'Libur'], ['deskripsi' => 'Hari Libur Nasional/Sekolah', 'is_kbm' => false, 'warna' => '#9E9E9E']);

        // 5.1 Kalender Akademik (Semester Genap 2025/2026)
        $events = [
            [
                'nama' => 'Masa Orientasi Siswa Baru',
                'id' => $jkEkskul->id,
                'awal' => '2026-01-06',
                'akhir' => '2026-01-08',
                'ket' => 'Orientasi untuk siswa baru semester genap'
            ],
            [
                'nama' => 'KBM Semester Genap Tahap 1',
                'id' => $jkKbm->id,
                'awal' => '2026-01-09',
                'akhir' => '2026-03-07',
                'ket' => 'Kegiatan belajar mengajar rutin'
            ],
            [
                'nama' => 'Ujian Tengah Semester (UTS)',
                'id' => $jkUjian->id,
                'awal' => '2026-03-10',
                'akhir' => '2026-03-14',
                'ket' => 'Ujian evaluasi tengah semester'
            ],
            [
                'nama' => 'Libur Ramadhan & Idul Fitri',
                'id' => $jkLibur->id,
                'awal' => '2026-03-24',
                'akhir' => '2026-04-04',
                'ket' => 'Libur menyambut hari raya'
            ],
            [
                'nama' => 'KBM Semester Genap Tahap 2',
                'id' => $jkKbm->id,
                'awal' => '2026-04-07',
                'akhir' => '2026-06-06',
                'ket' => 'Kegiatan belajar mengajar pasca lebaran'
            ],
            [
                'nama' => 'Ujian Akhir Semester (UAS)',
                'id' => $jkUjian->id,
                'awal' => '2026-06-09',
                'akhir' => '2026-06-13',
                'ket' => 'Ujian kenaikan kelas / kelulusan'
            ],
            [
                'nama' => 'Pekan Olahraga & Seni (PORSENI)',
                'id' => $jkEkskul->id,
                'awal' => '2026-05-05',
                'akhir' => '2026-05-09',
                'ket' => 'Kegiatan lomba antar kelas (Class Meeting)'
            ],
            [
                'nama' => 'Libur Akhir Tahun Pelajaran',
                'id' => $jkLibur->id,
                'awal' => '2026-06-23',
                'akhir' => '2026-07-11',
                'ket' => 'Libur panjang akhir tahun'
            ],
        ];

        foreach ($events as $event) {
            \App\Modules\Akademik\Models\KalenderAkademik::updateOrCreate(
                [
                    'tahunajaran_id' => $tahun->id,
                    'nama_kegiatan' => $event['nama'],
                    'tanggal_awal' => $event['awal'],
                ],
                [
                    'kegiatan_id' => $event['id'],
                    'tanggal_akhir' => $event['akhir'],
                    'deskripsi' => $event['ket'],
                ]
            );
        }

        // 6. Guru (30 Guru)
        $guruObjects = [];
        for ($i = 1; $i <= 30; $i++) {
            $nip = 'G' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $guru = Guru::updateOrCreate(
                ['nip' => $nip],
                [
                    'nama' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'status' => 'aktif',
                    'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                    'telepon' => $faker->phoneNumber,
                    'alamat' => $faker->address,
                ]
            );
            $guruObjects[] = $guru;

            // Buat Akun User Guru
            $username = 'guru.' . $guru->nip;
            $user = \App\Models\User::where('username', $username)->first();
            if (!$user) {
                $user = \App\Models\User::create([
                    'name' => $guru->nama,
                    'username' => $username,
                    'email' => $guru->email,
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'ref_type' => Guru::class,
                    'ref_id' => $guru->id,
                    'account_status' => 'active',
                ]);
                $user->assignRole('GURU');
            }
        }

        // 7. Kelas & Rombel (5 Kelas per Tingkat = 15 Rombel)
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamSlots = [
            ['jamke' => 1, 'awal' => '07:00:00', 'akhir' => '07:45:00'],
            ['jamke' => 2, 'awal' => '07:45:00', 'akhir' => '08:30:00'],
            ['jamke' => 3, 'awal' => '08:30:00', 'akhir' => '09:15:00'],
            ['jamke' => 4, 'awal' => '09:30:00', 'akhir' => '10:15:00'],
            ['jamke' => 5, 'awal' => '10:15:00', 'akhir' => '11:00:00'],
            ['jamke' => 6, 'awal' => '11:00:00', 'akhir' => '11:45:00'],
            ['jamke' => 7, 'awal' => '12:45:00', 'akhir' => '13:30:00'],
            ['jamke' => 8, 'awal' => '13:30:00', 'akhir' => '14:15:00'],
        ];

        foreach ($tingkatObjects as $tingkat) {
            for ($k = 1; $k <= 5; $k++) {
                $suffix = $faker->randomElement(['A', 'B', 'C', 'D']);
                $major = $faker->randomElement(['IPA', 'IPS', 'Tahfidz']);
                $namaKelas = $tingkat->nama_tingkat . ' ' . $suffix . ' (' . $major . ')';
                $kodeKelas = $tingkat->kode_tingkat . $suffix . $k;
                
                $kelas = Kelas::updateOrCreate(
                    ['nama_kelas' => $namaKelas],
                    [
                        'kode_kelas' => $kodeKelas,
                        'tingkat_id' => $tingkat->id,
                    ]
                );

                $rombel = Rombel::updateOrCreate(
                    ['kelas_id' => $kelas->id, 'tahunajaran_id' => $tahun->id],
                    [
                        'nama_rombel' => 'Rombel ' . $namaKelas,
                        'guru_id' => $guruObjects[array_rand($guruObjects)]->id,
                        'keterangan' => 'Rombel Unggulan ' . $major
                    ]
                );

                // 8. Siswa (20 Siswa per Rombel = Total 300 Siswa)
                for ($s = 1; $s <= 20; $s++) {
                    $nis = 'S' . $tingkat->kode_tingkat . str_pad($k, 2, '0', STR_PAD_LEFT) . str_pad($s, 2, '0', STR_PAD_LEFT);
                    $siswa = Siswa::updateOrCreate(
                        ['nis' => $nis],
                        [
                            'nama' => $faker->name,
                            'email' => $faker->unique()->safeEmail,
                            'status' => 'aktif',
                            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                            'tanggal_lahir' => $faker->date('Y-m-d', '-15 years'),
                            'tempat_lahir' => $faker->city,
                            'alamat' => $faker->address,
                            'tahun_masuk' => 2024
                        ]
                    );

                    RombelSiswa::updateOrCreate(
                        ['rombel_id' => $rombel->id, 'siswa_id' => $siswa->id]
                    );

                    // Buat Akun User Siswa
                    $username = 'siswa.' . $siswa->nis;
                    $user = \App\Models\User::where('username', $username)->first();
                    if (!$user) {
                        $user = \App\Models\User::create([
                            'name' => $siswa->nama,
                            'username' => $username,
                            'email' => $siswa->email,
                            'password' => \Illuminate\Support\Facades\Hash::make('password'),
                            'ref_type' => Siswa::class,
                            'ref_id' => $siswa->id,
                            'account_status' => 'active',
                        ]);
                        $user->assignRole('SISWA');
                    }
                }

                // 9. Jadwal Pelajaran (Full schedule 5 days x 8 periods)
                foreach ($hariList as $hari) {
                    foreach ($jamSlots as $slot) {
                        // Secara acak kosongkan beberapa jam (misal 15% kemungkinan kosong)
                        if (rand(1, 100) <= 15) continue;

                        $mapel = $mapelObjects[array_rand($mapelObjects)];
                        $guru = $guruObjects[array_rand($guruObjects)];

                        JadwalPelajaran::updateOrCreate(
                            [
                                'rombel_id' => $rombel->id, 
                                'hari' => $hari, 
                                'jamke' => $slot['jamke']
                            ],
                            [
                                'jamawal' => $slot['awal'],
                                'jamakhir' => $slot['akhir'],
                                'mapel_id' => $mapel->id,
                                'guru_id' => $guru->id
                            ]
                        );
                    }
                }
            }
        }

        Schema::enableForeignKeyConstraints();
        $this->command->info("Berhasil melakukan seeding masif:");
        $this->command->info("- 30 Guru");
        $this->command->info("- 15 Kelas & Rombel");
        $this->command->info("- 300 Siswa");
        $this->command->info("- ~600 Jadwal Pelajaran");
    }
}
