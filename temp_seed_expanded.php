<?php
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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

echo "--- Starting Seed process ---\n";

try {
    Schema::disableForeignKeyConstraints();

    // 1. Tahun Ajaran
    echo "Seeding Tahun Ajaran...\n";
    $tahun = TahunAjaran::updateOrCreate(
        ['tahunajaran' => '2024/2025'],
        ['semester' => 'Genap', 'status' => true]
    );

    // 2. Tingkat
    echo "Seeding Tingkat...\n";
    $t10 = Tingkat::updateOrCreate(['kode_tingkat' => '10'], ['nama_tingkat' => 'Kelas 10']);
    $t11 = Tingkat::updateOrCreate(['kode_tingkat' => '11'], ['nama_tingkat' => 'Kelas 11']);
    $t12 = Tingkat::updateOrCreate(['kode_tingkat' => '12'], ['nama_tingkat' => 'Kelas 12']);

    // 3. Kategori Pelajaran
    echo "Seeding Kategori Pelajaran...\n";
    $katMandiri = KategoriPelajaran::updateOrCreate(['kategori' => 'Internal'], ['deskripsi' => 'Kurikulum Internal']);
    $katNasional = KategoriPelajaran::updateOrCreate(['kategori' => 'Nasional'], ['deskripsi' => 'Kurikulum Nasional']);

    // 4. Mata Pelajaran
    echo "Seeding Mata Pelajaran...\n";
    $mapels = [
        ['kode' => 'MP001', 'nama' => 'Matematika', 'kat' => $katNasional],
        ['kode' => 'MP002', 'nama' => 'Bahasa Inggris', 'kat' => $katNasional],
        ['kode' => 'MP003', 'nama' => 'Fisika', 'kat' => $katNasional],
        ['kode' => 'MP004', 'nama' => 'Pendidikan Agama', 'kat' => $katMandiri],
        ['kode' => 'MP005', 'nama' => 'Bahasa Arab', 'kat' => $katMandiri],
        ['kode' => 'MP006', 'nama' => 'Tahfidz Al-Quran', 'kat' => $katMandiri],
    ];

    $mapelObjects = [];
    foreach ($mapels as $m) {
        $mapelObjects[] = MataPelajaran::updateOrCreate(
            ['kode' => $m['kode']],
            ['nama' => $m['nama'], 'kategori_id' => $m['kat']->id]
        );
    }

    // 5. Jenis Kegiatan
    echo "Seeding Jenis Kegiatan...\n";
    JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'KBM'], ['deskripsi' => 'Kegiatan Belajar Mengajar']);
    JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'Ujian'], ['deskripsi' => 'Ujian Akhir/Tengah Semester']);
    JenisKegiatan::updateOrCreate(['jeniskegiatan' => 'Ekskul'], ['deskripsi' => 'Ekstrakurikuler']);

    // 6. Guru
    echo "Seeding Guru...\n";
    $gurusData = [
        ['nama' => 'Ustadz Ahmad Mudabbir', 'nip' => 'G001', 'email' => 'ahmad@almahir.sch.id'],
        ['nama' => 'Ustadzah Siti Aminah', 'nip' => 'G002', 'email' => 'siti@almahir.sch.id'],
        ['nama' => 'Ustadz Yusuf Mansur', 'nip' => 'G003', 'email' => 'yusuf@almahir.sch.id'],
        ['nama' => 'Ustadzah Fatimah Azzahra', 'nip' => 'G004', 'email' => 'fatimah@almahir.sch.id'],
        ['nama' => 'Ustadz Umar Bin Khattab', 'nip' => 'G005', 'email' => 'umar@almahir.sch.id'],
    ];

    $guruObjects = [];
    foreach ($gurusData as $gd) {
        $guruObjects[] = Guru::updateOrCreate(
            ['nip' => $gd['nip']],
            ['nama' => $gd['nama'], 'email' => $gd['email'], 'status' => 'aktif']
        );
    }

    // 7. Kelas
    echo "Seeding Kelas...\n";
    $kelasList = [
        ['nama' => 'X-1 IPA', 'kode' => 'X1', 'tingkat' => $t10],
        ['nama' => 'XI-1 IPA', 'kode' => 'XI1', 'tingkat' => $t11],
        ['nama' => 'XII-1 IPA', 'kode' => 'XII1', 'tingkat' => $t12],
    ];

    $kelasObjects = [];
    foreach ($kelasList as $kl) {
        $kelasObjects[] = Kelas::updateOrCreate(
            ['nama_kelas' => $kl['nama']],
            ['kode_kelas' => $kl['kode'], 'tingkat_id' => $kl['tingkat']->id]
        );
    }

    // 8. Rombel
    echo "Seeding Rombel, Siswa, and Jadwal...\n";
    foreach ($kelasObjects as $key => $ko) {
        echo "Processing {$ko->nama_kelas}...\n";
        $rombel = Rombel::updateOrCreate(
            ['kelas_id' => $ko->id, 'tahunajaran_id' => $tahun->id],
            [
                'nama_rombel' => 'Rombel ' . $ko->nama_kelas . ' 24/25',
                'wali_kelas_id' => $guruObjects[$key % count($guruObjects)]->id,
                'keterangan' => 'Rombongan Belajar ' . $ko->nama_kelas
            ]
        );

        // 9. Siswa
        for ($i = 1; $i <= 10; $i++) {
            $nis = 'S' . $ko->kode_kelas . str_pad($i, 3, '0', STR_PAD_LEFT);
            $siswa = Siswa::updateOrCreate(
                ['nis' => $nis],
                [
                    'nama' => 'Siswa ' . $ko->nama_kelas . ' ' . $i,
                    'email' => strtolower(str_replace(['-', ' '], '', $ko->nama_kelas)) . $i . '@student.com',
                    'status' => 'aktif'
                ]
            );

            RombelSiswa::updateOrCreate(
                ['rombel_id' => $rombel->id, 'siswa_id' => $siswa->id]
            );
        }

        // 10. Jadwal Pelajaran
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        for ($j = 0; $j < 3; $j++) {
            JadwalPelajaran::updateOrCreate(
                ['rombel_id' => $rombel->id, 'hari' => $hariList[$j % count($hariList)], 'jamke' => $j + 1],
                [
                    'jamawal' => str_pad(7 + $j, 2, '0', STR_PAD_LEFT) . ':30:00',
                    'jamakhir' => str_pad(8 + $j, 2, '0', STR_PAD_LEFT) . ':30:00',
                    'mapel_id' => $mapelObjects[$j % count($mapelObjects)]->id,
                    'guru_id' => $guruObjects[$j % count($guruObjects)]->id
                ]
            );
        }
    }

    Schema::enableForeignKeyConstraints();
    echo "--- All records seeded successfully ---\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getTraceAsString() . "\n";
}
