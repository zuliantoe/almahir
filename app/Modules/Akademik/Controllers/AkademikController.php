<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\KalenderAkademik;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AkademikController
 * Menangani routing Dashboard Akademik untuk semua role (Admin/Staff/Guru/Siswa).
 */
class AkademikController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user      = auth()->user();
        $today     = Carbon::now()->locale('id')->translatedFormat('l');
        $todayDate = Carbon::now()->toDateString();

        // Tahun ajaran aktif — dibutuhkan semua role
        $tahunAjaranAktif = TahunAjaran::aktif()->first();

        // Events hari ini & akan datang (semua role)
        $eventHariIni = KalenderAkademik::with('jenisKegiatan')
            ->whereDate('tanggal_awal', '<=', $todayDate)
            ->whereDate('tanggal_akhir', '>=', $todayDate)
            ->get();

        $upcomingEvents = KalenderAkademik::with('jenisKegiatan')
            ->whereDate('tanggal_awal', '>', $todayDate)
            ->whereDate('tanggal_awal', '<=', Carbon::now()->addDays(30))
            ->orderBy('tanggal_awal')
            ->take(5)
            ->get();

        // ── Dashboard GURU ────────────────────────────────────────────
        if ($user && $user->hasRole('GURU')) {
            $guru = method_exists($user, 'guru') ? $user->guru : ($user->ref ?? null);

            $jadwalHariIni  = collect();
            $jadwalMingguan = collect();

            if ($guru) {
                $jadwalHariIni = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran'])
                    ->where('guru_id', $guru->id)
                    ->where('hari', $today)
                    ->when($tahunAjaranAktif, fn($q) => $q->whereHas(
                        'rombel', fn($sq) => $sq->where('tahunajaran_id', $tahunAjaranAktif->id)
                    ))
                    ->orderBy('jamke')
                    ->get();

                $jadwalMingguan = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran'])
                    ->where('guru_id', $guru->id)
                    ->when($tahunAjaranAktif, fn($q) => $q->whereHas(
                        'rombel', fn($sq) => $sq->where('tahunajaran_id', $tahunAjaranAktif->id)
                    ))
                    ->orderBy('hari')
                    ->orderBy('jamke')
                    ->get();
            }

            return view('akademik::dashboards.guru', compact(
                'today', 'todayDate', 'guru',
                'tahunAjaranAktif',
                'jadwalHariIni', 'jadwalMingguan',
                'eventHariIni', 'upcomingEvents'
            ));
        }

        // ── Dashboard SISWA ───────────────────────────────────────────
        if ($user && $user->hasRole('SISWA')) {
            $siswa = method_exists($user, 'siswa') ? $user->siswa : ($user->ref ?? null);

            $rombelSiswa    = null;
            $jadwalHariIni  = collect();
            $jadwalMingguan = collect();

            if ($siswa) {
                $rombelSiswa = RombelSiswa::with(['rombel.kelas', 'rombel.tahunAjaran'])
                    ->where('siswa_id', $siswa->id)
                    ->where('status', 'aktif')
                    ->when($tahunAjaranAktif, fn($q) => $q->where('tahunajaran_id', $tahunAjaranAktif->id))
                    ->first();

                if ($rombelSiswa) {
                    $jadwalHariIni = JadwalPelajaran::with(['guru', 'mataPelajaran'])
                        ->where('rombel_id', $rombelSiswa->rombel_id)
                        ->where('hari', $today)
                        ->orderBy('jamke')
                        ->get();

                    $jadwalMingguan = JadwalPelajaran::with(['guru', 'mataPelajaran'])
                        ->where('rombel_id', $rombelSiswa->rombel_id)
                        ->orderBy('hari')
                        ->orderBy('jamke')
                        ->get();
                }
            }

            return view('akademik::dashboards.siswa', compact(
                'today', 'todayDate', 'siswa', 'rombelSiswa',
                'tahunAjaranAktif',
                'jadwalHariIni', 'jadwalMingguan',
                'eventHariIni', 'upcomingEvents'
            ));
        }

        // ── Dashboard ADMIN / STAFF ───────────────────────────────────
        $totalSiswa = \Modules\Siswa\Models\Siswa::count();
        $totalGuru  = \Modules\Guru\Models\Guru::count();
        $totalKelas = Kelas::count();
        $totalMapel = MataPelajaran::count();

        $siswaTerbaru = \Modules\Siswa\Models\Siswa::latest()->take(5)->get();
        $guruTerbaru  = \Modules\Guru\Models\Guru::latest()->take(5)->get();

        return view('akademik::index', [
            'title'            => 'Dashboard Akademik',
            'totalSiswa'       => $totalSiswa,
            'totalGuru'        => $totalGuru,
            'totalKelas'       => $totalKelas,
            'totalMapel'       => $totalMapel,
            'siswaTerbaru'     => $siswaTerbaru,
            'guruTerbaru'      => $guruTerbaru,
            'upcomingEvents'   => $upcomingEvents,
            'ongoingEvents'    => $eventHariIni,
            'jadwalHariIni'    => collect(),
            'tahunAjaranAktif' => $tahunAjaranAktif,
            'today'            => $today,
            'todayDate'        => $todayDate,
        ]);
    }
}
