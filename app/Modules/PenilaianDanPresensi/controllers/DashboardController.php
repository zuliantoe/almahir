<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\PenilaianAkademik;
use Modules\PenilaianDanPresensi\Models\PenilaianTahfidz;
use Modules\PenilaianDanPresensi\Models\Presensi;
use Modules\PenilaianDanPresensi\Models\IzinSakit;

/**
 * DashboardController
 *
 * Dashboard for PenilaianDanPresensi module showing all sections.
 */
class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();
        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        $today = today();

        // Data for SISWA
        if ($user->hasRole('SISWA')) {
            $siswa = $user->ref;
            
            // Presensi Stats for this student
            $presensiSiswa = Presensi::where('siswa_id', $siswa?->id)
                ->where('tahunajaran_id', $activeTA->id ?? 0)
                ->get();
            
            $statsPresensi = [
                'hadir' => $presensiSiswa->where('status', 'Hadir')->count(),
                'izin' => $presensiSiswa->where('status', 'Izin')->count(),
                'sakit' => $presensiSiswa->where('status', 'Sakit')->count(),
                'alpha' => $presensiSiswa->where('status', 'Alpha')->count(),
            ];

            // Recent scores
            $penilaianAkademik = PenilaianAkademik::with(['mataPelajaran'])
                ->where('siswa_id', $siswa?->id)
                ->latest()
                ->take(5)
                ->get();
            
            $penilaianTahfidz = PenilaianTahfidz::where('siswa_id', $siswa?->id)
                ->where('tahunajaran_id', $activeTA->id ?? 0)
                ->latest()
                ->take(5)
                ->get();

            // Calculate Ranking in Rombel
            $activeRombel = \App\Modules\Akademik\Models\RombelSiswa::where('siswa_id', $siswa?->id)
                ->where('status', 'aktif')
                ->whereHas('rombel', function($q) use ($activeTA) {
                    $q->where('tahunajaran_id', $activeTA->id ?? 0);
                })->first();
            
            $rombelId = $activeRombel ? $activeRombel->rombel_id : 0;
            $ranking = '-';
            
            if ($rombelId > 0) {
                $allRombelSiswaIds = \App\Modules\Akademik\Models\RombelSiswa::where('rombel_id', $rombelId)
                    ->where('status', 'aktif')
                    ->pluck('siswa_id');

                $allScores = PenilaianAkademik::whereIn('siswa_id', $allRombelSiswaIds)
                    ->where('tahunajaran_id', $activeTA->id ?? 0)
                    ->get();

                $studentAverages = [];
                foreach ($allRombelSiswaIds as $rsid) {
                    $studentScores = $allScores->where('siswa_id', $rsid);
                    if ($studentScores->isEmpty()) {
                        $studentAverages[$rsid] = 0;
                        continue;
                    }

                    $rekap = [];
                    foreach ($studentScores as $score) {
                        $mid = $score->mapel_id;
                        if (!isset($rekap[$mid])) {
                            $rekap[$mid] = ['harian' => [], 'uts' => null, 'uas' => null];
                        }
                        if ($score->jenis_nilai == 'Harian') $rekap[$mid]['harian'][] = $score->nilai;
                        elseif ($score->jenis_nilai == 'UTS') $rekap[$mid]['uts'] = $score->nilai;
                        elseif ($score->jenis_nilai == 'UAS') $rekap[$mid]['uas'] = $score->nilai;
                    }

                    $totalFinal = 0;
                    $mapelCount = 0;
                    foreach ($rekap as $item) {
                        $avgH = count($item['harian']) > 0 ? array_sum($item['harian']) / count($item['harian']) : 0;
                        $div = 0; $sum = 0;
                        if ($avgH > 0) { $sum += $avgH; $div++; }
                        if ($item['uts'] !== null) { $sum += $item['uts']; $div++; }
                        if ($item['uas'] !== null) { $sum += $item['uas']; $div++; }
                        
                        if ($div > 0) {
                            $totalFinal += ($sum / $div);
                            $mapelCount++;
                        }
                    }
                    $studentAverages[$rsid] = $mapelCount > 0 ? $totalFinal / $mapelCount : 0;
                }

                arsort($studentAverages);
                $ranks = array_keys($studentAverages);
                $myRank = array_search($siswa?->id, $ranks) + 1;
                $ranking = $myRank . ' / ' . count($ranks);
            }

            return view('penilaiandanpresensi::dashboard.index', [
                'title' => 'Dashboard Santri',
                'activeTA' => $activeTA,
                'statsPresensi' => $statsPresensi,
                'penilaianAkademik' => $penilaianAkademik,
                'penilaianTahfidz' => $penilaianTahfidz,
                'ranking' => $ranking,
                'isSiswa' => true,
            ]);
        }

        // Data for GURU
        if ($user->hasRole('GURU')) {
            $guru = $user->ref;
            
            // Stats for Guru's classes in active TA
            $todayPresensi = Presensi::where('guru_id', $guru?->id)
                ->where('tahunajaran_id', $activeTA->id ?? 0)
                ->whereDate('created_at', $today)
                ->get();
            
            $statsPresensi = [
                'hadir' => $todayPresensi->where('status', 'Hadir')->count(),
                'izin' => $todayPresensi->where('status', 'Izin')->count(),
                'sakit' => $todayPresensi->where('status', 'Sakit')->count(),
                'alpha' => $todayPresensi->where('status', 'Alpha')->count(),
            ];

            $pendingIzin = IzinSakit::with(['siswa', 'rombel'])
                ->where('tahunajaran_id', $activeTA->id ?? 0)
                ->where('status', 'Pending')
                ->latest()
                ->take(5)
                ->get();

            $recentPenilaianAkademik = PenilaianAkademik::with(['siswa', 'mataPelajaran'])
                ->where('guru_id', $guru?->id)
                ->where('tahunajaran_id', $activeTA->id ?? 0)
                ->latest()
                ->take(5)
                ->get();

            // Additional stats for Guru Stats Cards (consolidated from misplaced Guru dashboard)
            $guruStats = [
                'total_siswa' => \Modules\Siswa\Models\Siswa::count(),
                'presensi_today' => $todayPresensi->count(),
                'avg_nilai' => PenilaianAkademik::where('guru_id', $guru?->id)->avg('nilai') ?? 0
            ];

            $recentPenilaianTahfidz = PenilaianTahfidz::with(['siswa'])
                ->where('guru_id', $guru?->id)
                ->where('tahunajaran_id', $activeTA->id ?? 0)
                ->latest()
                ->take(5)
                ->get();

            return view('penilaiandanpresensi::dashboard.index', [
                'title' => 'Dashboard Penilaian & Presensi',
                'activeTA' => $activeTA,
                'statsPresensi' => $statsPresensi,
                'pendingIzin' => $pendingIzin,
                'penilaianAkademik' => $recentPenilaianAkademik,
                'penilaianTahfidz' => $recentPenilaianTahfidz,
                'isGuru' => true,
                'guruStats' => $guruStats,
            ]);
        }

        $todayPresensi = Presensi::where('tahunajaran_id', $activeTA->id ?? 0)->whereDate('created_at', today())->get();
        $statsPresensi = [
            'hadir' => $todayPresensi->where('status', 'Hadir')->count(),
            'izin' => $todayPresensi->where('status', 'Izin')->count(),
            'sakit' => $todayPresensi->where('status', 'Sakit')->count(),
            'alpha' => $todayPresensi->where('status', 'Alpha')->count(),
        ];

        $pendingIzin = IzinSakit::with(['siswa', 'rombel'])
            ->where('tahunajaran_id', $activeTA->id ?? 0)
            ->where('status', 'Pending')
            ->latest()
            ->take(5)
            ->get();

        $recentPenilaianAkademik = PenilaianAkademik::with(['siswa', 'mataPelajaran'])->where('tahunajaran_id', $activeTA->id ?? 0)->latest()->take(5)->get();
        $recentPenilaianTahfidz = PenilaianTahfidz::with(['siswa'])->where('tahunajaran_id', $activeTA->id ?? 0)->latest()->take(5)->get();

        return view('penilaiandanpresensi::dashboard.index', [
            'title' => 'Dashboard Penilaian & Presensi',
            'activeTA' => $activeTA,
            'statsPresensi' => $statsPresensi,
            'pendingIzin' => $pendingIzin,
            'penilaianAkademik' => $recentPenilaianAkademik,
            'penilaianTahfidz' => $recentPenilaianTahfidz,
            'isAdmin' => true,
        ]);
    }
}
