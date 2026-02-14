<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\PenilaianAkademik;
use App\Models\Siswa;
use App\Models\Guru;
use Modules\Akademik\Models\MataPelajaran;
use Modules\Akademik\Models\TahunAjaran;
use Modules\Guru\Models\Guru as ModelsGuru;
use Modules\Siswa\Models\Siswa as ModelsSiswa;

/**
 * PenilaianAkademikController
 *
 * CRUD operations for PenilaianAkademik module.
 */
class PenilaianAkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $penilaianAkademiks = PenilaianAkademik::with(['siswa', 'guru'])->paginate(10);

        return view('penilaiandanpresensi::penilaianakademik.index', [
            'title' => 'Daftar Penilaian Akademik',
            'penilaianAkademiks' => $penilaianAkademiks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $siswas = ModelsSiswa::all();
        $gurus = ModelsGuru::all();
        $mapels = MataPelajaran::all();
        $tahunAjarans = TahunAjaran::all();

        return view('penilaiandanpresensi::penilaianakademik.create', [
            'title' => 'Tambah Penilaian Akademik',
            'siswas' => $siswas,
            'gurus' => $gurus,
            'mapels' => $mapels,
            'tahunAjarans' => $tahunAjarans,
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
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id',
            'nilai' => 'required|integer|min:0|max:100',
        ]);

        PenilaianAkademik::create($validated);

        return redirect()->route('penilaiandanpresensi.penilaianakademik.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $penilaianAkademik = PenilaianAkademik::with(['siswa', 'guru'])->findOrFail($id);

        return view('penilaiandanpresensi::penilaianakademik.show', [
            'title' => 'Detail Penilaian Akademik',
            'penilaianAkademik' => $penilaianAkademik,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $penilaianAkademik = PenilaianAkademik::findOrFail($id);
        $siswas = ModelsSiswa::all();
        $gurus = ModelsGuru::all();
        $mapels = MataPelajaran::all();
        $tahunAjarans = TahunAjaran::all();

        return view('penilaiandanpresensi::penilaianakademik.edit', [
            'title' => 'Edit Penilaian Akademik',
            'penilaianAkademik' => $penilaianAkademik,
            'siswas' => $siswas,
            'gurus' => $gurus,
            'mapels' => $mapels,
            'tahunAjarans' => $tahunAjarans,
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
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id',
            'nilai' => 'required|integer|min:0|max:100',
        ]);

        $penilaianAkademik = PenilaianAkademik::findOrFail($id);
        $penilaianAkademik->update($validated);

        return redirect()->route('penilaiandanpresensi.penilaianakademik.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $penilaianAkademik = PenilaianAkademik::findOrFail($id);
        $penilaianAkademik->delete();

        return redirect()->route('penilaiandanpresensi.penilaianakademik.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
