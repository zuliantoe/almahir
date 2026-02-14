<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\IzinSakit;
use App\Models\Siswa;
use Modules\Siswa\Models\Siswa as ModelsSiswa;

/**
 * IzinSakitController
 *
 * CRUD operations for IzinSakit module.
 */
class IzinSakitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $izinSakits = IzinSakit::with(['siswa'])->paginate(10);

        return view('penilaiandanpresensi::izinsakit.index', [
            'title' => 'Daftar Izin Sakit',
            'izinSakits' => $izinSakits,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $siswas = ModelsSiswa::all();

        return view('penilaiandanpresensi::izinsakit.create', [
            'title' => 'Tambah Izin Sakit',
            'siswas' => $siswas,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'id_kelas' => 'required|integer',
            'jenis' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        IzinSakit::create($validated);

        return redirect()->route('penilaiandanpresensi.izinsakit.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $izinSakit = IzinSakit::with(['siswa'])->findOrFail($id);

        return view('penilaiandanpresensi::izinsakit.show', [
            'title' => 'Detail Izin Sakit',
            'izinSakit' => $izinSakit,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $izinSakit = IzinSakit::findOrFail($id);
        $siswas = ModelsSiswa::all();

        return view('penilaiandanpresensi::izinsakit.edit', [
            'title' => 'Edit Izin Sakit',
            'izinSakit' => $izinSakit,
            'siswas' => $siswas,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'id_kelas' => 'required|integer',
            'jenis' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        $izinSakit = IzinSakit::findOrFail($id);
        $izinSakit->update($validated);

        return redirect()->route('penilaiandanpresensi.izinsakit.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $izinSakit = IzinSakit::findOrFail($id);
        $izinSakit->delete();

        return redirect()->route('penilaiandanpresensi.izinsakit.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
