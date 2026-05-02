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
    public function index(Request $request): View
    {
        $user = auth()->user();

        // 1. Context Guru
        if ($user && $user->hasRole('GURU')) {
            $guru = $user->ref;
            $today = \Carbon\Carbon::now()->locale('id')->translatedFormat('l');

            $jadwalHariIni = \App\Modules\Akademik\Models\JadwalPelajaran::with(['mataPelajaran', 'rombel'])
                ->where('guru_id', $guru?->id)
                ->where('hari', $today)
                ->orderBy('jamke')
                ->get();

            $jadwalMingguan = \App\Modules\Akademik\Models\JadwalPelajaran::with(['mataPelajaran', 'rombel'])
                ->where('guru_id', $guru?->id)
                ->orderBy('hari')
                ->orderBy('jamke')
                ->get();

            $kalender = \App\Modules\Akademik\Models\KalenderAkademik::whereDate('tanggal_awal', '>=', now()->subDays(7)) // fetch recent and future cal items
                ->orderBy('tanggal_awal')
                ->take(5)
                ->get();

            return view('akademik::dashboards.guru', compact('jadwalHariIni', 'jadwalMingguan', 'kalender', 'today'));
        }

        // 2. Context Siswa
        if ($user && $user->hasRole('SISWA')) {
            $siswa = $user->ref;
            $rombelSiswa = \App\Modules\Akademik\Models\RombelSiswa::with('rombel')->where('siswa_id', $siswa?->id)->first();
            $rombelId = $rombelSiswa?->rombel_id;
            $today = \Carbon\Carbon::now()->locale('id')->translatedFormat('l');

            $jadwalHariIni = collect();
            $jadwalMingguan = collect();
            if ($rombelId) {
                $jadwalHariIni = \App\Modules\Akademik\Models\JadwalPelajaran::with(['mataPelajaran', 'guru'])
                    ->where('rombel_id', $rombelId)
                    ->where('hari', $today)
                    ->orderBy('jamke')
                    ->get();

                $jadwalMingguan = \App\Modules\Akademik\Models\JadwalPelajaran::with(['mataPelajaran', 'guru'])
                    ->where('rombel_id', $rombelId)
                    ->orderBy('hari')
                    ->orderBy('jamke')
                    ->get();
            }

            $kalender = \App\Modules\Akademik\Models\KalenderAkademik::whereDate('tanggal_awal', '>=', now()->subDays(7))
                ->orderBy('tanggal_awal')
                ->take(5)
                ->get();

            return view('akademik::dashboards.siswa', compact('jadwalHariIni', 'jadwalMingguan', 'kalender', 'today', 'rombelSiswa'));
        }

        // Default Admin / Staff Context
        $totalSiswa = \Modules\Siswa\Models\Siswa::count();
        $totalGuru = \Modules\Guru\Models\Guru::count();
        $totalKelas = \App\Modules\Akademik\Models\Kelas::count();
        $totalMapel = \App\Modules\Akademik\Models\MataPelajaran::count();
        
        $siswaTerbaru = \Modules\Siswa\Models\Siswa::latest()->take(5)->get();
        $guruTerbaru = \Modules\Guru\Models\Guru::latest()->take(5)->get();

        return view('akademik::index', [
            'title' => 'Dashboard Akademik',
            'totalSiswa' => $totalSiswa,
            'totalGuru' => $totalGuru,
            'totalKelas' => $totalKelas,
            'totalMapel' => $totalMapel,
            'siswaTerbaru' => $siswaTerbaru,
            'guruTerbaru' => $guruTerbaru,
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
