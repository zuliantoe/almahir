<?php

namespace Modules\Guru\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\View\View;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $guru = $user?->ref;

        if (!$guru) {
            return redirect()->route('penilaiandanpresensi.index');
        }

        $activeTahunAjaran = TahunAjaran::aktif()->first();

        // Hari disimpan sebagai string di DB ('Senin','Selasa',dst)
        $hariList  = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $todayName = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'][\Carbon\Carbon::now()->dayOfWeekIso - 1] ?? '';

        $rawJadwal = JadwalPelajaran::with(['mataPelajaran', 'rombel.kelas'])
            ->where('guru_id', $guru->id)
            ->when($activeTahunAjaran, function ($q) use ($activeTahunAjaran) {
                return $q->whereHas('rombel', fn($sq) => $sq->where('tahunajaran_id', $activeTahunAjaran->id));
            })
            ->orderBy('hari')
            ->orderBy('jamke')
            ->get();

        $timetable = [];
        foreach ($rawJadwal as $j) {
            $timetable[$j->hari][$j->jamke] = $j;
        }
        $usedJamKes = $rawJadwal->pluck('jamke')->unique()->sort()->values()->toArray();

        // Jadwal hari ini (hari disimpan sebagai string)
        $jadwalHariIni = $rawJadwal->where('hari', $todayName)->sortBy('jamke');

        return view('guru::dashboard', [
            'title'             => 'Dashboard Guru',
            'breadcrumb'        => 'Dashboard',
            'guru'              => $guru,
            'rawJadwal'         => $rawJadwal,
            'timetable'         => $timetable,
            'usedJamKes'        => $usedJamKes,
            'hariList'          => $hariList,
            'todayName'         => $todayName,
            'activeTahunAjaran' => $activeTahunAjaran,
            'jadwalHariIni'     => $jadwalHariIni,
        ]);
    }
}
