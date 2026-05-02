<?php

namespace App\Modules\ManajemenAsetDanAsrama\Services;

use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;
use App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;
use App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JadwalPiketService
{
    /**
     * Generate jadwal piket untuk suatu kamar secara Round Robin.
     * 
     * @param int $kamarId
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param int $personPerDay Jumlah orang yang piket per hari
     */
    public function generateForKamar(int $kamarId, string $startDate, string $endDate, int $personPerDay = 1): int
    {
        // 1. Ambil semua penghuni AKTIF di kamar tersebut
        $penghuniAktif = KamarPenghuni::where('kamar_id', $kamarId)
            ->where(function($query) {
                $query->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>', now());
            })
            ->pluck('siswa_id')
            ->toArray();

        if (empty($penghuniAktif)) {
            return 0; // Tidak ada yang bisa di-assign
        }

        // 2. Hitung statistik beban piket (total & terakhir piket) untuk pengurutan yang adil
        // Kita sorting berdasarkan total_piket ASC, lalu last_piket ASC
        $siswaStats = [];
        foreach ($penghuniAktif as $siswaId) {
            $totalPiket = JadwalPiket::where('kamar_id', $kamarId)
                            ->where('siswa_id', $siswaId)
                            ->count();
                            
            $lastPiket = JadwalPiket::where('kamar_id', $kamarId)
                            ->where('siswa_id', $siswaId)
                            ->orderBy('tanggal', 'desc')
                            ->first();

            $siswaStats[] = [
                'siswa_id' => $siswaId,
                'total_piket' => $totalPiket,
                'last_piket_date' => $lastPiket ? $lastPiket->tanggal->format('Y-m-d') : '2000-01-01', // Asumsi belum pernah
            ];
        }

        // Urutkan (Fairness Algorithm)
        usort($siswaStats, function($a, $b) {
            if ($a['total_piket'] == $b['total_piket']) {
                return $a['last_piket_date'] <=> $b['last_piket_date'];
            }
            return $a['total_piket'] <=> $b['total_piket'];
        });

        // Ekstrak ID yang sudah terurut
        $sortedSiswaIds = array_column($siswaStats, 'siswa_id');
        $totalSiswa = count($sortedSiswaIds);
        $siswaIndex = 0;

        // 3. Loop hari per hari dari startDate ke endDate
        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $insertedCount = 0;

        // Mulai transaksi agar aman
        DB::beginTransaction();
        try {
            while ($currentDate->lte($end)) {
                $dateStr = $currentDate->format('Y-m-d');

                // Cek berapa jadwal yang sudah ada di tanggal ini untuk kamar ini
                // agar tidak duplikat generate
                $existingCount = JadwalPiket::where('kamar_id', $kamarId)
                                    ->where('tanggal', $dateStr)
                                    ->count();

                $needed = $personPerDay - $existingCount;

                for ($i = 0; $i < $needed; $i++) {
                    $selectedSiswaId = $sortedSiswaIds[$siswaIndex % $totalSiswa];
                    
                    // Pastikan siswa ini belum piket di hari yang sama
                    $isAlreadyPiket = JadwalPiket::where('kamar_id', $kamarId)
                                        ->where('tanggal', $dateStr)
                                        ->where('siswa_id', $selectedSiswaId)
                                        ->exists();

                    if (!$isAlreadyPiket) {
                        JadwalPiket::create([
                            'kamar_id' => $kamarId,
                            'tanggal'  => $dateStr,
                            'siswa_id' => $selectedSiswaId,
                            'status'   => 'belum'
                        ]);
                        $insertedCount++;
                    }
                    
                    $siswaIndex++;
                }

                $currentDate->addDay();
            }
            DB::commit();
            return $insertedCount;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Regenerate jadwal untuk ke depannya jika ada perubahan penghuni.
     * Ini menghapus jadwal 'belum' di masa depan dan me-regenerate ulang.
     */
    public function regenerateFutureJadwal(int $kamarId)
    {
        $tomorrow = now()->addDay()->format('Y-m-d');
        
        // Cari ujung jadwal terakhir di kamar ini untuk tahu seberapa jauh kita harus regenerate
        $lastJadwal = JadwalPiket::where('kamar_id', $kamarId)
                        ->orderBy('tanggal', 'desc')
                        ->first();
                        
        if (!$lastJadwal) {
            return 0;
        }

        $endDate = $lastJadwal->tanggal->format('Y-m-d');
        
        if ($endDate < $tomorrow) {
            return 0; // Tidak ada jadwal masa depan
        }

        // Hapus jadwal masa depan yang statusnya masih 'belum'
        JadwalPiket::where('kamar_id', $kamarId)
            ->where('tanggal', '>=', $tomorrow)
            ->where('status', 'belum')
            ->delete();

        // Generate ulang
        return $this->generateForKamar($kamarId, $tomorrow, $endDate, 1); // Asumsi 1 orang per hari, bisa dinamis
    }
}
