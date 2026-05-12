<?php
\Schema::disableForeignKeyConstraints();
\App\Modules\Akademik\Models\JadwalPelajaran::truncate();
\App\Modules\Akademik\Models\RombelSiswa::truncate();
\App\Modules\Akademik\Models\Rombel::truncate();
\App\Modules\Akademik\Models\Kelas::truncate();
\App\Modules\Akademik\Models\MataPelajaran::truncate();
\App\Modules\Akademik\Models\KategoriPelajaran::truncate();
\App\Modules\Akademik\Models\Tingkat::truncate();
\App\Modules\Akademik\Models\TahunAjaran::truncate();
\App\Modules\Akademik\Models\JenisKegiatan::truncate();

$tahun = \App\Modules\Akademik\Models\TahunAjaran::create(['tahunajaran' => '2024/2025', 'semester' => 'Genap', 'status' => 'Aktif']);
$tingkat = \App\Modules\Akademik\Models\Tingkat::create(['kode_tingkat' => '10', 'nama_tingkat' => 'Kelas 10']);
$kat = \App\Modules\Akademik\Models\KategoriPelajaran::create(['kategori' => 'Internal', 'deskripsi' => 'Pelajaran Internal']);
$mapel = \App\Modules\Akademik\Models\MataPelajaran::create(['kode' => 'MAP01', 'nama' => 'Pendidikan Agama', 'kategori_id' => $kat->id]);
$jk = \App\Modules\Akademik\Models\JenisKegiatan::create(['jeniskegiatan' => 'KBM', 'deskripsi' => 'Kegiatan Belajar Mengajar']);
$kelas = \App\Modules\Akademik\Models\Kelas::create(['nama_kelas' => 'X IPA 1', 'kode_kelas' => 'X-PA-1', 'tingkat_id' => $tingkat->id]);

$guru = \Modules\Guru\Models\Guru::first();
if (!$guru) {
    $guru = \Modules\Guru\Models\Guru::create([
        'id' => \Illuminate\Support\Str::uuid(), 
        'nama' => 'Ustadz Ahmad', 
        'nip' => '123', 
        'email' => 'ahmad@web.com', 
        'status' => 'aktif'
    ]);
}

$siswa = \Modules\Siswa\Models\Siswa::first();
if (!$siswa) {
    $siswa = \Modules\Siswa\Models\Siswa::create([
        'id' => \Illuminate\Support\Str::uuid(), 
        'nis' => '555', 
        'nama' => 'Zaidan Al-Fatih', 
        'email' => 'zaidan@web.com', 
        'status' => 'aktif'
    ]);
}

$rombel = \App\Modules\Akademik\Models\Rombel::create([
    'nama_rombel' => 'Rombel X IPA 1 2024', 
    'kelas_id' => $kelas->id, 
    'tahunajaran_id' => $tahun->id, 
    'wali_kelas_id' => $guru->id, 
    'keterangan' => 'Rombongan Belajar Utama'
]);

\App\Modules\Akademik\Models\RombelSiswa::create([
    'rombel_id' => $rombel->id, 
    'siswa_id' => $siswa->id
]);

\App\Modules\Akademik\Models\JadwalPelajaran::create([
    'rombel_id' => $rombel->id, 
    'hari' => 'Senin', 
    'jamke' => 1, 
    'jamawal' => '07:00', 
    'jamakhir' => '08:00', 
    'mapel_id' => $mapel->id, 
    'guru_id' => $guru->id
]);
\Schema::enableForeignKeyConstraints();
echo "Success! Dummy data seeded.\n";
