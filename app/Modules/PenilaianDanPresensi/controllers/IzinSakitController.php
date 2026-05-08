<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\IzinSakit;
use Modules\Siswa\Models\Siswa as ModelsSiswa;
use App\Modules\Akademik\Models\Kelas as AkademikKelas;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\RombelSiswa;

/**
 * IzinSakitController
 *
 * CRUD operations for IzinSakit module.
 */
class IzinSakitController extends Controller
{
    /**
     * Display a listing of the resource for Siswa.
     */
    public function siswaIndex(Request $request): View
    {
        $user = auth()->user();
        if ($user->ref_type !== \Modules\Siswa\Models\Siswa::class) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Siswa.');
        }

        $siswa = ModelsSiswa::find($user->ref_id);
        $query = IzinSakit::with(['mataPelajaran'])
            ->where('siswa_id', $siswa->id);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $filterTanggal = $request->tanggal;
            $query->whereDate('tgl_mulai', '<=', $filterTanggal)
                  ->whereDate('tgl_selesai', '>=', $filterTanggal);
        }
        
        $activeTahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        $izinSakits = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('penilaiandanpresensi::izinsakit.siswa_index', [
            'title' => 'Riwayat Pengajuan Izin/Sakit',
            'izinSakits' => $izinSakits,
            'activeTahunAjaran' => $activeTahunAjaran,
        ]);
    }

    /**
     * Show the form for creating a new resource for Siswa.
     */
    public function siswaCreate(): View
    {
        $user = auth()->user();
        if ($user->ref_type !== \Modules\Siswa\Models\Siswa::class) {
            abort(403, 'Akses ditolak.');
        }
        $siswa = ModelsSiswa::find($user->ref_id);

        // Find student's active rombel
        $activeRombel = RombelSiswa::where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->whereHas('rombel', function($q) use ($activeTA) {
                $q->where('tahunajaran_id', $activeTA->id ?? 0);
            })->first();
        $rombelId = $activeRombel ? $activeRombel->rombel_id : null;

        $jadwalsQuery = JadwalPelajaran::with('mataPelajaran')
            ->where('rombel_id', $rombelId);
            
        $jadwals = $jadwalsQuery->get();
            
        $mapels = $jadwals->pluck('mataPelajaran')->filter()->unique('id');

        // Fallback if schedule is empty
        if ($mapels->isEmpty()) {
            $mapels = MataPelajaran::orderBy('nama')->get();
        }

        return view('penilaiandanpresensi::izinsakit.siswa_create', [
            'title' => 'Pengajuan Izin/Sakit Baru',
            'mapels' => $mapels,
        ]);
    }

    /**
     * Store a newly created resource in storage for Siswa.
     */
    public function siswaStore(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if ($user->ref_type !== \Modules\Siswa\Models\Siswa::class) {
            abort(403, 'Akses ditolak.');
        }
        $siswa = ModelsSiswa::find($user->ref_id);

        $validated = $request->validate([
            'jenis' => 'required|in:Izin,Sakit',
            'tipe_izin' => 'required|in:Harian,Per Matpel',
            'mapel_id' => 'nullable|required_if:tipe_izin,Per Matpel|exists:mata_pelajaran,id',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'nullable|string',
            'bukti_foto' => 'nullable|image|max:2048',
        ]);

        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        
        $activeRombel = RombelSiswa::where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->whereHas('rombel', function($q) use ($activeTA) {
                $q->where('tahunajaran_id', $activeTA->id ?? 0);
            })->first();

        $validated['siswa_id'] = $siswa->id;
        $validated['kelas_id'] = $activeRombel ? $activeRombel->rombel_id : ($siswa->kelas_id ?? 1);

        if ($validated['tipe_izin'] === 'Per Matpel') {
            $validated['tgl_selesai'] = $validated['tgl_mulai']; // Force single day
        } else {
            $validated['mapel_id'] = null; // Clean up just in case
        }

        if ($request->hasFile('bukti_foto')) {
            $validated['bukti_foto'] = $request->file('bukti_foto')->store('izin_sakit', 'public');
        } elseif ($request->filled('captured_image')) {
            $imageData = $request->input('captured_image');
            $imageName = 'captured_' . time() . '.jpg';
            $imagePath = 'izin_sakit/' . $imageName;
            
            $data = explode(',', $imageData);
            if (isset($data[1])) {
                \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, base64_decode($data[1]));
                $validated['bukti_foto'] = $imagePath;
            }
        }
        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        $validated['tahunajaran_id'] = $activeTA->id ?? null;
        $validated['semester'] = $activeTA->semester ?? null;
        $validated['author_id'] = auth()->id();

        IzinSakit::create($validated);

        return redirect()->route('penilaiandanpresensi.izinsakit.siswa.index')
            ->with('success', 'Pengajuan berhasil dikirim.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = auth()->user();
        if ($user->ref_type === \Modules\Siswa\Models\Siswa::class) {
            return redirect()->route('penilaiandanpresensi.izinsakit.siswa.index');
        }

        $query = IzinSakit::with(['siswa', 'rombel']);

        // Apply filters
        if ($request->filled('rombel_id')) {
            $query->where('kelas_id', $request->rombel_id);
        }

        if ($request->filled('kelas_id')) {
            $kelasId = $request->kelas_id;
            $query->whereHas('rombel', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $filterTanggal = $request->tanggal;
            $query->whereDate('tgl_mulai', '<=', $filterTanggal)
                  ->whereDate('tgl_selesai', '>=', $filterTanggal);
        }

        // Order by status (Pending first) and then date
        $izinSakits = $query->orderByRaw("CASE WHEN status = 'Pending' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $activeTahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        $rombels = Rombel::where('tahunajaran_id', $activeTahunAjaran->id ?? 0)->orderBy('nama_rombel')->get();

        return view('penilaiandanpresensi::izinsakit.index', [
            'title' => 'Konfirmasi Izin & Sakit',
            'izinSakits' => $izinSakits,
            'activeTahunAjaran' => $activeTahunAjaran,
            'rombels' => $rombels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        if (auth()->user()->ref_type === \Modules\Siswa\Models\Siswa::class) {
            return redirect()->route('penilaiandanpresensi.izinsakit.siswa.create');
        }

        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        $rombels = Rombel::where('tahunajaran_id', $activeTA->id ?? 0)->orderBy('nama_rombel')->get();
        $siswas = ModelsSiswa::orderBy('nama')->get();

        return view('penilaiandanpresensi::izinsakit.create', [
            'title' => 'Tambah Izin Sakit',
            'siswas' => $siswas,
            'rombels' => $rombels,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'rombel_id' => 'required|exists:rombel,id',
            'jenis' => 'required|string|max:255',
            'tipe_izin' => 'nullable|string|in:Harian,Per Matpel',
            'mapel_id' => 'nullable|exists:mata_pelajaran,id',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'nullable|string',
            'bukti_foto' => 'nullable|image|max:2048',
        ]);

        if (empty($validated['tipe_izin'])) {
            $validated['tipe_izin'] = 'Harian';
        }
        
        if ($validated['tipe_izin'] === 'Per Matpel') {
            $validated['tgl_selesai'] = $validated['tgl_mulai'];
        } else {
            $validated['mapel_id'] = null;
        }

        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        $validated['tahunajaran_id'] = $activeTA->id ?? null;
        $validated['semester'] = $activeTA->semester ?? null;
        $validated['author_id'] = auth()->id();

        $validated['kelas_id'] = $request->rombel_id;
        IzinSakit::create($validated);

        return redirect()->route('penilaiandanpresensi.izinsakit.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $izinSakit = IzinSakit::with(['siswa', 'rombel'])->findOrFail($id);

        return view('penilaiandanpresensi::izinsakit.show', [
            'title' => 'Detail Izin Sakit',
            'izinSakit' => $izinSakit,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View|RedirectResponse
    {
        if (auth()->user()->ref_type === \Modules\Siswa\Models\Siswa::class) {
            return redirect()->route('penilaiandanpresensi.izinsakit.siswa.edit', $id);
        }

        $izinSakit = IzinSakit::findOrFail($id);
        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        $rombels = Rombel::where('tahunajaran_id', $activeTA->id ?? 0)->orderBy('nama_rombel')->get();
        $siswas = ModelsSiswa::orderBy('nama')->get();

        return view('penilaiandanpresensi::izinsakit.edit', [
            'title' => 'Edit Izin Sakit',
            'izinSakit' => $izinSakit,
            'siswas' => $siswas,
            'rombels' => $rombels,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        if (auth()->user()->ref_type === \Modules\Siswa\Models\Siswa::class) {
            return redirect()->route('penilaiandanpresensi.izinsakit.siswa.update', $id);
        }

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'rombel_id' => 'required|exists:rombel,id',
            'jenis' => 'required|string|max:255',
            'tipe_izin' => 'nullable|string|in:Harian,Per Matpel',
            'mapel_id' => 'nullable|exists:mata_pelajaran,id',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'nullable|string',
            'bukti_foto' => 'nullable|image|max:2048',
        ]);

        if (empty($validated['tipe_izin'])) {
            $validated['tipe_izin'] = 'Harian';
        }

        if ($validated['tipe_izin'] === 'Per Matpel') {
            $validated['tgl_selesai'] = $validated['tgl_mulai'];
        } else {
            $validated['mapel_id'] = null;
        }

        if ($request->hasFile('bukti_foto')) {
            $validated['bukti_foto'] = $request->file('bukti_foto')->store('izin_sakit', 'public');
        } elseif ($request->filled('captured_image')) {
            $imageData = $request->input('captured_image');
            $imageName = 'captured_' . time() . '.jpg';
            $imagePath = 'izin_sakit/' . $imageName;
            
            $data = explode(',', $imageData);
            if (isset($data[1])) {
                \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, base64_decode($data[1]));
                $validated['bukti_foto'] = $imagePath;
            }
        }

        $izinSakit = IzinSakit::findOrFail($id);
        $validated['kelas_id'] = $request->rombel_id;
        $izinSakit->update($validated);

        return redirect()->route('penilaiandanpresensi.izinsakit.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        if (auth()->user()->ref_type === \Modules\Siswa\Models\Siswa::class) {
            return redirect()->route('penilaiandanpresensi.izinsakit.siswa.destroy', $id);
        }

        $izinSakit = IzinSakit::findOrFail($id);
        $izinSakit->delete();

        return redirect()->route('penilaiandanpresensi.izinsakit.index')
            ->with('success', 'Data berhasil dihapus.');
    }
    /**
     * Confirm or Reject Izin Sakit pengajuan.
     */
    public function confirm(Request $request, string $id): RedirectResponse
    {
        if (auth()->user()->ref_type === \Modules\Siswa\Models\Siswa::class) {
            abort(403, 'Anda tidak memiliki otoritas untuk mengonfirmasi pengajuan.');
        }

        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $izinSakit = IzinSakit::findOrFail($id);
        $izinSakit->update([
            'status' => $request->status,
            'konfirmasi_oleh' => auth()->id(),
            'waktu_konfirmasi' => now(),
        ]);

        if ($request->status === 'Disetujui') {
            // Sinkronisasi ke tabel Presensi
            $tglMulai = \Carbon\Carbon::parse($izinSakit->tgl_mulai);
            $tglSelesai = \Carbon\Carbon::parse($izinSakit->tgl_selesai);

            for ($date = $tglMulai; $date->lte($tglSelesai); $date->addDay()) {
                $hariAngka = $date->format('N'); // 1 = Senin, 7 = Minggu

                $queryJadwal = JadwalPelajaran::where('rombel_id', $izinSakit->kelas_id)
                    ->where('hari', $hariAngka);

                if ($izinSakit->tipe_izin === 'Per Matpel' && $izinSakit->mapel_id) {
                    $queryJadwal->where('mapel_id', $izinSakit->mapel_id);
                }

                $jadwals = $queryJadwal->get();

                foreach ($jadwals as $jadwal) {
                    // Check if presensi already exists to avoid duplicates
                    $existingPresensi = \Modules\PenilaianDanPresensi\Models\Presensi::where('siswa_id', $izinSakit->siswa_id)
                        ->where('jadwal_pelajaran_id', $jadwal->id)
                        ->whereDate('created_at', $date->format('Y-m-d'))
                        ->first();

                    if (!$existingPresensi) {
                        \Modules\PenilaianDanPresensi\Models\Presensi::create([
                            'siswa_id' => $izinSakit->siswa_id,
                            'guru_id' => $jadwal->guru_id ?? auth()->id(),
                            'mapel_id' => $jadwal->mapel_id,
                            'jadwal_pelajaran_id' => $jadwal->id,
                            'jam' => $date->format('Y-m-d') . ' ' . $jadwal->jamawal,
                            'status' => $izinSakit->jenis, // 'Izin' or 'Sakit'
                            'kategori' => 'Sekolah',
                            'created_at' => $date->format('Y-m-d H:i:s'),
                            'updated_at' => now(),
                        ]);
                    } else {
                        // If exists, just update the status
                        $existingPresensi->update([
                            'status' => $izinSakit->jenis,
                        ]);
                    }
                }
            }
        }

        $message = $request->status === 'Disetujui' ? 'Pengajuan telah disetujui dan disinkronkan ke Presensi.' : 'Pengajuan telah ditolak.';
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Show the form for editing for Siswa.
     */
    public function siswaEdit(string $id): View
    {
        $user = auth()->user();
        $izinSakit = IzinSakit::where('id', $id)->where('siswa_id', $user->ref_id)->firstOrFail();

        if ($izinSakit->status !== 'Pending') {
            abort(403, 'Hanya pengajuan dengan status Pending yang bisa diedit.');
        }

        $siswa = ModelsSiswa::find($user->ref_id);
        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();

        $jadwalsQuery = JadwalPelajaran::with('mataPelajaran')
            ->where('rombel_id', $siswa->kelas_id);
            
        if ($activeTA) {
            $jadwalsQuery->whereHas('rombel', function($q) use ($activeTA) {
                $q->where('tahunajaran_id', $activeTA->id);
            });
        }

        $jadwals = $jadwalsQuery->get();
            
        $mapels = $jadwals->pluck('mataPelajaran')->filter()->unique('id');
        if ($mapels->isEmpty()) {
            $mapels = MataPelajaran::orderBy('nama')->get();
        }

        return view('penilaiandanpresensi::izinsakit.siswa_edit', [
            'title' => 'Edit Pengajuan Izin/Sakit',
            'izinSakit' => $izinSakit,
            'mapels' => $mapels,
        ]);
    }

    /**
     * Update for Siswa.
     */
    public function siswaUpdate(Request $request, string $id): RedirectResponse
    {
        $user = auth()->user();
        $izinSakit = IzinSakit::where('id', $id)->where('siswa_id', $user->ref_id)->firstOrFail();

        if ($izinSakit->status !== 'Pending') {
            return back()->with('error', 'Hanya pengajuan dengan status Pending yang bisa diperbarui.');
        }

        $validated = $request->validate([
            'jenis' => 'required|in:Izin,Sakit',
            'tipe_izin' => 'required|in:Harian,Per Matpel',
            'mapel_id' => 'nullable|required_if:tipe_izin,Per Matpel|exists:mata_pelajaran,id',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'nullable|string',
            'bukti_foto' => 'nullable|image|max:2048',
        ]);

        if ($validated['tipe_izin'] === 'Per Matpel') {
            $validated['tgl_selesai'] = $validated['tgl_mulai'];
        } else {
            $validated['mapel_id'] = null;
        }

        if ($request->hasFile('bukti_foto')) {
            $validated['bukti_foto'] = $request->file('bukti_foto')->store('izin_sakit', 'public');
        } elseif ($request->filled('captured_image')) {
            $imageData = $request->input('captured_image');
            $imageName = 'captured_' . time() . '.jpg';
            $imagePath = 'izin_sakit/' . $imageName;
            
            $data = explode(',', $imageData);
            if (isset($data[1])) {
                \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, base64_decode($data[1]));
                $validated['bukti_foto'] = $imagePath;
            }
        }

        $izinSakit->update($validated);

        return redirect()->route('penilaiandanpresensi.izinsakit.siswa.index')
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    /**
     * Delete for Siswa.
     */
    public function siswaDestroy(string $id): RedirectResponse
    {
        $user = auth()->user();
        $izinSakit = IzinSakit::where('id', $id)->where('siswa_id', $user->ref_id)->firstOrFail();

        if ($izinSakit->status !== 'Pending') {
            return back()->with('error', 'Hanya pengajuan dengan status Pending yang bisa dihapus.');
        }

        $izinSakit->delete();

        return redirect()->route('penilaiandanpresensi.izinsakit.siswa.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }
}
