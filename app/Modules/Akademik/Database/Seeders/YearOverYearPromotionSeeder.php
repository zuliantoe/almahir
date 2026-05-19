<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Tingkat;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use Modules\Siswa\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class YearOverYearPromotionSeeder extends Seeder
{
    public function run()
    {
        // 1. Clean up existing data to avoid confusion during testing
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RombelSiswa::truncate();
        Rombel::truncate();
        TahunAjaran::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Create Academic Years
        $pastYear = TahunAjaran::create([
            'tahunajaran' => '2024/2025',
            'status' => 0, // Inactive
        ]);

        $activeYear = TahunAjaran::create([
            'tahunajaran' => '2025/2026',
            'status' => 1, // Active
        ]);

        // 3. Ensure Tingkat and Kelas exist
        $tingkat10 = Tingkat::where('kode_tingkat', '10')->first() ?? Tingkat::create(['kode_tingkat' => '10', 'nama_tingkat' => 'Kelas 10']);
        $tingkat11 = Tingkat::where('kode_tingkat', '11')->first() ?? Tingkat::create(['kode_tingkat' => '11', 'nama_tingkat' => 'Kelas 11']);
        $tingkat12 = Tingkat::where('kode_tingkat', '12')->first() ?? Tingkat::create(['kode_tingkat' => '12', 'nama_tingkat' => 'Kelas 12']);

        $kelas10A = Kelas::where('nama_kelas', 'X-A')->first() ?? Kelas::create(['kode_kelas' => 'X-A', 'nama_kelas' => 'X-A', 'tingkat_id' => $tingkat10->id]);
        $kelas11A = Kelas::where('nama_kelas', 'XI-A')->first() ?? Kelas::create(['kode_kelas' => 'XI-A', 'nama_kelas' => 'XI-A', 'tingkat_id' => $tingkat11->id]);

        // 4. Create Rombels in Past Year (2024/2025)
        $rombel10Past = Rombel::create([
            'nama_rombel' => 'Rombel X-A 2024',
            'kelas_id' => $kelas10A->id,
            'tingkat_id' => $tingkat10->id,
            'tahunajaran_id' => $pastYear->id,
        ]);

        $rombel11Past = Rombel::create([
            'nama_rombel' => 'Rombel XI-A 2024',
            'kelas_id' => $kelas11A->id,
            'tingkat_id' => $tingkat11->id,
            'tahunajaran_id' => $pastYear->id,
        ]);

        // 5. Create Students and Link to Past Rombels
        $students = Siswa::take(20)->get();
        if ($students->count() < 10) {
            // Create some if not enough
            for ($i = 1; $i <= 20; $i++) {
                Siswa::create([
                    'nis' => 'TEST' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'nama' => 'Santri Test ' . $i,
                    'jenis_kelamin' => $i % 2 == 0 ? 'L' : 'P',
                    'status' => 'aktif'
                ]);
            }
            $students = Siswa::all();
        }

        foreach ($students as $index => $siswa) {
            if ($index < 10) {
                // First 10 students in Class 10
                RombelSiswa::create([
                    'rombel_id' => $rombel10Past->id,
                    'siswa_id' => $siswa->id,
                    'tahunajaran_id' => $pastYear->id,
                    'status' => 'aktif'
                ]);
            } else {
                // Next 10 students in Class 11
                RombelSiswa::create([
                    'rombel_id' => $rombel11Past->id,
                    'siswa_id' => $siswa->id,
                    'tahunajaran_id' => $pastYear->id,
                    'status' => 'aktif'
                ]);
            }
        }

        // 6. Create Rombels in Active Year (2025/2026) - Destination
        $kelas12A = Kelas::where('nama_kelas', 'XII-A')->first() ?? Kelas::create(['kode_kelas' => 'XII-A', 'nama_kelas' => 'XII-A', 'tingkat_id' => $tingkat12->id]);

        Rombel::create([
            'nama_rombel' => 'Rombel XI-A 2025', // Destination for 10-A
            'kelas_id' => $kelas11A->id,
            'tingkat_id' => $tingkat11->id,
            'tahunajaran_id' => $activeYear->id,
        ]);

        Rombel::create([
            'nama_rombel' => 'Rombel XII-A 2025', // Destination for 11-A
            'kelas_id' => $kelas12A->id,
            'tingkat_id' => $tingkat12->id,
            'tahunajaran_id' => $activeYear->id,
        ]);

        $this->command->info('YearOverYearPromotionSeeder completed successfully.');
        $this->command->info('Past Year (2024/2025): Inactive, contains 2 Rombels with 20 students total.');
        $this->command->info('Active Year (2025/2026): Active, contains empty destination Rombels.');
    }
}
