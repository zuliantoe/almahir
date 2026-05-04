<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\PenilaianAkademik;
use Modules\Siswa\Models\Siswa as ModelsSiswa;
use Modules\Guru\Models\Guru as ModelsGuru;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\JadwalPelajaran;
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
        $query = PenilaianAkademik::with(['siswa', 'guru', 'mataPelajaran', 'tahunAjaran']);
        
        // Filter for students: they only see their own scores
        if (auth()->user()->ref_type === ModelsSiswa::class) {
            $siswaId = auth()->user()->ref_id;
            $query->where('id_siswa', $siswaId);
        }

        // Apply dynamic filters
        if ($request->filled('id_mapel')) {
            $query->where('id_mapel', $request->id_mapel);
        }

        if ($request->filled('id_tahun_ajaran')) {
            $query->where('id_tahun_ajaran', $request->id_tahun_ajaran);
        } else {
            // Default to active year if no filter is selected
            $activeTA = TahunAjaran::where('status', 'aktif')->first();
            if ($activeTA) {
                $query->where('id_tahun_ajaran', $activeTA->id);
            }
        }

        $penilaianAkademiks = $query->latest()->paginate(10);
        $allMapels = MataPelajaran::orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();

        return view('penilaiandanpresensi::penilaianakademik.index', [
            'title' => 'Daftar Penilaian Akademik',
            'penilaianAkademiks' => $penilaianAkademiks,
            'allMapels' => $allMapels,
            'tahunAjarans' => $tahunAjarans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        if (auth()->user()->ref_type === ModelsSiswa::class) {
            abort(403, 'Akses ditolak. Santri tidak diperbolehkan menginput nilai.');
        }

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $user = auth()->user();
        $isGuru = $user->ref_type === ModelsGuru::class;
        $loggedGuruId = $isGuru ? $user->ref_id : null;

        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if (!$activeTahunAjaran) {
            $activeTahunAjaran = TahunAjaran::orderBy('tahunajaran', 'desc')->first();
        }

        $gurus = ModelsGuru::orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();
        
        // Show ALL mapels for better usability in demo/setup phase
        $mapels = MataPelajaran::with('kategori')->orderBy('nama')->get();

        $siswas = ModelsSiswa::orderBy('nama')->get();
        $jadwals = JadwalPelajaran::with(['mataPelajaran.kategori'])->get();
        $allMapels = $mapels;

        return view('penilaiandanpresensi::penilaianakademik.create', [
            'title' => 'Tambah Penilaian Akademik',
            'kelas' => $kelas,
            'siswas' => $siswas,
            'gurus' => $gurus,
            'mapels' => $mapels,
            'allMapels' => $allMapels,
            'tahunAjarans' => $tahunAjarans,
            'jadwals' => $jadwals,
            'activeTahunAjaran' => $activeTahunAjaran,
            'isGuru' => $isGuru,
            'loggedGuruId' => $loggedGuruId,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->ref_type === ModelsSiswa::class) {
            abort(403, 'Akses ditolak.');
        }
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'id_guru' => 'required|exists:guru,id',
            'id_mapel' => 'required|exists:mata_pelajaran,id',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        $request->validate([
            'penilaian' => 'required|array|min:1',
            'penilaian.*.id_siswa' => 'required|exists:siswa,id',
            'penilaian.*.nilai' => 'nullable|integer|min:0|max:100',
        ], [
            'penilaian.required' => 'Data santri tidak ditemukan.',
            'penilaian.*.nilai.integer' => 'Nilai harus berupa angka.',
            'penilaian.*.nilai.min' => 'Nilai minimal adalah 0.',
            'penilaian.*.nilai.max' => 'Nilai maksimal adalah 100.',
        ]);

        $stored = 0;
        foreach ($request->penilaian as $item) {
            // Only save if value is provided
            if ($item['nilai'] !== null && $item['nilai'] !== '') {
                PenilaianAkademik::create([
                    'id_siswa' => $item['id_siswa'],
                    'id_guru' => $request->id_guru,
                    'id_mapel' => $request->id_mapel,
                    'id_tahun_ajaran' => $request->id_tahun_ajaran,
                    'nilai' => $item['nilai'],
                    'kkm' => $request->kkm,
                ]);
                $stored++;
            }
        }

        if ($stored === 0) {
            return back()->withInput()->with('error', 'Silakan masukkan minimal satu nilai santri.');
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
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $user = auth()->user();
        $isGuru = $user->ref_type === ModelsGuru::class;
        $loggedGuruId = $isGuru ? $user->ref_id : null;

        $gurus = ModelsGuru::orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();
        $allMapels = MataPelajaran::with('kategori')->orderBy('nama')->get();
        
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first() ?: TahunAjaran::orderBy('tahunajaran', 'desc')->first();

        // Get mapels for this guru specifically if needed
        if ($isGuru) {
            $myMapelIds = JadwalPelajaran::where('guru_id', $loggedGuruId)->pluck('mapel_id')->unique();
            $mapels = MataPelajaran::whereIn('id', $myMapelIds)->with('kategori')->orderBy('nama')->get();
        } else {
            $mapels = $allMapels;
        }

        $siswas = ModelsSiswa::orderBy('nama')->get();
        $jadwals = JadwalPelajaran::with(['mataPelajaran.kategori'])->get();

        return view('penilaiandanpresensi::penilaianakademik.edit', [
            'title' => 'Edit Penilaian Akademik',
            'penilaianAkademik' => $penilaianAkademik,
            'kelas' => $kelas,
            'siswas' => $siswas,
            'gurus' => $gurus,
            'mapels' => $mapels,
            'allMapels' => $allMapels,
            'tahunAjarans' => $tahunAjarans,
            'jadwals' => $jadwals,
            'activeTahunAjaran' => $activeTahunAjaran,
            'isGuru' => $isGuru,
            'loggedGuruId' => $loggedGuruId,
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
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        $penilaianAkademik = PenilaianAkademik::findOrFail($id);
        $penilaianAkademik->update([
            'id_siswa' => $validated['id_siswa'],
            'id_guru' => $validated['id_guru'],
            'id_mapel' => $validated['id_mapel'],
            'id_tahun_ajaran' => $validated['id_tahun_ajaran'],
            'nilai' => $validated['nilai'],
            'kkm' => $validated['kkm'],
        ]);

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
