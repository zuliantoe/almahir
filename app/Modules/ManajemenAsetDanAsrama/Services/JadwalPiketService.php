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
     * Generate jadwal piket cerdas & adil.
     * 
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param string $shift (pagi/sore/malam)
     * @param array $locations Array of ['nama' => '...', 'kuota' => ...]
     * @param int|null $kamarId
     */
    public function generateSmart(string $startDate, string $endDate, string $shift, array $locations, ?int $kamarId = null): int
    {
        // 1. Ambil semua santri aktif
        $allSiswa = \Modules\Siswa\Models\Siswa::all();
        if ($allSiswa->isEmpty()) return 0;

        // 2. Siapkan data beban kerja awal (total piket) untuk semua santri & mapping kamar
        $workloadMap = [];
        $kamarMap = [];
        foreach ($allSiswa as $s) {
            $workloadMap[$s->id] = JadwalPiket::where('siswa_id', $s->id)->count();
            
            // Jika generate global, ambil kamar masing-masing santri
            if (!$kamarId) {
                $kamarMap[$s->id] = KamarPenghuni::where('siswa_id', $s->id)->aktif()->value('kamar_id');
            }
        }

        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $insertedCount = 0;

        DB::beginTransaction();
        try {
            while ($currentDate->lte($end)) {
                $dateStr = $currentDate->format('Y-m-d');

                // Untuk hari ini, siapa saja yang SUDAH piket di shift lain?
                // (Mencegah 1 orang piket berkali-kali di hari yang sama)
                $alreadyPicketToday = JadwalPiket::where('tanggal', $dateStr)
                                        ->pluck('siswa_id')
                                        ->toArray();

                foreach ($locations as $loc) {
                    $namaLokasi = $loc['nama'];
                    $kuota = (int) $loc['kuota'];

                    for ($i = 0; $i < $kuota; $i++) {
                        // Cari kandidat:
                        // 1. Belum piket hari ini (di shift manapun)
                        // 2. Jika filter kamar aktif, harus santri dari kamar tersebut
                        $candidates = $allSiswa->filter(function($s) use ($alreadyPicketToday, $kamarId, $kamarMap) {
                            $notPicketed = !in_array($s->id, $alreadyPicketToday);
                            if ($kamarId) {
                                // Cek apakah santri ini di kamar yang dimaksud
                                $isFromKamar = KamarPenghuni::where('siswa_id', $s->id)
                                    ->where('kamar_id', $kamarId)
                                    ->aktif()
                                    ->exists();
                                return $notPicketed && $isFromKamar;
                            }
                            return $notPicketed;
                        });

                        if ($candidates->isEmpty()) break; // Kehabisan santri untuk hari ini

                        // Urutkan kandidat berdasarkan workload terkecil
                        // Jika workload sama, kita acak (shuffle) agar adil
                        $sortedCandidates = $candidates->values()->all();
                        usort($sortedCandidates, function($a, $b) use ($workloadMap) {
                            if ($workloadMap[$a->id] == $workloadMap[$b->id]) {
                                return rand(-1, 1); // Random jika beban sama
                            }
                            return $workloadMap[$a->id] <=> $workloadMap[$b->id];
                        });

                        $selected = $sortedCandidates[0];

                        // Simpan ke database
                        JadwalPiket::create([
                            'tanggal'      => $dateStr,
                            'shift'        => $shift,
                            'lokasi_piket' => $namaLokasi,
                            'siswa_id'     => $selected->id,
                            'kamar_id'     => $kamarId ?? ($kamarMap[$selected->id] ?? null),
                            'status'       => 'belum'
                        ]);

                        $insertedCount++;
                        
                        // Update tracking lokal agar sisa loop hari ini tahu dia sudah terpilih
                        $alreadyPicketToday[] = $selected->id;
                        $workloadMap[$selected->id]++;
                    }
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
     * Regenerate legacy method placeholder (untuk kompatibilitas controller lama jika ada)
     */
    public function generateForKamar(int $kamarId, string $startDate, string $endDate, int $personPerDay = 1): int
    {
        $locations = [['nama' => 'Kamar', 'kuota' => $personPerDay]];
        return $this->generateSmart($startDate, $endDate, 'pagi', $locations, $kamarId);
    }

    /**
     * Regenerate jadwal piket mendatang untuk kamar tertentu.
     * Dipanggil saat ada perubahan penghuni (tambah/hapus/edit).
     */
    public function regenerateFutureJadwal(int $kamarId): void
    {
        $today = Carbon::today()->toDateString();
        
        // 1. Ambil semua jadwal piket mendatang untuk kamar ini yang statusnya masih 'belum'
        $futureJadwal = JadwalPiket::where('kamar_id', $kamarId)
            ->where('tanggal', '>=', $today)
            ->where('status', 'belum')
            ->orderBy('tanggal')
            ->orderBy('shift')
            ->get();
            
        if ($futureJadwal->isEmpty()) {
            // Jika jadwal masa depan kosong tapi kamar ini punya penghuni aktif, 
            // otomatis BUATKAN tabel/putaran jadwal piket baru selama 30 hari ke depan.
            $hasPenghuni = KamarPenghuni::where('kamar_id', $kamarId)->aktif()->exists();
            if ($hasPenghuni) {
                $startDate = Carbon::today()->format('Y-m-d');
                $endDate   = Carbon::today()->addDays(30)->format('Y-m-d');
                $this->generateForKamar($kamarId, $startDate, $endDate, 1);
            }
            return;
        }

        // 2. Ambil daftar penghuni aktif di kamar ini
        $penghuniIds = KamarPenghuni::where('kamar_id', $kamarId)
            ->aktif()
            ->pluck('siswa_id')
            ->toArray();

        if (empty($penghuniIds)) {
            // Jika tidak ada penghuni, hapus jadwal mendatang karena tidak ada yang bisa piket
            JadwalPiket::where('kamar_id', $kamarId)
                ->where('tanggal', '>=', $today)
                ->where('status', 'belum')
                ->delete();
            return;
        }

        // 3. Distribusikan penghuni ke jadwal secara round-robin agar adil
        $index = 0;
        foreach ($futureJadwal as $jadwal) {
            /** @var JadwalPiket $jadwal */
            $jadwal->update([
                'siswa_id' => $penghuniIds[$index % count($penghuniIds)]
            ]);
            $index++;
        }
    }
}
