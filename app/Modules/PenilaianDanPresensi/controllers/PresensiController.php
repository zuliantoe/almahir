<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\Presensi;
use App\Models\Siswa;
use App\Models\Guru;
use Modules\Guru\Models\Guru as ModelsGuru;
use Modules\Siswa\Models\Siswa as ModelsSiswa;

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

        return view('penilaiandanpresensi::presensi.index', [
            'title' => 'Daftar Presensi',
            'presensis' => $presensis,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $siswas = ModelsSiswa::all();
        $gurus = ModelsGuru::all();

        return view('penilaiandanpresensi::presensi.create', [
            'title' => 'Tambah Presensi',
            'siswas' => $siswas,
            'gurus' => $gurus,
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
            'id_mapel' => 'required|exists:mapel,id',
            'id_jadwal_pelajaran' => 'required|exists:jadwal_pelajaran,id',
            'jam' => 'required|date_format:H:i',
            'status' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
        ]);

        Presensi::create($validated);

        return redirect()->route('penilaiandanpresensi.presensi.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $presensi = Presensi::with(['siswa', 'guru'])->findOrFail($id);

        return view('penilaiandanpresensi::presensi.show', [
            'title' => 'Detail Presensi',
            'presensi' => $presensi,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $presensi = Presensi::findOrFail($id);
        $siswas = ModelsSiswa::all();
        $gurus = ModelsGuru::all();

        return view('penilaiandanpresensi::presensi.edit', [
            'title' => 'Edit Presensi',
            'presensi' => $presensi,
            'siswas' => $siswas,
            'gurus' => $gurus,
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
            'id_mapel' => 'required|exists:mapel,id',
            'id_jadwal_pelajaran' => 'required|exists:jadwal_pelajaran,id',
            'jam' => 'required|date_format:H:i',
            'status' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
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
