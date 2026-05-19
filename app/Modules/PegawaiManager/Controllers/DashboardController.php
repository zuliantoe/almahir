<?php

namespace Modules\PegawaiManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PegawaiManager\Models\Pegawai;
use Modules\PegawaiManager\Models\CalonPegawai;
use Modules\PegawaiManager\Models\TypePegawai;
use App\Models\User;
use Illuminate\View\View;
use Modules\Absensi\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today()->toDateString();
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // ============================================================
        // STATS CARDS
        // ============================================================
        $totalSdm     = Pegawai::count();
        $totalGuru    = User::withRole('GURU')->count();
        $totalStaff   = User::withRole('STAFF')->count();
        $totalCalon   = CalonPegawai::whereNull('deleted_at')->count();

        $hadirHariIni = Absensi::whereDate('tanggal', $today)
            ->whereIn('status', ['TEPAT WAKTU', 'TERLAMBAT'])->count();

        $izinHariIni = \Modules\Perizinan\Models\Perizinan::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)->count();

        $terlambatHariIni = Absensi::whereDate('tanggal', $today)
            ->where('status', 'TERLAMBAT')->count();

        // ============================================================
        // CHART 1: Komposisi SDM per Tipe Pegawai (Pie/Donut)
        // ============================================================
        $komposisiTipe = Pegawai::with('typePegawai')
            ->select('type_pegawai_id', DB::raw('count(*) as total'))
            ->groupBy('type_pegawai_id')
            ->get()
            ->map(fn($item) => [
                'label' => $item->typePegawai->nama_type ?? 'Tidak Ada Tipe',
                'total' => $item->total,
            ]);

        // ============================================================
        // CHART 2: Komposisi Jenis Kelamin (Pie)
        // ============================================================
        $komposisiGender = Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->whereNotNull('jenis_kelamin')
            ->groupBy('jenis_kelamin')
            ->get()
            ->map(fn($item) => [
                'label' => $item->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan',
                'total' => $item->total,
            ]);

        // ============================================================
        // CHART 3: Tren Rekrutmen 6 Bulan Terakhir (Bar Chart)
        // ============================================================
        $trenRekrutmen = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $trenRekrutmen[] = [
                'bulan' => $bulan->format('M Y'),
                'masuk' => Pegawai::whereYear('tanggal_masuk', $bulan->year)
                    ->whereMonth('tanggal_masuk', $bulan->month)->count(),
                'calon' => CalonPegawai::withTrashed()
                    ->whereYear('created_at', $bulan->year)
                    ->whereMonth('created_at', $bulan->month)->count(),
            ];
        }

        // ============================================================
        // CHART 4: Kehadiran Bulanan (Line Chart) - 6 Bulan Terakhir
        // ============================================================
        $trenKehadiran = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $trenKehadiran[] = [
                'bulan'   => $bulan->format('M Y'),
                'hadir'   => Absensi::whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)
                    ->whereIn('status', ['TEPAT WAKTU', 'TERLAMBAT'])->count(),
                'terlambat' => Absensi::whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)
                    ->where('status', 'TERLAMBAT')->count(),
            ];
        }

        // ============================================================
        // AUDIT TRAIL: 15 Aktivitas Terbaru
        // ============================================================
        $activityLogs = Activity::with('causer')
            ->latest()
            ->take(15)
            ->get();

        // ============================================================
        // STATUS SELEKSI CALON PEGAWAI
        // ============================================================
        $statusSeleksi = CalonPegawai::select('status_seleksi', DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('status_seleksi')
            ->pluck('total', 'status_seleksi');

        return view('pegawaimanager::dashboard', [
            'title'             => 'Dashboard Kepegawaian',
            'totalSdm'          => $totalSdm,
            'totalGuru'         => $totalGuru,
            'totalStaff'        => $totalStaff,
            'totalCalon'        => $totalCalon,
            'hadirHariIni'      => $hadirHariIni,
            'izinHariIni'       => $izinHariIni,
            'terlambatHariIni'  => $terlambatHariIni,
            'komposisiTipe'     => $komposisiTipe,
            'komposisiGender'   => $komposisiGender,
            'trenRekrutmen'     => $trenRekrutmen,
            'trenKehadiran'     => $trenKehadiran,
            'activityLogs'      => $activityLogs,
            'statusSeleksi'     => $statusSeleksi,
        ]);
    }
}
