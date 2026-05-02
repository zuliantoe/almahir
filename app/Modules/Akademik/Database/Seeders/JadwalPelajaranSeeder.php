<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\MataPelajaran;
use Modules\Guru\Models\Guru;

/**
 * Tambahkan lebih banyak jadwal pelajaran per rombel, mencakup semua hari.
 */
class JadwalPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $hariList   = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $rombels    = Rombel::all();
        $mapels     = MataPelajaran::all();
        $gurus      = Guru::where('status', 'aktif')->get();

        if ($rombels->isEmpty() || $mapels->isEmpty() || $gurus->isEmpty()) {
            $this->command->warn('Data rombel, mapel, atau guru belum ada. Jalankan AcademicDummySeeder dulu.');
            return;
        }

        $jamSlots = [
            ['jamke' => 1, 'awal' => '07:00:00', 'akhir' => '07:45:00'],
            ['jamke' => 2, 'awal' => '07:45:00', 'akhir' => '08:30:00'],
            ['jamke' => 3, 'awal' => '08:30:00', 'akhir' => '09:15:00'],
            ['jamke' => 4, 'awal' => '09:30:00', 'akhir' => '10:15:00'],
            ['jamke' => 5, 'awal' => '10:15:00', 'akhir' => '11:00:00'],
            ['jamke' => 6, 'awal' => '11:00:00', 'akhir' => '11:45:00'],
        ];

        $created = 0;

        foreach ($rombels as $rombel) {
            foreach ($hariList as $hIdx => $hari) {
                // Pilih 4 jam slot per hari
                $selectedSlots = array_slice($jamSlots, 0, 4);

                foreach ($selectedSlots as $slotIdx => $slot) {
                    $mapel = $mapels[($hIdx * 4 + $slotIdx) % $mapels->count()];
                    $guru  = $gurus[($hIdx + $slotIdx) % $gurus->count()];

                    $existing = JadwalPelajaran::where('rombel_id', $rombel->id)
                        ->where('hari', $hari)
                        ->where('jamke', $slot['jamke'])
                        ->exists();

                    if (!$existing) {
                        JadwalPelajaran::create([
                            'rombel_id' => $rombel->id,
                            'hari'      => $hari,
                            'jamke'     => $slot['jamke'],
                            'jamawal'   => $slot['awal'],
                            'jamakhir'  => $slot['akhir'],
                            'mapel_id'  => $mapel->id,
                            'guru_id'   => $guru->id,
                        ]);
                        $created++;
                    }
                }
            }
        }

        $this->command->info("Berhasil membuat {$created} jadwal pelajaran baru.");
    }
}
