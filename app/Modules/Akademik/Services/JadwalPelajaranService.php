<?php

namespace App\Modules\Akademik\Services;

use App\Modules\Akademik\Models\KalenderAkademik;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\TahunAjaran;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class JadwalPelajaranService
{
    /**
     * Hitung estimasi total jam pelajaran untuk satu mata pelajaran dalam satu semester.
     */
    public function hitungEstimasiTotalJP($mapelId, $rombelId, $tahunAjaranId)
    {
        // 1. Tentukan rentang semester
        // Kita ambil dari tanggal terkecil dan terbesar di kalender akademik tahun ajaran tersebut
        $kalender = KalenderAkademik::where('tahunajaran_id', $tahunAjaranId)->orderBy('tanggal_awal')->get();
        
        if ($kalender->isEmpty()) {
            return 0;
        }

        $startDate = Carbon::parse($kalender->min('tanggal_awal'));
        $endDate = Carbon::parse($kalender->max('tanggal_akhir'));

        // 2. Ambil hari-hari libur (is_kbm = false)
        $holidays = [];
        $holidayEvents = KalenderAkademik::with('jenisKegiatan')
            ->where('tahunajaran_id', $tahunAjaranId)
            ->whereHas('jenisKegiatan', function($q) {
                $q->where('is_kbm', false);
            })
            ->get();

        foreach ($holidayEvents as $event) {
            $period = CarbonPeriod::create($event->tanggal_awal, $event->tanggal_akhir);
            foreach ($period as $date) {
                $holidays[] = $date->format('Y-m-d');
            }
        }

        // 3. Ambil jadwal mingguan untuk mapel ini di rombel ini
        $jadwalMingguan = JadwalPelajaran::where('mapel_id', $mapelId)
            ->where('rombel_id', $rombelId)
            ->get();
        
        if ($jadwalMingguan->isEmpty()) {
            return 0;
        }

        // Petakan jumlah jam per hari
        $jamPerHari = [
            'Senin' => 0, 'Selasa' => 0, 'Rabu' => 0, 'Kamis' => 0, 
            'Jumat' => 0, 'Sabtu' => 0, 'Minggu' => 0
        ];

        foreach ($jadwalMingguan as $j) {
            if (isset($jamPerHari[$j->hari])) {
                $jamPerHari[$j->hari]++;
            }
        }

        // 4. Iterasi setiap hari dalam rentang semester
        $totalJP = 0;
        $period = CarbonPeriod::create($startDate, $endDate);

        // Map Carbon dayOfWeek to Indonesian day name
        $dayMap = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayName = $dayMap[$date->dayOfWeek];

            // Jika hari Minggu, lewati (asumsi tidak ada KBM di hari Minggu)
            if ($date->dayOfWeek === 0) {
                continue;
            }

            // Jika hari ini libur di kalender, lewati
            if (in_array($dateStr, $holidays)) {
                continue;
            }

            // Tambahkan jumlah jam pelajaran sesuai jadwal hari tersebut
            $totalJP += $jamPerHari[$dayName];
        }

        return $totalJP;
    }
}
