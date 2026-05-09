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
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use Faker\Factory as Faker;

class AcademicModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Tahun Ajaran
        $this->command->info('1. Seeding Tahun Ajaran...');
        $ta = TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2025/2026', 'semester' => 'Ganjil'],
            ['status' => 1, 'keterangan' => 'Tahun Ajaran Aktif']
        );
        TahunAjaran::updateOrCreate(
            ['tahunajaran' => '2025/2026', 'semester' => 'Genap'],
            ['status' => 0]
        );

        // 2. Tingkat
        $this->command->info('2. Seeding Tingkat...');
        $levels = ['X' => 'Sepuluh', 'XI' => 'Sebelas', 'XII' => 'Duabelas'];
        $tingkatIds = [];
        foreach ($levels as $kode => $nama) {
            $t = Tingkat::updateOrCreate(['kode_tingkat' => $kode], ['nama_tingkat' => "Tingkat $nama"]);
            $tingkatIds[$kode] = $t->id;
        }

        // 3. Kategori Pelajaran
        $this->command->info('3. Seeding Kategori Pelajaran...');
        $categories = [
            ['kategori' => 'Nasional', 'keterangan' => 'Mata pelajaran wajib nasional'],
            ['kategori' => 'Muatan Lokal', 'keterangan' => 'Mata pelajaran khusus daerah/sekolah'],
            ['kategori' => 'Peminatan', 'keterangan' => 'Mata pelajaran pilihan jurusan'],
        ];
        $catIds = [];
        foreach ($categories as $cat) {
            $c = KategoriPelajaran::updateOrCreate(['kategori' => $cat['kategori']]);
            $catIds[] = $c->id;
        }

        // 4. Mata Pelajaran
        $this->command->info('4. Seeding Mata Pelajaran...');
        $subjects = [
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia', 'cat' => $catIds[0]],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris', 'cat' => $catIds[0]],
            ['kode' => 'MTK', 'nama' => 'Matematika', 'cat' => $catIds[0]],
            ['kode' => 'PAI', 'nama' => 'Pendidikan Agama Islam', 'cat' => $catIds[0]],
            ['kode' => 'IPA', 'nama' => 'Ilmu Pengetahuan Alam', 'cat' => $catIds[0]],
            ['kode' => 'IPS', 'nama' => 'Ilmu Pengetahuan Sosial', 'cat' => $catIds[0]],
            ['kode' => 'ORG', 'nama' => 'Olahraga', 'cat' => $catIds[1]],
            ['kode' => 'ARB', 'nama' => 'Bahasa Arab', 'cat' => $catIds[1]],
        ];
        $mapelIds = [];
        foreach ($subjects as $s) {
            $m = MataPelajaran::updateOrCreate(
                ['kode' => $s['kode']],
                ['nama' => $s['nama'], 'kategori_id' => $s['cat'], 'kelompok' => 'A']
            );
            $mapelIds[] = $m->id;
        }

        // 5. Master Kurikulum
        $this->command->info('5. Seeding Master Kurikulum...');
        $mk = MasterKurikulum::updateOrCreate(
            ['nama_kurikulum' => 'Kurikulum Merdeka'],
            ['status' => 1]
        );

        // 6. Kelas
        $this->command->info('6. Seeding Kelas...');
        $classSuffixes = ['A', 'B', 'C'];
        $kelasIds = [];
        foreach ($tingkatIds as $kode => $tId) {
            foreach ($classSuffixes as $suffix) {
                $namaKelas = "$kode-$suffix";
                $k = Kelas::updateOrCreate(
                    ['nama_kelas' => $namaKelas],
                    ['kode_kelas' => str_replace('-', '', $namaKelas), 'tingkat_id' => $tId]
                );
                $kelasIds[] = $k->id;
            }
        }

        // 7. Kurikulum (Link subjects to classes)
        $this->command->info('7. Seeding Kurikulum (Junction)...');
        foreach ($kelasIds as $kId) {
            $kelas = Kelas::find($kId);
            foreach ($mapelIds as $mId) {
                Kurikulum::updateOrCreate(
                    ['master_kurikulum_id' => $mk->id, 'kelas_id' => $kId, 'mapel_id' => $mId, 'tahunajaran_id' => $ta->id],
                    ['tingkat_id' => $kelas->tingkat_id, 'totaljam' => 2, 'kkm' => 75]
                );
            }
        }

        // 8. Jenis Kegiatan
        $this->command->info('8. Seeding Jenis Kegiatan...');
        $activities = [
            ['jeniskegiatan' => 'KBM', 'warna' => '#4e73df', 'is_kbm' => 1],
            ['jeniskegiatan' => 'Libur Nasional', 'warna' => '#e74a3b', 'is_kbm' => 0],
            ['jeniskegiatan' => 'Ujian Tengah Semester', 'warna' => '#f6c23e', 'is_kbm' => 0],
            ['jeniskegiatan' => 'Ujian Akhir Semester', 'warna' => '#1cc88a', 'is_kbm' => 0],
            ['jeniskegiatan' => 'Rapat Guru', 'warna' => '#36b9cc', 'is_kbm' => 0],
        ];
        $jkIds = [];
        foreach ($activities as $act) {
            $jk = JenisKegiatan::updateOrCreate(
                ['jeniskegiatan' => $act['jeniskegiatan']],
                ['warna' => $act['warna'], 'is_kbm' => $act['is_kbm']]
            );
            $jkIds[] = $jk->id;
        }

        // 9. Kalender Akademik
        $this->command->info('9. Seeding Kalender Akademik...');
        $events = [
            ['nama' => 'Awal Masuk Sekolah', 'jk' => $jkIds[0], 'start' => '2025-07-14', 'end' => '2025-07-14'],
            ['nama' => 'UTS Ganjil', 'jk' => $jkIds[2], 'start' => '2025-09-15', 'end' => '2025-09-20'],
            ['nama' => 'Libur Maulid', 'jk' => $jkIds[1], 'start' => '2025-09-16', 'end' => '2025-09-16'],
        ];
        foreach ($events as $ev) {
            KalenderAkademik::updateOrCreate(
                ['nama_kegiatan' => $ev['nama'], 'tanggal_awal' => $ev['start']],
                [
                    'kegiatan_id' => $ev['jk'], 
                    'tanggal_akhir' => $ev['end'], 
                    'tahunajaran_id' => $ta->id,
                    'deskripsi' => 'Seeded event description'
                ]
            );
        }

        // 10. Guru & Siswa (5 Guru, 20 Siswa for demo)
        $this->command->info('10. Seeding Guru & Siswa...');
        $guruIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $nip = '1990' . str_pad($i, 10, '0', STR_PAD_LEFT);
            $guru = Guru::updateOrCreate(
                ['nip' => $nip],
                [
                    'nama' => $faker->name . ', S.Pd',
                    'email' => "guru$i@siakad.local",
                    'status' => 'aktif',
                    'jabatan' => 'Guru Pengajar',
                ]
            );
            $guruIds[] = $guru->id;
            
            $user = User::updateOrCreate(
                ['username' => 'guru.' . $nip],
                ['name' => $guru->nama, 'email' => $guru->email, 'password' => Hash::make('password'), 'ref_type' => Guru::class, 'ref_id' => $guru->id]
            );
            $user->assignRole('GURU');
        }

        $siswaIds = [];
        for ($i = 1; $i <= 20; $i++) {
            $nis = '2025' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $siswa = Siswa::updateOrCreate(
                ['nis' => $nis],
                [
                    'nama' => $faker->name,
                    'email' => "siswa$i@siakad.local",
                    'status' => 'aktif',
                    'tahun_masuk' => 2025,
                ]
            );
            $siswaIds[] = $siswa->id;
            
            $user = User::updateOrCreate(
                ['username' => 'siswa.' . $nis],
                ['name' => $siswa->nama, 'email' => $siswa->email, 'password' => Hash::make('password'), 'ref_type' => Siswa::class, 'ref_id' => $siswa->id]
            );
            $user->assignRole('SISWA');
        }

        // 11. Rombel
        $this->command->info('11. Seeding Rombel...');
        $rombelIds = [];
        for ($i = 0; $i < 3; $i++) {
            $kId = $kelasIds[$i];
            $kelas = Kelas::find($kId);
            $rombel = Rombel::updateOrCreate(
                ['nama_rombel' => 'Rombel ' . $kelas->nama_kelas],
                ['kelas_id' => $kId, 'tahunajaran_id' => $ta->id, 'guru_id' => $guruIds[$i]]
            );
            $rombelIds[] = $rombel->id;

            // Assign students
            $chunks = array_chunk($siswaIds, 6);
            if (isset($chunks[$i])) {
                foreach ($chunks[$i] as $sId) {
                    RombelSiswa::updateOrCreate(
                        ['rombel_id' => $rombel->id, 'siswa_id' => $sId, 'tahunajaran_id' => $ta->id],
                        ['kelas_id' => $kId, 'status' => 'aktif']
                    );
                }
            }
        }

        // 12. JadwalPelajaran
        $this->command->info('12. Seeding Jadwal Pelajaran...');
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ($rombelIds as $rId) {
            foreach ($days as $day) {
                JadwalPelajaran::updateOrCreate(
                    ['rombel_id' => $rId, 'hari' => $day, 'jamke' => 1],
                    [
                        'jamawal' => '07:30:00',
                        'jamakhir' => '09:00:00',
                        'mapel_id' => $faker->randomElement($mapelIds),
                        'guru_id' => $faker->randomElement($guruIds)
                    ]
                );
            }
        }

        $this->command->info('✓ AcademicModuleSeeder: SEMUA HALAMAN BERHASIL DI-SEED!');
    }
}
