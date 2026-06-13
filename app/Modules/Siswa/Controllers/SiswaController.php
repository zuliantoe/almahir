<?php

namespace Modules\Siswa\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Siswa\Models\Siswa;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;

/**
 * SiswaController
 *
 * Handles all student-related operations within the Siswa module.
 * This is a sample controller demonstrating the modular architecture.
 *
 * @author SIAKAD Development Team
 */
class SiswaController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(): View
    {
        $siswas = \Modules\Siswa\Models\Siswa::with('kelas')->orderBy('nama')->get();
        return view('siswa::index', [
            'title' => 'Data Siswa',
            'breadcrumb' => 'Siswa / Daftar',
            'siswas' => $siswas,
        ]);
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(): View
    {
        $tahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::orderBy('id', 'desc')->get();
        $pendaftaranDiterima = \Modules\Pendaftaran\Models\Pendaftaran::where('status', 'diterima')
            ->whereDoesntHave('siswa')
            ->get();

        return view('siswa::create', [
            'title' => 'Tambah Siswa Baru',
            'breadcrumb' => 'Siswa / Tambah',
            'tahunAjaran' => $tahunAjaran,
            'pendaftaranDiterima' => $pendaftaranDiterima,
        ]);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswa,nis',
            'email' => 'required|email|unique:siswa,email',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'tahun_masuk' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $tahun_masuk = explode('/', $validated['tahun_masuk']);
        $validated['tahun_masuk'] = $tahun_masuk[0];
        $validated['status'] = 'aktif'; // default status for new student

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('siswa/foto', 'public');
        }

        $siswa = Siswa::create($validated);

        if ($request->filled('pendaftaran_id')) {
            $siswa->update(['pendaftaran_id' => $request->pendaftaran_id]);
        }

        return redirect()->route('siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified student.
     */
    public function show(string $id): View
    {
        $siswa = Siswa::findOrFail($id);

        $tahunAjarans      = TahunAjaran::orderBy('tahunajaran', 'desc')->get();
        $activeTahunAjaran = TahunAjaran::aktif()->first();

        // Hari disimpan sebagai string di DB ('Senin','Selasa',dst)
        $hariList  = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $todayName = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][\Carbon\Carbon::now()->dayOfWeekIso - 1] ?? '';

        $rombelSiswa = RombelSiswa::with(['rombel.kelas'])
            ->where('siswa_id', $siswa->id)
            ->when($activeTahunAjaran, function ($q) use ($activeTahunAjaran) {
                return $q->whereHas('rombel', fn($sq) => $sq->where('tahunajaran_id', $activeTahunAjaran->id));
            })
            ->first();

        $rawJadwal = collect();
        if ($rombelSiswa?->rombel_id) {
            $rawJadwal = JadwalPelajaran::with(['mataPelajaran', 'guru'])
                ->where('rombel_id', $rombelSiswa->rombel_id)
                ->orderBy('hari')
                ->orderBy('jamke')
                ->get();
        }

        $timetable = [];
        foreach ($rawJadwal as $j) {
            $timetable[$j->hari][$j->jamke] = $j;
        }
        $usedJamKes = $rawJadwal->pluck('jamke')->unique()->sort()->values()->toArray();

        return view('siswa::show', [
            'title'             => 'Detail Siswa',
            'breadcrumb'        => 'Siswa / Detail',
            'siswa'             => $siswa,
            'rombelSiswa'       => $rombelSiswa,
            'rawJadwal'         => $rawJadwal,
            'timetable'         => $timetable,
            'usedJamKes'        => $usedJamKes,
            'hariList'          => $hariList,
            'todayName'         => $todayName,
            'tahunAjarans'      => $tahunAjarans,
            'activeTahunAjaran' => $activeTahunAjaran,
        ]);
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(string $id): View
    {
        $siswa = Siswa::findOrFail($id);
        $tahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::orderBy('id', 'desc')->get();

        return view('siswa::edit', [
            'title' => 'Edit Siswa',
            'breadcrumb' => 'Siswa / Edit',
            'siswa' => $siswa,
            'tahunAjaran' => $tahunAjaran,
        ]);
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswa,nis,' . $id,
            'email' => 'required|email|unique:siswa,email,' . $id,
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tahun_masuk' => 'nullable|string',
            'status' => 'nullable|in:aktif,lulus,keluar,cuti',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $siswa = Siswa::findOrFail($id);
        if (!empty($validated['tahun_masuk'])) {
            $tahun_masuk = explode('/', $validated['tahun_masuk']);
            $validated['tahun_masuk'] = $tahun_masuk[0];
        } else {
            $validated['tahun_masuk'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($siswa->foto);
            }
            $validated['foto'] = $request->file('foto')->store('siswa/foto', 'public');
        }

        $siswa->update($validated);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
