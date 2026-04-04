<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\Presensi;
use Modules\Guru\Models\Guru as ModelsGuru;
use Modules\Siswa\Models\Siswa as ModelsSiswa;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\JadwalPelajaran;

/**
 * PresensiController
 *
 * CRUD operations for Presensi module.
 */
class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $presensis = Presensi::with(['siswa', 'guru'])->paginate(10);

        $mapels = MataPelajaran::orderBy('nama')->get()->keyBy('id');
        $jadwals = JadwalPelajaran::orderBy('hari')->get()->keyBy('id');

        return view('penilaiandanpresensi::presensi.index', [
            'title' => 'Daftar Presensi',
            'presensis' => $presensis,
            'mapels' => $mapels,
            'jadwals' => $jadwals,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // order by akademik data first
        $mapels = MataPelajaran::orderBy('nama')->get();
        $jadwals = JadwalPelajaran::orderBy('hari')->orderBy('jamawal')->get();
        $gurus = ModelsGuru::orderBy('nama')->get();
        $siswas = ModelsSiswa::orderBy('nama')->get();

        return view('penilaiandanpresensi::presensi.create', [
            'title' => 'Tambah Presensi',
            'siswas' => $siswas,
            'gurus' => $gurus,
            'mapels' => $mapels,
            'jadwals' => $jadwals,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'id_guru' => 'required|exists:guru,id',
            'id_mapel' => 'required|exists:mata_pelajaran,id',
            'id_jadwal_pelajaran' => 'required|exists:jadwal_pelajaran,id',
            'jam' => 'required|date_format:H:i',
            'status' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'scan_id' => 'nullable|string',
        ]);

        Presensi::create($validated);

        return redirect()->route('penilaiandanpresensi.presensi.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Scan kartu ID dan ambil data siswa
     */
    public function scanCard(Request $request): JsonResponse
    {
        $request->validate([
            'scan_id' => 'required|string',
        ]);

        $siswa = ModelsSiswa::where('id', $request->scan_id)
            ->orWhere('nis', $request->scan_id)
            ->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_siswa' => $siswa->id,
                'nama_siswa' => $siswa->nama,
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $presensi = Presensi::with(['siswa', 'guru'])->findOrFail($id);

        $mapel = MataPelajaran::find($presensi->id_mapel);
        $jadwal = JadwalPelajaran::find($presensi->id_jadwal_pelajaran);

        return view('penilaiandanpresensi::presensi.show', [
            'title' => 'Detail Presensi',
            'presensi' => $presensi,
            'mapel' => $mapel,
            'jadwal' => $jadwal,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $presensi = Presensi::findOrFail($id);
        $mapels = MataPelajaran::orderBy('nama')->get();
        $jadwals = JadwalPelajaran::orderBy('hari')->orderBy('jamawal')->get();
        $gurus = ModelsGuru::orderBy('nama')->get();
        $siswas = ModelsSiswa::orderBy('nama')->get();

        return view('penilaiandanpresensi::presensi.edit', [
            'title' => 'Edit Presensi',
            'presensi' => $presensi,
            'siswas' => $siswas,
            'gurus' => $gurus,
            'mapels' => $mapels,
            'jadwals' => $jadwals,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'id_guru' => 'required|exists:guru,id',
            'id_mapel' => 'required|exists:mata_pelajaran,id',
            'id_jadwal_pelajaran' => 'required|exists:jadwal_pelajaran,id',
            'jam' => 'required|date_format:H:i',
            'status' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'scan_id' => 'nullable|string',
        ]);

        $presensi = Presensi::findOrFail($id);
        $presensi->update($validated);

        return redirect()->route('penilaiandanpresensi.presensi.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return redirect()->route('penilaiandanpresensi.presensi.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
