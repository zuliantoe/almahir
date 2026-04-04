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
        // Get counts for each module
        $penilaianAkademikCount = PenilaianAkademik::count();
        $penilaianTahfidzCount = PenilaianTahfidz::count();
        $presensiCount = Presensi::count();
        $izinSakitCount = IzinSakit::count();

        // Get recent data for each module (latest 5)
        $recentPenilaianAkademik = PenilaianAkademik::with(['siswa', 'guru'])->latest()->take(5)->get();
        $recentPenilaianTahfidz = PenilaianTahfidz::with(['siswa'])->latest()->take(5)->get();
        $recentPresensi = Presensi::with(['siswa', 'guru'])->latest()->take(5)->get();
        $recentIzinSakit = IzinSakit::with(['siswa'])->latest()->take(5)->get();

        // Get today's presensi statistics
        $todayPresensi = Presensi::whereDate('created_at', today())->get();
        $todayHadir = $todayPresensi->where('status', 'Hadir')->count();
        $todayIzin = $todayPresensi->where('status', 'Izin')->count();
        $todaySakit = $todayPresensi->where('status', 'Sakit')->count();
        $todayAlpha = $todayPresensi->where('status', 'Alpha')->count();

        // Get pending izin/sakit requests
        $pendingIzinSakit = IzinSakit::where('status', 'pending')->count();

        return view('penilaiandanpresensi::dashboard.index', [
            'title' => 'Dashboard Penilaian dan Presensi',
            'penilaianAkademikCount' => $penilaianAkademikCount,
            'penilaianTahfidzCount' => $penilaianTahfidzCount,
            'presensiCount' => $presensiCount,
            'izinSakitCount' => $izinSakitCount,
            'recentPenilaianAkademik' => $recentPenilaianAkademik,
            'recentPenilaianTahfidz' => $recentPenilaianTahfidz,
            'recentPresensi' => $recentPresensi,
            'recentIzinSakit' => $recentIzinSakit,
            'todayHadir' => $todayHadir,
            'todayIzin' => $todayIzin,
            'todaySakit' => $todaySakit,
            'todayAlpha' => $todayAlpha,
            'pendingIzinSakit' => $pendingIzinSakit,
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
            'students' => PenilaianAkademik::distinct('id_siswa')->count(),
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
            'surat_count' => PenilaianTahfidz::distinct('surat')->count(),
            'students' => PenilaianTahfidz::distinct('id_siswa')->count(),
            'this_month' => PenilaianTahfidz::whereMonth('created_at', now()->month)->count(),
        ];

        // Top hafizan (students with most hafalans)
        $topHafizan = PenilaianTahfidz::selectRaw('id_siswa, COUNT(*) as total')
            ->with('siswa')
            ->groupBy('id_siswa')
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
