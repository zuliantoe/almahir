<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Siswa\Models\Siswa;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\MataPelajaran;
use Modules\Guru\Models\Guru;

class FixScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Find active TA
        $activeTA = TahunAjaran::whereIn('status', [1, 'aktif'])->first() ?: TahunAjaran::orderBy('id', 'desc')->first();
        if (!$activeTA) return;

        // 2. Get Rombels for active TA
        $activeRombels = Rombel::where('tahunajaran_id', $activeTA->id)->get();
        if ($activeRombels->isEmpty()) {
            // If no rombels for active TA, assign some existing rombels to this TA
            $someRombels = Rombel::take(3)->get();
            foreach ($someRombels as $r) {
                $r->update(['tahunajaran_id' => $activeTA->id]);
            }
            $activeRombels = Rombel::where('tahunajaran_id', $activeTA->id)->get();
        }

        // 3. Clear and assign students to active Rombels
        RombelSiswa::whereIn('rombel_id', Rombel::pluck('id'))->delete();
        $siswas = Siswa::all();
        foreach ($siswas as $siswa) {
            RombelSiswa::create([
                'siswa_id' => $siswa->id,
                'rombel_id' => $activeRombels->random()->id
            ]);
        }

        // 4. Seed Schedules for active Rombels
        JadwalPelajaran::whereIn('rombel_id', $activeRombels->pluck('id'))->delete();
        $mapels = MataPelajaran::all();
        $gurus = Guru::all();
        $days = [1, 2, 3, 4, 5, 6];
        $hours = [
            ['jamke' => 1, 'awal' => '07:30', 'akhir' => '08:10'],
            ['jamke' => 2, 'awal' => '08:10', 'akhir' => '08:50'],
            ['jamke' => 3, 'awal' => '08:50', 'akhir' => '09:30'],
            ['jamke' => 4, 'awal' => '10:00', 'akhir' => '10:40'],
            ['jamke' => 5, 'awal' => '10:40', 'akhir' => '11:20'],
        ];

        foreach ($activeRombels as $rombel) {
            foreach ($days as $day) {
                foreach ($hours as $hour) {
                    JadwalPelajaran::create([
                        'hari' => $day,
                        'jamke' => $hour['jamke'],
                        'jamawal' => $hour['awal'],
                        'jamakhir' => $hour['akhir'],
                        'mapel_id' => $mapels->random()->id,
                        'rombel_id' => $rombel->id,
                        'guru_id' => $gurus->random()->id,
                    ]);
                }
            }
        }
    }
}
