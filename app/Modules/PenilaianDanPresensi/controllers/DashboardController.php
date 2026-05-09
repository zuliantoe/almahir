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

            return view('penilaiandanpresensi::dashboard.index', [
                'title' => 'Dashboard Santri',
                'activeTA' => $activeTA,
                'statsPresensi' => $statsPresensi,
                'penilaianAkademik' => $penilaianAkademik,
                'penilaianTahfidz' => $penilaianTahfidz,
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

            return view('penilaiandanpresensi::dashboard.index', [
                'title' => 'Dashboard Penilaian & Presensi',
                'activeTA' => $activeTA,
                'statsPresensi' => $statsPresensi,
                'pendingIzin' => $pendingIzin,
                'penilaianAkademik' => $recentPenilaianAkademik,
                'penilaianTahfidz' => collect(),
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
