<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * AkademikController
 * 
 * CRUD operations for Akademik module.
 */
class AkademikController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = auth()->user();
        $today = \Carbon\Carbon::now()->locale('id')->translatedFormat('l');
        $todayDate = \Carbon\Carbon::now()->toDateString();

        /*
        // 1. Redirect Guru/Siswa who are NOT Admin/Staff
        if ($user && ($user->hasRole('GURU') || $user->hasRole('SISWA'))) {
            if (!$user->hasRole('SUPER_ADMIN') && !$user->hasRole('STAFF')) {
                return redirect()->route('akademik.jadwal-pelajaran.index');
            }
        }
        */

        // Default Admin / Staff Context
        $todayDate = \Carbon\Carbon::now()->toDateString();
        $totalSiswa = \Modules\Siswa\Models\Siswa::count();
        $totalGuru = \Modules\Guru\Models\Guru::count();
        $totalKelas = \App\Modules\Akademik\Models\Kelas::count();
        $totalMapel = \App\Modules\Akademik\Models\MataPelajaran::count();
        
        $siswaTerbaru = \Modules\Siswa\Models\Siswa::latest()->take(5)->get();
        $guruTerbaru = \Modules\Guru\Models\Guru::latest()->take(5)->get();

        // Upcoming events (next 30 days)
        $upcomingEvents = \App\Modules\Akademik\Models\KalenderAkademik::with('jenisKegiatan')
            ->whereDate('tanggal_awal', '>', $todayDate)
            ->whereDate('tanggal_awal', '<=', \Carbon\Carbon::now()->addDays(30))
            ->orderBy('tanggal_awal')
            ->take(5)
            ->get();

        // Ongoing events (today is between start and end)
        $ongoingEvents = \App\Modules\Akademik\Models\KalenderAkademik::with('jenisKegiatan')
            ->whereDate('tanggal_awal', '<=', $todayDate)
            ->whereDate('tanggal_akhir', '>=', $todayDate)
            ->get();

        // Jadwal Pelajaran Hari Ini (for Guru/Siswa)
        $jadwalHariIni = collect();
        if ($user->hasRole('GURU')) {
            $guru = $user->guru;
            if ($guru) {
                $jadwalHariIni = \App\Modules\Akademik\Models\JadwalPelajaran::with(['rombel', 'mataPelajaran'])
                    ->where('guru_id', $guru->id)
                    ->where('hari', $today)
                    ->orderBy('jamawal')
                    ->get();
            }
        } elseif ($user->hasRole('SISWA')) {
            $siswa = $user->siswa;
            if ($siswa) {
                $rombelSiswa = \App\Modules\Akademik\Models\RombelSiswa::where('siswa_id', $siswa->id)
                    ->where('status', 'aktif')
                    ->first();
                if ($rombelSiswa) {
                    $jadwalHariIni = \App\Modules\Akademik\Models\JadwalPelajaran::with(['guru', 'mataPelajaran'])
                        ->where('rombel_id', $rombelSiswa->rombel_id)
                        ->where('hari', $today)
                        ->orderBy('jamawal')
                        ->get();
                }
            }
        }

        return view('akademik::index', [
            'title' => 'Dashboard Akademik',
            'totalSiswa' => $totalSiswa,
            'totalGuru' => $totalGuru,
            'totalKelas' => $totalKelas,
            'totalMapel' => $totalMapel,
            'siswaTerbaru' => $siswaTerbaru,
            'guruTerbaru' => $guruTerbaru,
            'upcomingEvents' => $upcomingEvents,
            'ongoingEvents' => $ongoingEvents,
            'jadwalHariIni' => $jadwalHariIni,
            'today' => $today,
            'todayDate' => $todayDate,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('akademik::create', [
            'title' => 'Tambah Akademik',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // TODO: Add validation rules
        ]);

        // TODO: Create record

        return redirect()->route('akademik.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        // TODO: Find record
        $akademik = null;
        
        return view('akademik::show', [
            'title' => 'Detail Akademik',
            'akademik' => $akademik,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // TODO: Find record
        $akademik = null;
        
        return view('akademik::edit', [
            'title' => 'Edit Akademik',
            'akademik' => $akademik,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            // TODO: Add validation rules
        ]);

        // TODO: Update record

        return redirect()->route('akademik.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // TODO: Delete record

        return redirect()->route('akademik.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
