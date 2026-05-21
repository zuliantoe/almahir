<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;
use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;
use Modules\Siswa\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class JadwalPiketController extends BaseController
{
    /**
     * Display a listing of jadwal piket.
     * Logika: Paginate per Tanggal Unik, Default ke Hari Ini.
     */
    public function index(Request $request): View
    {
        // 1. Ambil list tanggal unik yang punya jadwal
        $dateQuery = JadwalPiket::select('tanggal')->distinct();

        if ($request->filled('tanggal_mulai')) {
            $dateQuery->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $dateQuery->where('tanggal', '<=', $request->tanggal_selesai);
        }
        if ($request->filled('lokasi_piket')) {
            $dateQuery->where('lokasi_piket', $request->lokasi_piket);
        }
        if ($request->filled('q')) {
            $dateQuery->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%');
            });
        }

        // Ambil semua tanggal unik, urutkan terbaru ke lama
        $allDates = $dateQuery->orderBy('tanggal', 'desc')->pluck('tanggal')->toArray();

        // --- LOGIKA SMART DEFAULT HARI INI ---
        $today = now()->format('Y-m-d');
        $allDatesFormatted = array_map(function ($d) {
            return \Carbon\Carbon::parse($d)->format('Y-m-d');
        }, $allDates);

        $todayIndex = array_search($today, $allDatesFormatted);

        $perPage = 1;
        $currentPage = $request->input('page');

        // Jika tidak ada parameter 'page', coba arahkan ke index hari ini
        if (!$currentPage && $todayIndex !== false) {
            $currentPage = $todayIndex + 1;
        } else {
            $currentPage = $currentPage ?: 1;
        }

        $currentDateSlice = array_slice($allDates, ($currentPage - 1) * $perPage, $perPage);

        $paginatedDates = new LengthAwarePaginator(
            $currentDateSlice,
            count($allDates),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $activeDate = count($currentDateSlice) > 0 ? $currentDateSlice[0] : null;

        $jadwalQuery = JadwalPiket::with(['siswa', 'kamar']);
        if ($activeDate) {
            $jadwalQuery->where('tanggal', $activeDate);
        } else {
            $jadwalQuery->where('id', 0);
        }

        if ($request->filled('q')) {
            $jadwalQuery->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('lokasi_piket')) {
            $jadwalQuery->where('lokasi_piket', $request->lokasi_piket);
        }

        $jadwalData = $jadwalQuery->orderBy('shift', 'asc')
            ->orderBy('lokasi_piket', 'asc')
            ->get();

        $stats = [
            'hari_ini' => JadwalPiket::whereDate('tanggal', now())->count(),
            'selesai_hari_ini' => JadwalPiket::whereDate('tanggal', now())->where('status', 'sudah')->count(),
            'belum_hari_ini' => JadwalPiket::whereDate('tanggal', now())->where('status', 'belum')->count(),
        ];

        $allSiswa = Siswa::orderBy('nama', 'asc')->get();
        $kamar = Kamar::all();
        $locations = JadwalPiket::whereNotNull('lokasi_piket')->distinct()->orderBy('lokasi_piket', 'asc')->pluck('lokasi_piket');

        return view('manajemenasetdanasrama::jadwal-piket.index', [
            'title' => 'Jadwal Piket Asrama',
            'jadwal' => $jadwalData,
            'paginator' => $paginatedDates,
            'activeDate' => $activeDate,
            'kamar' => $kamar,
            'locations' => $locations,
            'totalSantri' => count($allSiswa),
            'allSiswa' => $allSiswa,
            'stats' => $stats,
        ]);
    }

    /**
     * Delete all schedules for a specific date.
     */
    public function destroyDay(string $date): RedirectResponse
    {
        JadwalPiket::whereDate('tanggal', $date)->delete();

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', "Seluruh jadwal piket pada tanggal " . \Carbon\Carbon::parse($date)->translatedFormat('d F Y') . " berhasil dihapus.");
    }

    /**
     * Store bulk manual jadwal piket.
     */
    public function bulkStore(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'shift' => 'required',
            'lokasi' => 'required|array',
            'siswa_id' => 'required|array',
        ]);

        $count = 0;
        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($request->tanggal_selesai);

        for ($date = $tanggalMulai->copy(); $date->lte($tanggalSelesai); $date->addDay()) {
            foreach ($request->siswa_id as $index => $siswaId) {
                if (!$siswaId)
                    continue;
                $lokasiIdx = $request->lokasi_mapping[$index];

                // Seragamkan format nama lokasi jadi Huruf Depan Besar biar menyatu sempurna
                $namaLokasi = ucwords(strtolower(trim($request->lokasi[$lokasiIdx])));

                // Cari kamar aktif santri ini saat ini
                $kamarId = \App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni::where('siswa_id', $siswaId)
                    ->aktif()
                    ->value('kamar_id');

                JadwalPiket::updateOrCreate(
                    ['tanggal' => $date->format('Y-m-d'), 'shift' => $request->shift, 'siswa_id' => $siswaId],
                    [
                        'lokasi_piket' => $namaLokasi,
                        'kamar_id' => $kamarId,
                        'status' => 'belum'
                    ]
                );
                $count++;
            }
        }

        // Arahkan langsung ke filter tanggal mulai agar user langsung melihat hasilnya
        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index', [
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d')
        ])->with('success', "Berhasil menambahkan/memperbarui {$count} jadwal piket manual.");
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->getValidationRules($request));
        $validated['status'] = 'belum';
        JadwalPiket::create($validated);
        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')->with('success', 'Jadwal piket berhasil ditambahkan.');
    }

    public function edit(string $id): View
    {
        $jadwal = JadwalPiket::with(['siswa', 'kamar'])->findOrFail($id);

        // Ambil kandidat penugasan piket lain di tanggal dan shift yang identik untuk disilang
        $candidates = JadwalPiket::with(['siswa', 'kamar'])
            ->where('tanggal', $jadwal->tanggal)
            ->where('shift', $jadwal->shift)
            ->where('id', '!=', $id)
            ->get();

        return view('manajemenasetdanasrama::jadwal-piket.edit', [
            'title' => 'Tukar Lokasi Penugasan Piket (Switch)',
            'jadwal' => $jadwal,
            'candidates' => $candidates,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);

        $request->validate([
            'target_jadwal_id' => 'required|exists:jadwal_piket,id',
        ]);

        $targetJadwal = JadwalPiket::findOrFail($request->target_jadwal_id);

        // Pastikan target berada di putaran tanggal dan shift yang sama
        if ($targetJadwal->tanggal != $jadwal->tanggal || $targetJadwal->shift != $jadwal->shift) {
            return redirect()->back()->with('error', 'Target penugasan piket tidak valid untuk disilang.');
        }

        // SWAP MURNI LOKASI PIKET & ASOSIASI KAMAR
        // Baris data masing-masing anak tetap utuh, sehingga tidak memicu Unique Constraint.
        $oldLokasi = $jadwal->lokasi_piket;
        $oldKamarId = $jadwal->kamar_id;

        $jadwal->update([
            'lokasi_piket' => $targetJadwal->lokasi_piket,
            'kamar_id' => $targetJadwal->kamar_id,
        ]);

        $targetJadwal->update([
            'lokasi_piket' => $oldLokasi,
            'kamar_id' => $oldKamarId,
        ]);

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index', [
            'tanggal_mulai' => \Carbon\Carbon::parse($jadwal->tanggal)->format('Y-m-d')
        ])->with('success', "Lokasi tugas piket berhasil disilang antara {$jadwal->siswa->nama} dan {$targetJadwal->siswa->nama}
        .");
    }

    private function getValidationRules(Request $request, ?string $id = null): array
    {
        return ['tanggal' => 'required|date', 'siswa_id' => ['required', 'exists:siswa,id']];
    }

    public function destroy(string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->delete();
        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')->with('success', 'Jadwal piket berhasil dihapus.');
    }

    public function selesai(string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->status = 'sudah';
        $jadwal->save();
        return redirect()->back()->with('success', 'Status piket diupdate menjadi selesai.');
    }

    public function batalSelesai(string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->status = 'belum';
        $jadwal->save();
        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index', [
            'tanggal_mulai' => \Carbon\Carbon::parse($jadwal->tanggal)->format('Y-m-d')
        ])->with('success', 'Status piket berhasil dibatalkan menjadi belum.');
    }

    /**
     * Konfirmasi selesai seluruh santri pada satu lokasi piket di tanggal tertentu.
     */
    public function selesaiTempat(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'lokasi_piket' => 'nullable|string',
        ]);

        $query = JadwalPiket::whereDate('tanggal', $request->tanggal);
        if ($request->filled('lokasi_piket')) {
            $query->where('lokasi_piket', $request->lokasi_piket);
        } else {
            $query->where(function($q) {
                $q->whereNull('lokasi_piket')->orWhere('lokasi_piket', '');
            });
        }

        $count = $query->update(['status' => 'sudah']);

        $lokasiName = $request->lokasi_piket ?: 'Umum';
        return redirect()->back()->with('success', "Berhasil mengonfirmasi semua ({$count}) santri di lokasi {$lokasiName} pada tanggal " . \Carbon\Carbon::parse($request->tanggal)->translatedFormat('d F Y') . " selesai piket.");
    }

    public function autoGenerate(Request $request)
    {
        $request->validate(['tanggal_mulai' => 'required|date', 'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai', 'shift' => 'required|in:pagi,sore,malam', 'lokasi' => 'required|array', 'jumlah_santri' => 'required|array']);
        try {
            $locations = [];
            foreach ($request->lokasi as $index => $nama) {
                $locations[] = ['nama' => $nama, 'kuota' => $request->jumlah_santri[$index] ?? 0];
            }
            $service = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
            $generated = $service->generateSmart($request->tanggal_mulai, $request->tanggal_selesai, $request->shift, $locations);
            if ($generated === 0) {
                return redirect()->back()->with('warning', 'Tidak ada jadwal yang di-generate.');
            }
            return redirect()->back()->with('success', "Berhasil me-generate {$generated} jadwal piket.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function print(Request $request)
    {
        $query = JadwalPiket::with(['kamar', 'siswa']);
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }
        if ($request->filled('lokasi_piket')) {
            $query->where('lokasi_piket', $request->lokasi_piket);
        }
        if ($request->filled('q')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%'); });
        }
        $jadwal = $query->orderBy('tanggal', 'asc')->get();
        return view('manajemenasetdanasrama::jadwal-piket.print', ['title' => 'Cetak Jadwal Piket', 'jadwal' => $jadwal, 'request' => $request]);
    }

    public function resetAll(): RedirectResponse
    {
        JadwalPiket::truncate();
        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')->with('success', 'Semua jadwal piket berhasil di-reset.');
    }

    /**
     * Tampilan evaluasi dan rekapitulasi piket bulanan/kerajinan santri.
     */
    public function evaluasi(Request $request): View
    {
        $activeTab = $request->input('tab', 'harian');

        // --- TAB 1: EVALUASI HARIAN (PAGINATION TANGGAL) ---
        // Ambil list tanggal unik yang punya jadwal (sampai hari ini)
        $dateQuery = JadwalPiket::select('tanggal')->distinct()->where('tanggal', '<=', now()->format('Y-m-d'));
        
        $allDates = $dateQuery->orderBy('tanggal', 'desc')->pluck('tanggal')->toArray();
        $today = now()->format('Y-m-d');
        $allDatesFormatted = array_map(function ($d) {
            return \Carbon\Carbon::parse($d)->format('Y-m-d');
        }, $allDates);

        $todayIndex = array_search($today, $allDatesFormatted);

        $perPage = 1;
        $currentPage = $request->input('page_date');

        // Default ke halaman hari ini jika ada
        if (!$currentPage && $todayIndex !== false) {
            $currentPage = $todayIndex + 1;
        } else {
            $currentPage = $currentPage ?: 1;
        }

        $currentDateSlice = array_slice($allDates, ($currentPage - 1) * $perPage, $perPage);
        $paginatedDates = new LengthAwarePaginator(
            $currentDateSlice,
            count($allDates),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page_date',
                'query' => array_merge($request->query(), ['tab' => 'harian'])
            ]
        );

        $activeDate = count($currentDateSlice) > 0 ? $currentDateSlice[0] : null;

        $harianJadwal = [];
        $statsHarian = [
            'total' => 0,
            'selesai' => 0,
            'belum' => 0,
            'rate' => 100
        ];

        if ($activeDate) {
            $harianJadwal = JadwalPiket::with(['siswa', 'kamar'])
                ->where('tanggal', $activeDate)
                ->orderBy('shift', 'asc')
                ->orderBy('lokasi_piket', 'asc')
                ->get();

            $totalActive = $harianJadwal->count();
            $completedActive = $harianJadwal->where('status', 'sudah')->count();
            
            $statsHarian = [
                'total' => $totalActive,
                'selesai' => $completedActive,
                'belum' => $totalActive - $completedActive,
                'rate' => $totalActive > 0 ? round(($completedActive / $totalActive) * 100, 1) : 100
            ];
        }

        // --- TAB 2: REKAP KERAJINAN PER SANTRI ---
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->input('tanggal_selesai', now()->format('Y-m-d'));

        $siswaQuery = Siswa::aktif()->orderBy('nama', 'asc');
        if ($request->filled('q')) {
            $siswaQuery->where('nama', 'like', '%' . $request->q . '%');
        }

        $siswaQuery->withCount([
            'jadwalPiket as total_piket' => function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
            },
            'jadwalPiket as total_selesai' => function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])->where('status', 'sudah');
            },
            'jadwalPiket as total_belum' => function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])->where('status', 'belum');
            }
        ]);

        $kerajinan = $request->input('kerajinan');
        if ($kerajinan === 'high') {
            $siswaQuery->havingRaw('total_piket > 0 AND (total_selesai / total_piket) >= 0.8');
        } elseif ($kerajinan === 'medium') {
            $siswaQuery->havingRaw('total_piket > 0 AND (total_selesai / total_piket) >= 0.5 AND (total_selesai / total_piket) < 0.8');
        } elseif ($kerajinan === 'low') {
            $siswaQuery->havingRaw('total_piket > 0 AND (total_selesai / total_piket) < 0.5');
        } elseif ($kerajinan === 'none') {
            $siswaQuery->havingRaw('total_piket = 0');
        }

        $complianceData = $siswaQuery->paginate(15, ['*'], 'page_compliance')->appends($request->query());

        // Hitung Ringkasan Metrik Global (Tab 2)
        $totalPastDuties = JadwalPiket::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->where('tanggal', '<=', now()->format('Y-m-d'))
            ->count();

        $totalPastCompleted = JadwalPiket::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->where('tanggal', '<=', now()->format('Y-m-d'))
            ->where('status', 'sudah')
            ->count();

        $globalComplianceRate = $totalPastDuties > 0 ? round(($totalPastCompleted / $totalPastDuties) * 100, 1) : 100;

        $statsRekap = [
            'total' => $totalPastDuties,
            'selesai' => $totalPastCompleted,
            'belum' => $totalPastDuties - $totalPastCompleted,
            'rate' => $globalComplianceRate
        ];

        return view('manajemenasetdanasrama::jadwal-piket.evaluasi', [
            'title' => 'Evaluasi & Rekap Kerajinan Piket',
            'activeTab' => $activeTab,
            'paginator' => $paginatedDates,
            'activeDate' => $activeDate,
            'harianJadwal' => $harianJadwal,
            'complianceData' => $complianceData,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'statsHarian' => $statsHarian,
            'statsRekap' => $statsRekap,
        ]);
    }
}