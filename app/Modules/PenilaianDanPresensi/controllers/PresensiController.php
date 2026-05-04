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
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\JadwalPelajaran;
use Carbon\Carbon;

/**
 * PresensiController
 *
 * CRUD operations for Presensi module.
 */
class PresensiController extends Controller
{
    /**
     * Display presensi page for Siswa (Siswa Login)
     */
    public function siswaIndex(Request $request): View
    {
        $user = auth()->user();
        
        // Ensure only Siswa can access
        if ($user->ref_type !== \Modules\Siswa\Models\Siswa::class) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Siswa.');
        }

        $siswa = ModelsSiswa::find($user->ref_id);
        if (!$siswa) {
            abort(404, 'Data Siswa tidak ditemukan.');
        }

        $activeTahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        $tahunAjaranId = $activeTahunAjaran ? $activeTahunAjaran->id : null;

        $hariIni = date('N'); // 1 = Senin, 7 = Minggu
        
        // Fetch schedule for today. Filtering by active academic year.
        $jadwalsQuery = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->where('hari', $hariIni)
            ->where('rombel_id', $siswa->kelas_id);
            
        if ($tahunAjaranId) {
            $jadwalsQuery->whereHas('rombel', function($q) use ($tahunAjaranId) {
                $q->where('tahunajaran_id', $tahunAjaranId);
            });
        }
        
        $jadwals = $jadwalsQuery->orderBy('jamawal')->get();

        $presensiHariIni = Presensi::where('id_siswa', $siswa->id)
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->keyBy('id_jadwal_pelajaran');

        // --- Monthly Stats Calculation ---
        $attendanceStats = [];
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $allMapelsQuery = JadwalPelajaran::where('rombel_id', $siswa->kelas_id);
        if ($tahunAjaranId) {
            $allMapelsQuery->where('tahunajaran_id', $tahunAjaranId);
        }
        
        $allMapels = $allMapelsQuery->with('mataPelajaran')
            ->get()
            ->pluck('mataPelajaran')
            ->unique('id');

        foreach ($allMapels as $mapel) {
            if (!$mapel) continue;
            
            $scheduledDaysQuery = JadwalPelajaran::where('rombel_id', $siswa->kelas_id)
                ->where('mapel_id', $mapel->id);
            if ($tahunAjaranId) {
                $scheduledDaysQuery->where('tahunajaran_id', $tahunAjaranId);
            }
            $scheduledDays = $scheduledDaysQuery->pluck('hari')->toArray();
            
            $expectedKbm = 0;
            $tempDate = $startOfMonth->copy();
            while ($tempDate <= $endOfMonth && $tempDate <= Carbon::today()) {
                if (in_array($tempDate->dayOfWeekIso, $scheduledDays)) {
                    $expectedKbm++;
                }
                $tempDate->addDay();
            }

            $presentCount = Presensi::where('id_siswa', $siswa->id)
                ->where('id_mapel', $mapel->id)
                ->where('status', 'Hadir')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
            
            $attendanceStats[] = [
                'nama_mapel' => $mapel->nama,
                'present' => $presentCount,
                'total' => $expectedKbm,
                'percentage' => $expectedKbm > 0 ? round(($presentCount / $expectedKbm) * 100) : 0
            ];
        }

        // --- History with Filters ---
        $historyQuery = Presensi::with(['mataPelajaran', 'guru'])
            ->where('id_siswa', $siswa->id);

        if ($request->filled('status')) {
            $historyQuery->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $historyQuery->whereDate('created_at', $request->tanggal);
        }

        $riwayatPresensi = $historyQuery->latest()->paginate(5);

        return view('penilaiandanpresensi::presensi.siswa', [
            'title' => 'Absen Hari Ini',
            'siswa' => $siswa,
            'jadwals' => $jadwals,
            'presensiHariIni' => $presensiHariIni,
            'attendanceStats' => $attendanceStats,
            'riwayatPresensi' => $riwayatPresensi,
            'activeTahunAjaran' => $activeTahunAjaran,
        ]);
    }

    /**
     * Store presensi for Siswa
     */
    public function siswaStore(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if ($user->ref_type !== \Modules\Siswa\Models\Siswa::class) {
            abort(403, 'Akses ditolak.');
        }
        $siswa = ModelsSiswa::find($user->ref_id);

        $validated = $request->validate([
            'id_jadwal_pelajaran' => 'required|exists:jadwal_pelajaran,id',
            'id_guru' => 'required|exists:guru,id',
            'id_mapel' => 'required|exists:mata_pelajaran,id',
        ]);

        $sudahAbsen = Presensi::where('id_siswa', $siswa->id)
            ->where('id_jadwal_pelajaran', $validated['id_jadwal_pelajaran'])
            ->whereDate('created_at', date('Y-m-d'))
            ->exists();

        if ($sudahAbsen) {
            return back()->with('error', 'Anda sudah melakukan absen untuk mata pelajaran ini.');
        }

        Presensi::create([
            'kelas_id' => $siswa->kelas_id ?? 1, // Fallback if null
            'id_siswa' => $siswa->id,
            'id_guru' => $validated['id_guru'],
            'id_mapel' => $validated['id_mapel'],
            'id_jadwal_pelajaran' => $validated['id_jadwal_pelajaran'],
            'jam' => date('H:i'),
            'status' => 'Hadir',
            'kategori' => 'Hadir',
            'scan_id' => null,
        ]);

        return back()->with('success', 'Berhasil melakukan absen.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // AUTO-SYNC EXISTING APPROVED IZIN SAKIT
        $approvedIzins = \Modules\PenilaianDanPresensi\Models\IzinSakit::where('status', 'Disetujui')->get();

        foreach ($approvedIzins as $izinSakit) {
            $tglMulai = \Carbon\Carbon::parse($izinSakit->tgl_mulai);
            $tglSelesai = \Carbon\Carbon::parse($izinSakit->tgl_selesai);

            for ($date = $tglMulai; $date->lte($tglSelesai); $date->addDay()) {
                $hariAngka = $date->format('N'); // 1 = Senin, 7 = Minggu
                $queryJadwal = JadwalPelajaran::where('rombel_id', $izinSakit->id_kelas)
                    ->where('hari', $hariAngka);

                if ($izinSakit->tipe_izin === 'Per Matpel' && $izinSakit->id_mapel) {
                    $queryJadwal->where('mapel_id', $izinSakit->id_mapel);
                }

                $jadwals = $queryJadwal->get();
                foreach ($jadwals as $jadwal) {
                    $existingPresensi = Presensi::where('id_siswa', $izinSakit->id_siswa)
                        ->where('id_jadwal_pelajaran', $jadwal->id)
                        ->whereDate('created_at', $date->format('Y-m-d'))
                        ->first();

                    if (!$existingPresensi) {
                        Presensi::create([
                            'id_siswa' => $izinSakit->id_siswa,
                            'id_guru' => $jadwal->guru_id ?? 1,
                            'id_mapel' => $jadwal->mapel_id,
                            'id_jadwal_pelajaran' => $jadwal->id,
                            'jam' => $date->format('Y-m-d') . ' ' . $jadwal->jamawal,
                            'status' => $izinSakit->jenis,
                            'kategori' => 'Sekolah',
                            'created_at' => $date->format('Y-m-d H:i:s'),
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Ensure it updates to Izin/Sakit if it was previously Hadir
                        if ($existingPresensi->status !== $izinSakit->jenis) {
                            $existingPresensi->update([
                                'status' => $izinSakit->jenis,
                            ]);
                        }
                    }
                }
            }
        }

        $query = Presensi::with(['siswa', 'guru']);
        $statsQuery = Presensi::query();

        // Apply filters to main query
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply filters to BOTH main query and stats query
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
            $statsQuery->where('kategori', $request->kategori);
        }
        
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
            $statsQuery->whereDate('created_at', $request->tanggal);
        }

        $presensis = $query->latest()->paginate(10);

        // Calculate stats based on current date/kategori filters
        $stats = [
            'Hadir' => (clone $statsQuery)->where('status', 'Hadir')->count(),
            'Izin' => (clone $statsQuery)->where('status', 'Izin')->count(),
            'Sakit' => (clone $statsQuery)->where('status', 'Sakit')->count(),
            'Alpha' => (clone $statsQuery)->where('status', 'Alpha')->count(),
            'total' => $statsQuery->count(),
        ];

        $mapels = MataPelajaran::orderBy('nama')->get()->keyBy('id');
        $jadwals = JadwalPelajaran::orderBy('hari')->get()->keyBy('id');

        $activeTahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        return view('penilaiandanpresensi::presensi.index', [
            'title' => 'Daftar Presensi',
            'presensis' => $presensis,
            'stats' => $stats,
            'mapels' => $mapels,
            'jadwals' => $jadwals,
            'activeTahunAjaran' => $activeTahunAjaran,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mapels = MataPelajaran::orderBy('nama')->get();
        $jadwals = JadwalPelajaran::with('mataPelajaran')->orderBy('hari')->orderBy('jamawal')->get();
        $gurus = ModelsGuru::orderBy('nama')->get();
        $siswas = ModelsSiswa::orderBy('nama')->get();

        return view('penilaiandanpresensi::presensi.create', [
            'title' => 'Tambah Presensi',
            'kelas' => $kelas,
            'siswas' => $siswas,
            'gurus' => $gurus,
            'mapels' => $mapels,
            'jadwals' => $jadwals,
        ]);
    }

    /**
     * Display the scanning page.
     */
    public function scanningIndex(): View
    {
        $rombels = Rombel::all();
        $gurus = ModelsGuru::orderBy('nama')->get();
        $mapels = MataPelajaran::orderBy('nama')->get();
        $jadwals = JadwalPelajaran::with(['mataPelajaran', 'guru'])->orderBy('hari')->orderBy('jamawal')->get();

        return view('penilaiandanpresensi::presensi.scanning', [
            'title' => 'Presensi Scanning Kartu',
            'rombels' => $rombels,
            'gurus' => $gurus,
            'mapels' => $mapels,
            'jadwals' => $jadwals,
        ]);
    }

    /**
     * Store presensi via scanning.
     */
    public function scanningStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scan_id' => 'required|string',
            'id_guru' => 'required|exists:guru,id',
            'id_mapel' => 'required|exists:mata_pelajaran,id',
            'id_jadwal_pelajaran' => 'required|exists:jadwal_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $siswa = ModelsSiswa::where('nis', $validated['scan_id'])
            ->orWhere('id', $validated['scan_id'])
            ->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan (NIS: ' . $validated['scan_id'] . ')',
            ], 404);
        }

        // Check if student is in the selected class
        if ($siswa->kelas_id != $validated['kelas_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa ' . $siswa->nama . ' bukan anggota kelas ini.',
            ], 400);
        }

        // Check if already present today for this session
        $exists = Presensi::where('id_siswa', $siswa->id)
            ->where('id_jadwal_pelajaran', $validated['id_jadwal_pelajaran'])
            ->whereDate('created_at', Carbon::today())
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa ' . $siswa->nama . ' SUDAH ABSEN. Anda tidak bisa scan lagi sampai jam pelajaran ini selesai.',
                'data' => [
                    'nama' => $siswa->nama,
                    'jam' => $exists->jam,
                ]
            ], 400);
        }

        $presensi = Presensi::create([
            'kelas_id' => $validated['kelas_id'],
            'id_siswa' => $siswa->id,
            'id_guru' => $validated['id_guru'],
            'id_mapel' => $validated['id_mapel'],
            'id_jadwal_pelajaran' => $validated['id_jadwal_pelajaran'],
            'jam' => Carbon::now()->format('H:i'),
            'status' => 'Hadir',
            'kategori' => 'Hadir',
            'scan_id' => $validated['scan_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Presensi Berhasil!',
            'data' => [
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'jam' => $presensi->jam,
                'foto' => $siswa->foto ? asset('storage/' . $siswa->foto) : null,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->has('bulk_penilaian')) {
            $validated = $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'id_guru' => 'required|exists:guru,id',
                'id_mapel' => 'required|exists:mata_pelajaran,id',
                'id_jadwal_pelajaran' => 'required|exists:jadwal_pelajaran,id',
                'jam' => 'required|date_format:H:i',
                'kategori' => 'required|string|max:255',
                'bulk_penilaian.*.id_siswa' => 'required|exists:siswa,id',
                'bulk_penilaian.*.status' => 'required|string|max:255',
            ]);

            foreach ($request->bulk_penilaian as $item) {
                Presensi::updateOrCreate(
                    [
                        'id_siswa' => $item['id_siswa'],
                        'id_jadwal_pelajaran' => $validated['id_jadwal_pelajaran'],
                        'created_at' => date('Y-m-d') . ' ' . date('H:i:s'), // Simplified, usually use today
                    ],
                    [
                        'kelas_id' => $validated['kelas_id'],
                        'id_guru' => $validated['id_guru'],
                        'id_mapel' => $validated['id_mapel'],
                        'jam' => $validated['jam'],
                        'status' => $item['status'],
                        'kategori' => $validated['kategori'],
                    ]
                );
            }

            return redirect()->route('penilaiandanpresensi.presensi.index')
                ->with('success', 'Data presensi masal berhasil disimpan.');
        }

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
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
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mapels = MataPelajaran::orderBy('nama')->get();
        $jadwals = JadwalPelajaran::with('mataPelajaran')->orderBy('hari')->orderBy('jamawal')->get();
        $gurus = ModelsGuru::orderBy('nama')->get();
        $siswas = ModelsSiswa::orderBy('nama')->get();

        return view('penilaiandanpresensi::presensi.edit', [
            'title' => 'Edit Presensi',
            'presensi' => $presensi,
            'kelas' => $kelas,
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
            'kelas_id' => 'required|exists:kelas,id',
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
