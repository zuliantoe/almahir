<?php

namespace Modules\PegawaiManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PegawaiManager\Models\Pegawai;
use App\Models\User;
use Illuminate\View\View;
use Modules\Absensi\Models\Absensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the Pegawai Manager dashboard.
     */
    public function index(): View
    {
        // Total pertipe
        $totalGuru = User::withRole('GURU')->count();
        $totalStaff = User::withRole('STAFF')->count();
        $totalSdm = Pegawai::count();

        // Absensi hari ini
        $today = Carbon::today()->toDateString();
        $hadirHariIni = Absensi::whereDate('tanggal', $today)
            ->whereIn('status', ['TEPAT WAKTU', 'TERLAMBAT'])
            ->count();
            
        $izinHariIni = \Modules\Perizinan\Models\Perizinan::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->count();

        return view('pegawaimanager::dashboard', [
            'title' => 'Dashboard Kepegawaian',
            'totalSdm' => $totalSdm,
            'totalGuru' => $totalGuru,
            'totalStaff' => $totalStaff,
            'hadirHariIni' => $hadirHariIni,
            'izinHariIni' => $izinHariIni,
        ]);
    }
}
