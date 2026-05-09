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

            return view('penilaiandanpresensi::dashboard.index', [
                'title' => 'Dashboard Penilaian & Presensi',
                'activeTA' => $activeTA,
                'statsPresensi' => $statsPresensi,
                'pendingIzin' => $pendingIzin,
                'penilaianAkademik' => $recentPenilaianAkademik,
                'penilaianTahfidz' => collect(),
                'isGuru' => true,
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

    /**
     * Dashboard Penilaian Akademik
     */
    public function dashboardPenilaianAkademik(): View
    {
        $recentNilai = PenilaianAkademik::with(['siswa', 'guru'])->latest()->take(10)->get();
        
        $stats = [
            'total' => PenilaianAkademik::count(),
            'average' => PenilaianAkademik::avg('nilai') ?? 0,
            'students' => PenilaianAkademik::distinct('siswa_id')->count(),
            'this_month' => PenilaianAkademik::whereMonth('created_at', now()->month)->count(),
        ];

        $distribution = [
            'excellent' => PenilaianAkademik::where('nilai', '>=', 90)->count(),
            'good' => PenilaianAkademik::whereBetween('nilai', [75, 89])->count(),
            'fair' => PenilaianAkademik::whereBetween('nilai', [60, 74])->count(),
            'poor' => PenilaianAkademik::where('nilai', '<', 60)->count(),
        ];

        return view('penilaiandanpresensi::penilaian-akademik.dashboard', [
            'recentNilai' => $recentNilai,
            'stats' => $stats,
            'distribution' => $distribution,
        ]);
    }

    /**
     * Dashboard Penilaian Tahfidz
     */
    public function dashboardPenilaianTahfidz(): View
    {
        $recentHafalan = PenilaianTahfidz::with(['siswa'])->latest()->take(10)->get();
        
        $stats = [
            'total' => PenilaianTahfidz::count(),
            'surat_count' => PenilaianTahfidz::distinct('surat_awal')->count(),
            'students' => PenilaianTahfidz::distinct('siswa_id')->count(),
            'this_month' => PenilaianTahfidz::whereMonth('created_at', now()->month)->count(),
        ];

        // Top hafizan (students with most hafalans)
        $topHafizan = PenilaianTahfidz::selectRaw('siswa_id, COUNT(*) as total')
            ->with('siswa')
            ->groupBy('siswa_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function($item) {
                return (object)[
                    'siswa_nama' => $item->siswa->nama ?? '-',
                    'total' => $item->total,
                ];
            });

        return view('penilaiandanpresensi::penilaian-tahfidz.dashboard', [
            'recentHafalan' => $recentHafalan,
            'stats' => $stats,
            'topHafizan' => $topHafizan,
        ]);
    }

    /**
     * Dashboard Presensi
     */
    public function dashboardPresensi(): View
    {
        $recentPresensi = Presensi::with(['siswa', 'guru'])->latest()->take(10)->get();
        
        $todayPresensi = Presensi::whereDate('created_at', today())->get();
        $todayHadir = $todayPresensi->where('status', 'Hadir')->count();
        $todayIzin = $todayPresensi->where('status', 'Izin')->count();
        $todaySakit = $todayPresensi->where('status', 'Sakit')->count();
        $todayAlpha = $todayPresensi->where('status', 'Alpha')->count();

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weekPresensi = Presensi::whereBetween('created_at', [$weekStart, $weekEnd])->get();
        $weekHadir = $weekPresensi->where('status', 'Hadir')->count();
        $weekIzin = $weekPresensi->where('status', 'Izin')->count();
        $weekSakit = $weekPresensi->where('status', 'Sakit')->count();
        $weekAlpha = $weekPresensi->where('status', 'Alpha')->count();

        return view('penilaiandanpresensi::presensi.dashboard', [
            'recentPresensi' => $recentPresensi,
            'todayHadir' => $todayHadir,
            'todayIzin' => $todayIzin,
            'todaySakit' => $todaySakit,
            'todayAlpha' => $todayAlpha,
            'presensiCount' => Presensi::count(),
            'weekHadir' => $weekHadir,
            'weekIzin' => $weekIzin,
            'weekSakit' => $weekSakit,
            'weekAlpha' => $weekAlpha,
        ]);
    }

    /**
     * Dashboard Izin & Sakit
     */
    public function dashboardIzinSakit(): View
    {
        $pendingRequests = IzinSakit::with(['siswa'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'pending' => IzinSakit::where('status', 'pending')->count(),
            'approved' => IzinSakit::where('status', 'approved')->count(),
            'rejected' => IzinSakit::where('status', 'rejected')->count(),
            'total' => IzinSakit::count(),
        ];

        $distribution = [
            'izin' => IzinSakit::where('jenis', 'Izin')->count(),
            'sakit' => IzinSakit::where('jenis', 'Sakit')->count(),
        ];

        return view('penilaiandanpresensi::izin-sakit.dashboard', [
            'pendingRequests' => $pendingRequests,
            'stats' => $stats,
            'distribution' => $distribution,
        ]);
    }
}
