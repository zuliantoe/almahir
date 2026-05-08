<?php

namespace Modules\Guru\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class GuruDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $guru = $user->ref;
        $today = now()->toDateString();

        $stats = [
            'total_siswa' => \Modules\Siswa\Models\Siswa::count(),
            'presensi_today' => \Modules\PenilaianDanPresensi\Models\Presensi::where('id_guru', $guru?->id)->whereDate('created_at', $today)->count(),
            'avg_nilai' => \Modules\PenilaianDanPresensi\Models\PenilaianAkademik::where('id_guru', $guru?->id)->avg('nilai') ?? 0
        ];

        return view('guru::dashboard', [
            'title' => 'Dashboard Guru',
            'breadcrumb' => 'Dashboard',
            'stats' => $stats
        ]);
    }
}
