<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\PenilaianAkademik;
use Modules\Siswa\Models\Siswa as ModelsSiswa;
use Modules\Guru\Models\Guru as ModelsGuru;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\TahunAjaran;

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
        $penilaianAkademiks = PenilaianAkademik::with(['siswa', 'guru', 'mataPelajaran', 'tahunAjaran'])->paginate(10);

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
        // master data order: akademik -> guru -> siswa
        $mapels = MataPelajaran::orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();
        $gurus = ModelsGuru::orderBy('nama')->get();
        $siswas = ModelsSiswa::orderBy('nama')->get();

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
        // Validate master data
        $request->validate([
            'id_guru' => 'required|exists:guru,id',
            'id_mapel' => 'required|exists:mata_pelajaran,id',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id',
        ]);

        // Validate penilaian array
        $request->validate([
            'penilaian' => 'required|array|min:1',
            'penilaian.*.id_siswa' => 'required|exists:siswa,id',
            'penilaian.*.nilai' => 'required|integer|min:0|max:100',
        ], [
            'penilaian.required' => 'Minimal harus ada 1 penilaian.',
            'penilaian.min' => 'Minimal harus ada 1 penilaian.',
            'penilaian.*.id_siswa.required' => 'Pilih siswa untuk setiap baris.',
            'penilaian.*.id_siswa.exists' => 'Siswa tidak ditemukan.',
            'penilaian.*.nilai.required' => 'Nilai tidak boleh kosong.',
            'penilaian.*.nilai.integer' => 'Nilai harus berupa angka.',
            'penilaian.*.nilai.min' => 'Nilai minimal adalah 0.',
            'penilaian.*.nilai.max' => 'Nilai maksimal adalah 100.',
        ]);

        // Store multiple penilaian
        $stored = 0;
        foreach ($request->penilaian as $item) {
            PenilaianAkademik::create([
                'id_siswa' => $item['id_siswa'],
                'id_guru' => $request->id_guru,
                'id_mapel' => $request->id_mapel,
                'id_tahun_ajaran' => $request->id_tahun_ajaran,
                'nilai' => $item['nilai'],
            ]);
            $stored++;
        }

        return redirect()->route('penilaiandanpresensi.penilaianakademik.index')
            ->with('success', "Total {$stored} penilaian berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $penilaianAkademik = PenilaianAkademik::with(['siswa', 'guru', 'mataPelajaran', 'tahunAjaran'])->findOrFail($id);

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
        $mapels = MataPelajaran::orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();
        $gurus = ModelsGuru::orderBy('nama')->get();
        $siswas = ModelsSiswa::orderBy('nama')->get();

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
            'id_mapel' => 'required|exists:mata_pelajaran,id',
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
