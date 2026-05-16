<?php

namespace App\Modules\Akademik\Database\Seeders;

use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Tingkat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;

class PromotionTestingSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Setting up Promotion Testing Data...');

        // 1. Clear relevant tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RombelSiswa::truncate();
        Rombel::truncate();
        TahunAjaran::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Tahun Ajaran
        $ta1 = TahunAjaran::create([
            'tahunajaran' => '2025/2026',
            'status' => 1, // Aktif (Asal)
            'keterangan' => 'Tahun Ajaran Aktif Saat Ini'
        ]);

        $ta2 = TahunAjaran::create([
            'tahunajaran' => '2026/2027',
            'status' => 0, // Belum Aktif (Tujuan)
            'keterangan' => 'Tahun Ajaran Mendatang'
        ]);

        // 3. Ensure Tingkat and Kelas exist
        $tingkat10 = Tingkat::firstOrCreate(['kode_tingkat' => 'X'], ['nama_tingkat' => 'Sepuluh']);
        $tingkat11 = Tingkat::firstOrCreate(['kode_tingkat' => 'XI'], ['nama_tingkat' => 'Sebelas']);
        $tingkat12 = Tingkat::firstOrCreate(['kode_tingkat' => 'XII'], ['nama_tingkat' => 'Duabelas']);

        $kelas10 = Kelas::firstOrCreate(['nama_kelas' => 'X IPA 1'], ['tingkat_id' => $tingkat10->id]);
        $kelas11 = Kelas::firstOrCreate(['nama_kelas' => 'XI IPA 1'], ['tingkat_id' => $tingkat11->id]);
        $kelas12 = Kelas::firstOrCreate(['nama_kelas' => 'XII IPA 1'], ['tingkat_id' => $tingkat12->id]);

        $guru = Guru::first() ?? Guru::create(['nama' => 'Guru Testing', 'nip' => '123456']);

        // 4. Create Rombels in 2025/2026
        $rombel10 = Rombel::create([
            'nama_rombel' => 'X IPA 1',
            'tingkat_id' => $tingkat10->id,
            'kelas_id' => $kelas10->id,
            'tahunajaran_id' => $ta1->id,
            'guru_id' => $guru->id
        ]);

        $rombel11 = Rombel::create([
            'nama_rombel' => 'XI IPA 1',
            'tingkat_id' => $tingkat11->id,
            'kelas_id' => $kelas11->id,
            'tahunajaran_id' => $ta1->id,
            'guru_id' => $guru->id
        ]);

        $rombel12 = Rombel::create([
            'nama_rombel' => 'XII IPA 1',
            'tingkat_id' => $tingkat12->id,
            'kelas_id' => $kelas12->id,
            'tahunajaran_id' => $ta1->id,
            'guru_id' => $guru->id
        ]);

        // 5. Create Siswas and Add to Rombels
        $siswas = Siswa::limit(15)->get();
        if ($siswas->count() < 15) {
             $this->command->warn('Siswa counts low, please run SiswaSeeder first.');
             // Create dummy if needed
             for($i=0; $i<15; $i++) {
                Siswa::create(['nama' => 'Siswa Test '.$i, 'nis' => '1000'.$i, 'status' => 'aktif']);
             }
             $siswas = Siswa::latest()->limit(15)->get();
        }

        foreach ($siswas->take(5) as $s) {
            $this->addSiswaToRombel($s->id, $rombel10);
        }

        foreach ($siswas->slice(5, 5) as $s) {
            $this->addSiswaToRombel($s->id, $rombel11);
        }

        foreach ($siswas->slice(10, 5) as $s) {
            $this->addSiswaToRombel($s->id, $rombel12);
        }

        $this->command->info('✅ Setup Done! You have 3 Rombels in 2025/2026 (Active) ready to be promoted to 2026/2027.');
    }

    private function addSiswaToRombel($siswaId, $rombel)
    {
        RombelSiswa::create([
            'siswa_id' => $siswaId,
            'rombel_id' => $rombel->id,
            'tahunajaran_id' => $rombel->tahunajaran_id,
            'kelas_id' => $rombel->kelas_id,
            'status' => 'aktif'
        ]);
    }
}
