<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\Rombel;
use Modules\Guru\Models\Guru;

class JadwalPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rombels = Rombel::all();
        $mapels = MataPelajaran::all();
        $gurus = Guru::all();

        if ($rombels->isEmpty() || $mapels->isEmpty() || $gurus->isEmpty()) {
            return;
        }

        $days = [1, 2, 3, 4, 5, 6]; // Senin - Sabtu
        $hours = [
            ['jamke' => 1, 'awal' => '07:30', 'akhir' => '08:10'],
            ['jamke' => 2, 'awal' => '08:10', 'akhir' => '08:50'],
            ['jamke' => 3, 'awal' => '08:50', 'akhir' => '09:30'],
            ['jamke' => 4, 'awal' => '10:00', 'akhir' => '10:40'],
            ['jamke' => 5, 'awal' => '10:40', 'akhir' => '11:20'],
        ];

        foreach ($rombels as $rombel) {
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
