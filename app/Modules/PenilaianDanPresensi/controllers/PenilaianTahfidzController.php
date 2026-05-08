<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\PenilaianTahfidz;
use Modules\Guru\Models\Guru as ModelsGuru;
use Modules\Siswa\Models\Siswa as ModelsSiswa;
use App\Modules\Akademik\Models\kelas as AkademikKelas;
use App\Modules\Akademik\Models\TahunAjaran;

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
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first() ?: TahunAjaran::orderBy('tahunajaran', 'desc')->first();
        $query = PenilaianTahfidz::with(['siswa.kelas', 'guru']);
        
        // Filter for students: they only see their own scores
        if (auth()->user()->ref_type === ModelsSiswa::class) {
            $siswaId = auth()->user()->ref_id;
            $query->where('id_siswa', $siswaId);
        }

        // Apply filters
        if ($request->filled('surah')) {
            $surah = $request->surah;
            $query->where(function($q) use ($surah) {
                $q->where('surat_awal', 'like', "%{$surah}%")
                  ->orWhere('surat_akhir', 'like', "%{$surah}%");
            });
        }

        if ($request->filled('status_capaian')) {
            $query->where('status_capaian', $request->status_capaian);
        }

        $penilaianTahfidzs = $query->latest()->paginate(10);

        return view('penilaiandanpresensi::penilaian-tahfidz.index', [
            'title' => 'Daftar Penilaian Tahfidz',
            'penilaianTahfidzs' => $penilaianTahfidzs,
            'activeTahunAjaran' => $activeTahunAjaran,
            'userRole' => auth()->user()->ref_type,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();
        $isGuru = $user->ref_type === ModelsGuru::class;
        $loggedGuruId = $isGuru ? $user->ref_id : null;

        $kelas = AkademikKelas::orderBy('nama_kelas')->get();
        $gurus = ModelsGuru::orderBy('nama')->get();
        $siswas = ModelsSiswa::with('kelas')->orderBy('nama')->get();
        
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first() ?: TahunAjaran::orderBy('tahunajaran', 'desc')->first();

        return view('penilaiandanpresensi::penilaian-tahfidz.create', [
            'title' => 'Tambah Penilaian Tahfidz',
            'siswas' => $siswas,
            'gurus' => $gurus,
            'kelas' => $kelas,
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
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'id_kelas' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'surat_awal' => 'required|array',
            'surat_awal.*' => 'required|string|max:255',
            'surat_akhir' => 'required|array',
            'surat_akhir.*' => 'required|string|max:255',
            'ayat_awal' => 'required|array',
            'ayat_awal.*' => 'required|integer|min:1',
            'ayat_akhir' => 'required|array',
            'ayat_akhir.*' => 'required|integer|min:1',
            'id_guru' => 'required|exists:guru,id',
            'nilai' => 'required|array',
            'nilai.*' => 'required|integer|min:0|max:100',
            'status_capaian' => 'required|array',
            'status_capaian.*' => 'required|in:Lolos,Tidak Lolos',
        ]);

        // Loop array and insert each record
        $suratAwals = $request->input('surat_awal');
        $suratAkhirs = $request->input('surat_akhir');
        $ayatAwals = $request->input('ayat_awal');
        $ayatAkhirs = $request->input('ayat_akhir');
        $nilais = $request->input('nilai');
        $status_capaians = $request->input('status_capaian');

        foreach ($ayatAwals as $index => $ayatAwal) {
            PenilaianTahfidz::create([
                'id_siswa' => $validated['id_siswa'],
                'id_kelas' => $validated['id_kelas'],
                'id_guru' => $validated['id_guru'],
                'tanggal' => $validated['tanggal'],
                'surat_awal' => $suratAwals[$index],
                'surat_akhir' => $suratAkhirs[$index],
                'ayat_awal' => $ayatAwal,
                'ayat_akhir' => $ayatAkhirs[$index],
                'nilai' => $nilais[$index] ?? 0,
                'status_capaian' => $status_capaians[$index] ?? 'Tidak Lolos',
            ]);
        }

        return redirect()->route('penilaiandanpresensi.penilaiantahfidz.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $penilaianTahfidz = PenilaianTahfidz::with(['siswa', 'guru'])->findOrFail($id);

        return view('penilaiandanpresensi::penilaian-tahfidz.show', [
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
        
        $user = auth()->user();
        $isGuru = $user->ref_type === ModelsGuru::class;
        $loggedGuruId = $isGuru ? $user->ref_id : null;

        $kelas = AkademikKelas::orderBy('nama_kelas')->get();
        $gurus = ModelsGuru::orderBy('nama')->get();
        $siswas = ModelsSiswa::with('kelas')->orderBy('nama')->get();
        
        $activeTahunAjaran = TahunAjaran::where('status', true)->first() ?: TahunAjaran::orderBy('tahunajaran', 'desc')->first();

        return view('penilaiandanpresensi::penilaian-tahfidz.edit', [
            'title' => 'Edit Penilaian Tahfidz',
            'penilaianTahfidz' => $penilaianTahfidz,
            'siswas' => $siswas,
            'gurus' => $gurus,
            'kelas' => $kelas,
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
            'id_kelas' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'surat_awal' => 'required|string|max:255',
            'surat_akhir' => 'required|string|max:255',
            'ayat_awal' => 'required|integer|min:1',
            'ayat_akhir' => 'required|integer|min:1',
            'id_guru' => 'required|exists:guru,id',
            'nilai' => 'required|integer|min:0|max:100',
            'status_capaian' => 'required|in:Lolos,Tidak Lolos',
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
