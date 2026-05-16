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

        // --- CLEANUP PREMATURE ALPHA FOR THIS STUDENT ---
        $currentTime = date('H:i');
        $prematureAlphaIds = Presensi::where('siswa_id', $siswa->id)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'Alpha')
            ->whereHas('jadwalPelajaran', function($q) use ($currentTime) {
                $q->where('jamakhir', '>', $currentTime);
            })
            ->pluck('id');
        
        if ($prematureAlphaIds->count() > 0) {
            Presensi::whereIn('id', $prematureAlphaIds)->delete();
        }

        $hariIni = date('N'); // 1 = Senin, 7 = Minggu
        
        // Get the actual rombel_id from RombelSiswa (Active only)
        $rombelSiswa = \App\Modules\Akademik\Models\RombelSiswa::where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->first();
        $rombelId = $rombelSiswa ? $rombelSiswa->rombel_id : null;
        
        // Fetch schedule for today. Filtering by active academic year.
        $jadwalsQuery = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->where('hari', $hariIni)
            ->where('rombel_id', $rombelId);
            
        if ($tahunAjaranId) {
            $jadwalsQuery->whereHas('rombel', function($q) use ($tahunAjaranId) {
                $q->where('tahunajaran_id', $tahunAjaranId);
            });
        }
        
        $jadwals = $jadwalsQuery->orderBy('jamawal')->get();

        $presensiHariIni = Presensi::where('siswa_id', $siswa->id)
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->keyBy('jadwal_pelajaran_id');

        // --- Monthly Stats Calculation ---
        $attendanceStats = [];
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $allMapelsQuery = JadwalPelajaran::where('rombel_id', $rombelId);
        if ($tahunAjaranId) {
            $allMapelsQuery->whereHas('rombel', function($q) use ($tahunAjaranId) {
                $q->where('tahunajaran_id', $tahunAjaranId);
            });
        }
        
        $allMapels = $allMapelsQuery->with('mataPelajaran')
            ->get()
            ->pluck('mataPelajaran')
            ->unique('id');

        foreach ($allMapels as $mapel) {
            if (!$mapel) continue;
            
            $scheduledDaysQuery = JadwalPelajaran::where('rombel_id', $rombelId)
                ->where('mapel_id', $mapel->id);
            if ($tahunAjaranId) {
                $scheduledDaysQuery->whereHas('rombel', function($q) use ($tahunAjaranId) {
                    $q->where('tahunajaran_id', $tahunAjaranId);
                });
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

            $presentCount = Presensi::where('siswa_id', $siswa->id)
                ->where('mapel_id', $mapel->id)
                ->whereIn('status', ['Hadir', 'Telat'])
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
            ->where('siswa_id', $siswa->id);

        if ($request->filled('status')) {
            $historyQuery->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $historyQuery->whereDate('created_at', $request->tanggal);
        }

        $riwayatPresensi = $historyQuery->latest()->paginate(10);

        return view('penilaiandanpresensi::presensi.siswa', [
            'title' => 'Absensi Hari Ini',
            'siswa' => $siswa,
            'jadwals' => $jadwals,
            'presensiHariIni' => $presensiHariIni,
            'riwayatPresensi' => $riwayatPresensi,
            'attendanceStats' => $attendanceStats
        ]);
    }

    /**
     * Store presensi for Siswa
     */
    public function siswaStore(Request $request)
    {
        $user = auth()->user();
        if ($user->ref_type !== \Modules\Siswa\Models\Siswa::class) {
            if ($request->ajax()) return response()->json(['message' => 'Akses ditolak.'], 403);
            abort(403, 'Akses ditolak.');
        }
        $siswa = ModelsSiswa::find($user->ref_id);

        $jadwalId = $request->jadwal_pelajaran_id;
        $guruId = $request->guru_id;
        $mapelId = $request->mapel_id;

        // Auto-detect if missing (e.g. scanning student ID instead of session QR)
        if (!$jadwalId) {
            $rombelSiswa = \App\Modules\Akademik\Models\RombelSiswa::where('siswa_id', $siswa->id)
                ->where('status', 'aktif')
                ->first();
            if ($rombelSiswa) {
                $now = date('H:i');
                $today = date('N');
                $currentJadwal = \App\Modules\Akademik\Models\JadwalPelajaran::where('rombel_id', $rombelSiswa->rombel_id)
                    ->where('hari', $today)
                    ->where('jamawal', '<=', $now)
                    ->where('jamakhir', '>=', $now)
                    ->first();
                
                if ($currentJadwal) {
                    $jadwalId = $currentJadwal->id;
                    $guruId = $currentJadwal->guru_id;
                    $mapelId = $currentJadwal->mapel_id;
                }
            }
        }

        if (!$jadwalId || !$guruId || !$mapelId) {
            $msg = 'Gagal mendeteksi jadwal aktif. Silakan gunakan tombol "Absen" pada daftar jadwal.';
            if ($request->ajax()) return response()->json(['message' => $msg], 422);
            return back()->with('error', $msg);
        }

        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();

        $existing = Presensi::where('siswa_id', $siswa->id)
            ->where('jadwal_pelajaran_id', $jadwalId)
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        if ($existing && !in_array($existing->status, ['Alpha'])) {
            $msg = 'Anda sudah melakukan absen untuk mata pelajaran ini.';
            if ($request->ajax()) return response()->json(['message' => $msg], 422);
            return back()->with('error', $msg);
        }

        $jadwal = \App\Modules\Akademik\Models\JadwalPelajaran::find($jadwalId);
        $currentTime = date('H:i');

        // Check if lesson has ended
        if ($jadwal && $currentTime > $jadwal->jamakhir) {
            $msg = 'Sesi pelajaran ini sudah berakhir.';
            if ($request->ajax()) return response()->json(['message' => $msg], 422);
            return back()->with('error', $msg);
        }

        // Determine status: check if late
        $status = 'Hadir';
        if ($currentTime > date('H:i', strtotime($jadwal->jamawal . ' + 10 minutes'))) {
            $status = 'Telat';
        }

        if ($existing && $existing->status === 'Alpha') {
            $existing->update([
                'guru_id' => $guruId,
                'jam' => date('H:i'),
                'status' => $status,
                'kategori' => $status === 'Telat' ? 'Telat' : 'Hadir',
                'author_id' => auth()->id(),
            ]);
        } else {
            Presensi::create([
                'siswa_id' => $siswa->id,
                'guru_id' => $guruId,
                'mapel_id' => $mapelId,
                'jadwal_pelajaran_id' => $jadwalId,
                'tahunajaran_id' => $activeTA->id ?? null,
                'semester' => $activeTA->semester ?? null,
                'author_id' => auth()->id(),
                'jam' => date('H:i'),
                'status' => $status,
                'kategori' => $status === 'Telat' ? 'Telat' : 'Hadir',
            ]);
        }

        $successMsg = $status === 'Telat' ? 'Berhasil absen, namun Anda tercatat TERLAMBAT.' : 'Berhasil melakukan absen.';
        if ($request->ajax()) return response()->json(['message' => $successMsg, 'status' => $status]);
        return back()->with('success', $successMsg);
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
                $queryJadwal = JadwalPelajaran::where('rombel_id', $izinSakit->kelas_id)
                    ->where('hari', $hariAngka);

                if ($izinSakit->tipe_izin === 'Per Matpel' && $izinSakit->mapel_id) {
                    $queryJadwal->where('mapel_id', $izinSakit->mapel_id);
                }

                $jadwals = $queryJadwal->get();
                foreach ($jadwals as $jadwal) {
                    $existingPresensi = Presensi::where('siswa_id', $izinSakit->siswa_id)
                        ->where('jadwal_pelajaran_id', $jadwal->id)
                        ->whereDate('created_at', $date->format('Y-m-d'))
                        ->first();

                    if (!$existingPresensi) {
                        Presensi::create([
                            'siswa_id' => $izinSakit->siswa_id,
                            'guru_id' => $jadwal->guru_id ?? 1,
                            'mapel_id' => $jadwal->mapel_id,
                            'jadwal_pelajaran_id' => $jadwal->id,
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

        // --- AUTOMATED ALPHA GENERATION (REAL-TIME) ---
        $targetTanggal = $request->tanggal ?? date('Y-m-d');
        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();
        
        if ($activeTA) {
            $dayOfWeek = date('N', strtotime($targetTanggal));
            $currentTime = date('H:i');
            $isToday = $targetTanggal == date('Y-m-d');

            // Pre-fetch existing for this date to avoid N+1. 
            // Key by student-mapel to handle duplicate schedules gracefully.
            $existingMapels = Presensi::whereDate('created_at', $targetTanggal)
                ->whereIn('status', ['Hadir', 'Telat', 'Izin', 'Sakit'])
                ->get()
                ->pluck('id', fn($item) => $item->siswa_id . '-' . $item->mapel_id)
                ->toArray();
                
            $existingAll = Presensi::whereDate('created_at', $targetTanggal)
                ->get()
                ->pluck('id', fn($item) => $item->siswa_id . '-' . $item->jadwal_pelajaran_id)
                ->toArray();

            // CLEANUP: If it's today, delete Alpha records for schedules that haven't ended yet
            // OR if the student already has a non-Alpha record for that mapel today
            if ($isToday) {
                $prematureAlphaIds = Presensi::whereDate('created_at', $targetTanggal)
                    ->where('status', 'Alpha')
                    ->where(function($q) use ($currentTime, $existingMapels) {
                        $q->whereHas('jadwalPelajaran', function($sq) use ($currentTime) {
                            $sq->where('jamakhir', '>', $currentTime);
                        });
                        // Also delete if they are already present for this mapel (fixes duplicates)
                        foreach ($existingMapels as $key => $id) {
                            $parts = explode('-', $key);
                            $q->orWhere(function($sub) use ($parts) {
                                $sub->where('siswa_id', $parts[0])->where('mapel_id', $parts[1]);
                            });
                        }
                    })
                    ->pluck('id');
                
                if ($prematureAlphaIds->count() > 0) {
                    Presensi::whereIn('id', $prematureAlphaIds)->delete();
                    // Refresh existing keys
                    $existingAll = Presensi::whereDate('created_at', $targetTanggal)
                        ->get()
                        ->pluck('id', fn($item) => $item->siswa_id . '-' . $item->jadwal_pelajaran_id)
                        ->toArray();
                }
            }

            $jadwalQuery = JadwalPelajaran::where('hari', $dayOfWeek);
            if ($request->filled('rombel_id')) {
                $jadwalQuery->where('rombel_id', $request->rombel_id);
            } else {
                $jadwalQuery->whereHas('rombel', function($q) use ($activeTA) {
                    $q->where('tahunajaran_id', $activeTA->id);
                });
            }
            
            $jadwalsForAlpha = $jadwalQuery->get()->groupBy('rombel_id');
            $alphaBatch = [];

            foreach ($jadwalsForAlpha as $rId => $schedules) {
                $rombel = Rombel::with(['aktifSiswa'])->find($rId);
                if (!$rombel) continue;

                foreach ($schedules as $j) {
                    if ($isToday && $currentTime < $j->jamakhir) continue;

                    foreach ($rombel->aktifSiswa as $s) {
                        $key = $s->id . '-' . $j->id;
                        $mapelKey = $s->id . '-' . $j->mapel_id;
                        
                        // Don't create Alpha if:
                        // 1. Specific schedule already has a record
                        // 2. Student is already present for this SUBJECT today (fixes duplicate schedule issue)
                        if (!isset($existingAll[$key]) && !isset($existingMapels[$mapelKey])) {
                            $alphaBatch[] = [
                                'siswa_id' => $s->id,
                                'guru_id' => $j->guru_id ?? 1,
                                'mapel_id' => $j->mapel_id,
                                'jadwal_pelajaran_id' => $j->id,
                                'tahunajaran_id' => $activeTA->id,
                                'semester' => $activeTA->semester,
                                'author_id' => auth()->id() ?? 1,
                                'jam' => $j->jamawal,
                                'status' => 'Alpha',
                                'kategori' => 'Alpha',
                                'created_at' => $targetTanggal . ' ' . $j->jamawal . ':00',
                                'updated_at' => now(),
                            ];
                            // Mark as existing so we don't double-add in the same batch
                            $existingAll[$key] = true;
                            $existingMapels[$mapelKey] = true;

                            if (count($alphaBatch) >= 50) {
                                Presensi::insert($alphaBatch);
                                $alphaBatch = [];
                            }
                        }
                    }
                }
            }
            if (count($alphaBatch) > 0) Presensi::insert($alphaBatch);
        }

        $query = Presensi::with(['siswa', 'guru', 'mataPelajaran', 'tahunAjaran', 'jadwalPelajaran']);

        // Filter for Guru: they see students in their class OR what they input themselves
        if (auth()->user()->ref_type === ModelsGuru::class) {
            $guruId = auth()->user()->ref_id;
            $query->where(function($q) use ($guruId) {
                $q->where('guru_id', $guruId) // Created/taught by them
                  ->orWhereHas('siswa.rombelSiswa', function($sq) use ($guruId) {
                      $sq->where('status', 'aktif')
                         ->whereHas('rombel', function($rq) use ($guruId) {
                             $rq->where('guru_id', $guruId); // Their class as Wali Kelas
                         });
                  });
            });
        }
        
        $statsQuery = clone $query; // Keep stats in sync with filtered results

        // Filter for students: they only see their own attendance
        if (auth()->user()->ref_type === \Modules\Siswa\Models\Siswa::class) {
            $siswaId = auth()->user()->ref_id;
            $query->where('siswa_id', $siswaId);
            $statsQuery->where('siswa_id', $siswaId);
        }

        // Apply filters to main query
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply filters to BOTH main query and stats query
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
            $statsQuery->where('kategori', $request->kategori);
        }
        
        if ($request->filled('rombel_id')) {
            $rombelId = $request->rombel_id;
            $query->whereHas('siswa.rombelSiswa', function($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status', 'aktif');
            });
            $statsQuery->whereHas('siswa.rombelSiswa', function($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status', 'aktif');
            });
        }
        
        // Support legacy kelas_id if needed
        if ($request->filled('kelas_id')) {
            $kelasId = $request->kelas_id;
            $query->whereHas('siswa.kelas', function($q) use ($kelasId) {
                $q->where('id', $kelasId);
            });
            $statsQuery->whereHas('siswa.kelas', function($q) use ($kelasId) {
                $q->where('id', $kelasId);
            });
        }

        if ($request->filled('mapel_id')) {
            $mapel = MataPelajaran::find($request->mapel_id);
            if ($mapel) {
                $mapelIds = MataPelajaran::where('nama', 'like', $mapel->nama)->pluck('id');
                $query->whereIn('mapel_id', $mapelIds);
                $statsQuery->whereIn('mapel_id', $mapelIds);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', 'like', $request->status);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
            $statsQuery->whereDate('created_at', $request->tanggal);
        }

        $activeTahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first() ?: \App\Modules\Akademik\Models\TahunAjaran::orderBy('tahunajaran', 'desc')->first();
        
        $presensis = $query->latest()->paginate(15);

        // Calculate stats based on current filters
        $stats = [
            'Hadir' => (clone $statsQuery)->where('status', 'Hadir')->count(),
            'Telat' => (clone $statsQuery)->where('status', 'Telat')->count(),
            'Izin' => (clone $statsQuery)->where('status', 'Izin')->count(),
            'Sakit' => (clone $statsQuery)->where('status', 'Sakit')->count(),
            'Alpha' => (clone $statsQuery)->where('status', 'Alpha')->count(),
            'total' => $statsQuery->count(),
        ];

        // Get UNIQUE Mapels by name to avoid doubles/triples
        $mapels = MataPelajaran::orderBy('nama')->get()->unique('nama');
        
        $rombels = Rombel::where('tahunajaran_id', $activeTahunAjaran->id ?? 0)
            ->orderBy('nama_rombel')
            ->get();
            
        $jadwals = JadwalPelajaran::orderBy('hari')->get();

        return view('penilaiandanpresensi::presensi.index', [
            'title' => 'Daftar Presensi',
            'presensis' => $presensis,
            'stats' => $stats,
            'mapels' => $mapels,
            'rombels' => $rombels,
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
        
        $hariIni = date('N'); // 1 = Senin, 7 = Minggu
        $jadwals = JadwalPelajaran::with('mataPelajaran')
            ->where('hari', $hariIni)
            ->orderBy('jamawal')
            ->get();
            
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
     * Store presensi via scanning.
     */
    public function scanningStore(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        // --- LOGIC DETECTOR ---
        // If the request is missing 'jadwal_pelajaran_id', it MUST be a student scan from the modal.
        $isStudentScan = !$request->has('jadwal_pelajaran_id');

        // --- CASE 1: SISWA SCANS A SESSION QR ---
        if ($isStudentScan) {
            // Find student record
            $siswa = ModelsSiswa::where('id', $user->ref_id)->first();
            if (!$siswa) {
                // If SUPER_ADMIN is testing, we try to pick any student for testing purposes
                if ($user->hasRole('SUPER_ADMIN')) {
                    $siswa = ModelsSiswa::first();
                } else {
                    return response()->json(['success' => false, 'message' => 'Data Siswa tidak ditemukan.'], 404);
                }
            }

            $validated = $request->validate([
                'scan_id' => 'required|string', 
            ]);

            // For student scan, scan_id IS the Jadwal ID (or the pipe-separated data)
            $rawContent = $validated['scan_id'];
            $jadwalId = $rawContent;

            // Handle pipe-separated content if scanned from image or special QR
            if (str_contains($rawContent, '|')) {
                $parts = explode('|', $rawContent);
                $jadwalId = $parts[0];
            }

            $jadwal = JadwalPelajaran::with(['mataPelajaran', 'guru'])->find($jadwalId);

            // Handle Dummy Schedule for testing
            if (!$jadwal && ($jadwalId == 1 || $jadwalId == 999)) {
                $jadwal = (object)[
                    'id' => $jadwalId,
                    'guru_id' => 1,
                    'mapel_id' => 1,
                    'mataPelajaran' => (object)['nama' => 'Dummy Pelajaran (Test)'],
                    'guru' => (object)['nama' => 'Ustadz Test'],
                ];
            }

            if (!$jadwal) {
                return response()->json(['success' => false, 'message' => 'QR Code tidak valid (Jadwal #' . $jadwalId . ' tidak ditemukan).'], 404);
            }

            // Check if lesson has ended
            $currentTime = date('H:i');
            if ($jadwal->jamakhir && $currentTime > $jadwal->jamakhir) {
                return response()->json(['success' => false, 'message' => 'Sesi pelajaran ' . ($jadwal->mataPelajaran->nama ?? '') . ' sudah berakhir.'], 400);
            }

            // Check if already present today for this session
            $exists = Presensi::where('siswa_id', $siswa->id)
                ->where('jadwal_pelajaran_id', $jadwal->id)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($exists && !in_array($exists->status, ['Alpha'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda SUDAH ABSEN untuk mata pelajaran ' . ($jadwal->mataPelajaran->nama ?? 'ini') . '.',
                ], 400);
            }

            $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();

            // Determine status: check if late (10 mins grace)
            $status = 'Hadir';
            $currentTime = date('H:i');
            if ($jadwal->jamawal && $currentTime > date('H:i', strtotime($jadwal->jamawal . ' + 10 minutes'))) {
                $status = 'Telat';
            }

            if ($exists && $exists->status === 'Alpha') {
                $exists->update([
                    'guru_id' => $jadwal->guru_id,
                    'jam' => Carbon::now()->format('H:i'),
                    'status' => $status,
                    'kategori' => 'Hadir',
                    'author_id' => auth()->id(),
                ]);
                $presensi = $exists;
            } else {
                $presensi = Presensi::create([
                    'siswa_id' => $siswa->id,
                    'guru_id' => $jadwal->guru_id,
                    'mapel_id' => $jadwal->mapel_id,
                    'jadwal_pelajaran_id' => $jadwal->id,
                    'tahunajaran_id' => $activeTA->id ?? null,
                    'semester' => $activeTA->semester ?? null,
                    'author_id' => auth()->id(),
                    'jam' => Carbon::now()->format('H:i'),
                    'status' => $status,
                    'kategori' => 'Hadir',
                    'scan_id' => 'STUDENT_SCAN',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Presensi Berhasil!',
                'data' => [
                    'nama' => $jadwal->mataPelajaran->nama ?? 'Pelajaran',
                    'nis' => 'GURU: ' . ($jadwal->guru->nama ?? 'Ustadz'),
                    'jam' => $presensi->jam,
                    'foto' => null,
                ]
            ]);
        }

        // --- CASE 2: GURU SCANS A STUDENT CARD ---
        $validated = $request->validate([
            'scan_id' => 'required|string', 
            'guru_id' => 'required|exists:guru,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
            'rombel_id' => 'required|exists:kelas,id',
        ]);
        // ... (rest of guru logic remains same)

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
        if ($siswa->kelas_id != $validated['rombel_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa ' . $siswa->nama . ' bukan anggota kelas ini.',
            ], 400);
        }

        // Check if already present today for this session
        $exists = Presensi::where('siswa_id', $siswa->id)
            ->where('jadwal_pelajaran_id', $validated['jadwal_pelajaran_id'])
            ->whereDate('created_at', Carbon::today())
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa ' . $siswa->nama . ' SUDAH ABSEN.',
            ], 400);
        }

        // Check if lesson has ended
        $currentTime = date('H:i');
        $jadwal = JadwalPelajaran::find($validated['jadwal_pelajaran_id']);
        if ($jadwal && $jadwal->jamakhir && $currentTime > $jadwal->jamakhir) {
            return response()->json(['success' => false, 'message' => 'Sesi pelajaran sudah berakhir.'], 400);
        }

        $activeTA = \App\Modules\Akademik\Models\TahunAjaran::where('status', 'aktif')->first();

        // Determine status: check if late (10 mins grace)
        $status = 'Hadir';
        $currentTime = date('H:i');
        $jadwal = JadwalPelajaran::find($validated['jadwal_pelajaran_id']);
        if ($jadwal && $jadwal->jamawal && $currentTime > date('H:i', strtotime($jadwal->jamawal . ' + 10 minutes'))) {
            $status = 'Telat';
        }

        $presensi = Presensi::create([
            'siswa_id' => $siswa->id,
            'guru_id' => $validated['guru_id'],
            'mapel_id' => $validated['mapel_id'],
            'jadwal_pelajaran_id' => $validated['jadwal_pelajaran_id'],
            'tahunajaran_id' => $activeTA->id ?? null,
            'semester' => $activeTA->semester ?? null,
            'author_id' => auth()->id(),
            'jam' => Carbon::now()->format('H:i'),
            'status' => $status,
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
                'guru_id' => 'required|exists:guru,id',
                'mapel_id' => 'required|exists:mata_pelajaran,id',
                'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
                'jam' => 'required|date_format:H:i',
                'kategori' => 'required|string|max:255',
                'bulk_penilaian.*.siswa_id' => 'required|exists:siswa,id',
                'bulk_penilaian.*.status' => 'required|string|max:255',
            ]);

            foreach ($request->bulk_penilaian as $item) {
                Presensi::updateOrCreate(
                    [
                        'siswa_id' => $item['siswa_id'],
                        'jadwal_pelajaran_id' => $validated['jadwal_pelajaran_id'],
                        'created_at' => date('Y-m-d') . ' ' . date('H:i:s'), // Simplified, usually use today
                    ],
                    [
                        'guru_id' => $validated['guru_id'],
                        'mapel_id' => $validated['mapel_id'],
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
            'siswa_id' => 'required|exists:siswa,id',
            'guru_id' => 'required|exists:guru,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
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
                'siswa_id' => $siswa->id,
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

        $mapel = MataPelajaran::find($presensi->mapel_id);
        $jadwal = JadwalPelajaran::find($presensi->jadwal_pelajaran_id);

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
            'siswa_id' => 'required|exists:siswa,id',
            'guru_id' => 'required|exists:guru,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
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
