<?php

namespace Modules\Siswa\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SiswaDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user->ref;

        $totalP = \Modules\PenilaianDanPresensi\Models\Presensi::where('siswa_id', $siswa?->id)->count();
        $hadirP = \Modules\PenilaianDanPresensi\Models\Presensi::where('siswa_id', $siswa?->id)->where('status', 'Hadir')->count();
        $percent = $totalP > 0 ? round(($hadirP / $totalP) * 100) : 0;

        $stats = [
            'kehadiran' => $percent,
        ];

        return view('siswa::dashboard', [
            'title' => 'Dashboard Santri',
            'breadcrumb' => 'Dashboard',
            'stats' => $stats,
            'siswa' => $siswa,
            'currentRombel' => $siswa?->currentRombel()->with('kelas')->first(),
            'kamarInfo' => $siswa?->kamarPenghuni()->aktif()->first()?->kamar,
            'jadwalPiketHariIni' => $siswa?->jadwalPiket()->whereDate('tanggal', today())->get(),
        ]);
    }
}
