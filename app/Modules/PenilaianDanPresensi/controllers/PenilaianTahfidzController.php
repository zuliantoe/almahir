<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\PenilaianTahfidz;
use App\Models\Siswa;
use App\Models\Guru;
use Modules\Guru\Models\Guru as ModelsGuru;
use Modules\Siswa\Models\Siswa as ModelsSiswa;

/**
 * PenilaianTahfidzController
 *
 * CRUD operations for PenilaianTahfidz module.
 */
class PenilaianTahfidzController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $penilaianTahfidzs = PenilaianTahfidz::with(['siswa', 'guru'])->paginate(10);

        return view('penilaiandanpresensi::penilaiantahfidz.index', [
            'title' => 'Daftar Penilaian Tahfidz',
            'penilaianTahfidzs' => $penilaianTahfidzs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $siswas = ModelsSiswa::all();
        $gurus = ModelsGuru::all();

        return view('penilaiandanpresensi::penilaiantahfidz.create', [
            'title' => 'Tambah Penilaian Tahfidz',
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
            'id_kelas' => 'required|integer',
            'tanggal' => 'required|date',
            'surat_awal' => 'required|string|max:255',
            'surat_akhir' => 'required|string|max:255',
            'ayat_awal' => 'required|integer|min:1',
            'ayat_akhir' => 'required|integer|min:1',
            'id_guru' => 'required|exists:guru,id',
            'nilai' => 'required|integer|min:0|max:100',
        ]);

        PenilaianTahfidz::create($validated);

        return redirect()->route('penilaiandanpresensi.penilaiantahfidz.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $penilaianTahfidz = PenilaianTahfidz::with(['siswa', 'guru'])->findOrFail($id);

        return view('penilaiandanpresensi::penilaiantahfidz.show', [
            'title' => 'Detail Penilaian Tahfidz',
            'penilaianTahfidz' => $penilaianTahfidz,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $penilaianTahfidz = PenilaianTahfidz::findOrFail($id);
        $siswas = ModelsSiswa::all();
        $gurus = ModelsGuru::all();

        return view('penilaiandanpresensi::penilaiantahfidz.edit', [
            'title' => 'Edit Penilaian Tahfidz',
            'penilaianTahfidz' => $penilaianTahfidz,
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
            'id_kelas' => 'required|integer',
            'tanggal' => 'required|date',
            'surat_awal' => 'required|string|max:255',
            'surat_akhir' => 'required|string|max:255',
            'ayat_awal' => 'required|integer|min:1',
            'ayat_akhir' => 'required|integer|min:1',
            'id_guru' => 'required|exists:guru,id',
            'nilai' => 'required|integer|min:0|max:100',
        ]);

        $penilaianTahfidz = PenilaianTahfidz::findOrFail($id);
        $penilaianTahfidz->update($validated);

        return redirect()->route('penilaiandanpresensi.penilaiantahfidz.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $penilaianTahfidz = PenilaianTahfidz::findOrFail($id);
        $penilaianTahfidz->delete();

        return redirect()->route('penilaiandanpresensi.penilaiantahfidz.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
