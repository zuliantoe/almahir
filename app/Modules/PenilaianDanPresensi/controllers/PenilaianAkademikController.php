<?php

namespace Modules\PenilaianDanPresensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PenilaianDanPresensi\Models\PenilaianAkademik;
use Modules\Siswa\Models\Siswa as ModelsSiswa;
use Modules\Guru\Models\Guru as ModelsGuru;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\Kurikulum;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\TahunAjaran;
use Modules\PenilaianDanPresensi\Models\Presensi;
use Modules\PenilaianDanPresensi\Models\PenilaianTahfidz;
use Modules\PenilaianDanPresensi\Models\RaportCatatan;

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
            $query->where('siswa_id', $siswaId);
        }

        // Filter for Guru: they see students in their class OR what they input themselves
        if (auth()->user()->ref_type === ModelsGuru::class) {
            $guruId = auth()->user()->ref_id;
            
            $query->where(function($q) use ($guruId) {
                // 1. Scores they input themselves
                $q->where('guru_id', $guruId);
                
                // 2. Scores for students in their class (Wali Kelas)
                $q->orWhereHas('siswa.rombelSiswa', function($sq) use ($guruId) {
                    $sq->where('status', 'aktif')
                       ->whereHas('rombel', function($rq) use ($guruId) {
                           $rq->where('guru_id', $guruId);
                       });
                });
            });
        }

        // Apply dynamic filters
        if ($request->filled('rombel_id')) {
            $rombelId = $request->rombel_id;
            $query->whereHas('siswa.rombelSiswa', function($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status', 'aktif');
            });
        }

        if ($request->filled('mapel_id')) {
            $mapel = MataPelajaran::find($request->mapel_id);
            if ($mapel) {
                // Case-insensitive name matching to handle duplicates/inconsistencies
                $mapelIds = MataPelajaran::where('nama', 'like', $mapel->nama)->pluck('id');
                $query->whereIn('mapel_id', $mapelIds);
            }
        }

        if ($request->filled('jenis_nilai')) {
            $query->where('jenis_nilai', 'like', $request->jenis_nilai);
        }

        if ($request->filled('tahunajaran_id')) {
            $query->where('tahunajaran_id', $request->tahunajaran_id);
        }

        $penilaianAkademiks = $query->latest()->paginate(10);
        $allMapels = MataPelajaran::with('kategori')->orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();
        $rombels = Rombel::where('tahunajaran_id', $request->tahunajaran_id ?? (TahunAjaran::where('status', 'aktif')->first()->id ?? 0))
            ->orderBy('nama_rombel')->get();

        return view('penilaiandanpresensi::penilaianakademik.index', [
            'title' => 'Daftar Penilaian Akademik',
            'penilaianAkademiks' => $penilaianAkademiks,
            'allMapels' => $allMapels,
            'tahunAjarans' => $tahunAjarans,
            'rombels' => $rombels,
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

        $user = auth()->user();
        $isGuru = $user->ref_type === ModelsGuru::class;
        $loggedGuruId = $isGuru ? $user->ref_id : null;

        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if (!$activeTahunAjaran) {
            $activeTahunAjaran = TahunAjaran::orderBy('tahunajaran', 'desc')->first();
        }

        // Get Rombels (Teaching or Wali Kelas)
        $rombels = collect();
        $mapels = collect();

        $rombels = collect();
        if ($activeTahunAjaran) {
            $rombels = Rombel::where('tahunajaran_id', $activeTahunAjaran->id)
                ->orderBy('nama_rombel')
                ->get();
        }

        // Subject selection
        if ($isGuru) {
            $myMapelIds = JadwalPelajaran::where('guru_id', $loggedGuruId)
                ->pluck('mapel_id')->unique();
            $mapels = MataPelajaran::whereIn('id', $myMapelIds)->with('kategori')->orderBy('nama')->get();
        } else {
            $mapels = MataPelajaran::with('kategori')->orderBy('nama')->get();
        }

        $gurus = ModelsGuru::orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();

        return view('penilaiandanpresensi::penilaianakademik.create', [
            'title' => 'Tambah Penilaian Akademik',
            'rombels' => $rombels,
            'gurus' => $gurus,
            'mapels' => $mapels,
            'tahunAjarans' => $tahunAjarans,
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
            'rombel_id' => 'required|exists:rombel,id',
            'guru_id' => 'required|exists:guru,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'tahunajaran_id' => 'required|exists:tahun_ajaran,id',
            'jenis_nilai' => 'required|string|in:Harian,UTS,UAS',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        $request->validate([
            'penilaian' => 'required|array|min:1',
            'penilaian.*.siswa_id' => 'required|exists:siswa,id',
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
                    'siswa_id' => $item['siswa_id'],
                    'guru_id' => $request->guru_id,
                    'mapel_id' => $request->mapel_id,
                    'tahunajaran_id' => $request->tahunajaran_id,
                    'jenis_nilai' => $request->jenis_nilai,
                    'semester' => TahunAjaran::find($request->tahunajaran_id)->semester ?? null,
                    'author_id' => auth()->id(),
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
        $allMapels = MataPelajaran::with('kategori')->orderBy('nama')->get()
            ->map(function($m) {
                $m->nama = trim($m->nama);
                if (str_contains(strtolower($m->nama), 'tahfidz')) $m->nama = 'Tahfidz Al-Qur\'an';
                if (strtolower($m->nama) == 'fiqih') $m->nama = 'Fiqih';
                if (str_contains(strtolower($m->nama), 'ipa')) $m->nama = 'IPA (Ilmu Pengetahuan Alam)';
                return $m;
            })->unique('nama')->values();
        
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
            'siswa_id' => 'required|exists:siswa,id',
            'guru_id' => 'required|exists:guru,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'tahunajaran_id' => 'required|exists:tahun_ajaran,id',
            'jenis_nilai' => 'required|string|in:Harian,UTS,UAS',
            'nilai' => 'required|integer|min:0|max:100',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        $penilaianAkademik = PenilaianAkademik::findOrFail($id);
        $penilaianAkademik->update([
            'siswa_id' => $validated['siswa_id'],
            'guru_id' => $validated['guru_id'],
            'mapel_id' => $validated['mapel_id'],
            'tahunajaran_id' => $validated['tahunajaran_id'],
            'jenis_nilai' => $validated['jenis_nilai'],
            'semester' => TahunAjaran::find($validated['tahunajaran_id'])->semester ?? null,
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

    /**
     * Export data to Excel (HTML format for better styling/column support).
     */
    public function exportExcel(Request $request)
    {
        $query = PenilaianAkademik::with(['siswa', 'guru', 'mataPelajaran', 'tahunAjaran']);
        
        // Filter for Guru: they only export their own class students' scores (if they are Wali Kelas)
        if (auth()->user()->ref_type === ModelsGuru::class) {
            $guruId = auth()->user()->ref_id;
            $mySiswaIds = ModelsSiswa::whereHas('kelas.rombel', function($q) use ($guruId) {
                $q->where('wali_kelas_id', $guruId);
            })->pluck('id');
            
            if ($mySiswaIds->isNotEmpty()) {
                $query->whereIn('siswa_id', $mySiswaIds);
            } else {
                $query->where('guru_id', $guruId);
            }
        }

        if ($request->filled('mapel_id')) $query->where('mapel_id', $request->mapel_id);
        if ($request->filled('jenis_nilai')) $query->where('jenis_nilai', $request->jenis_nilai);
        if ($request->filled('tahunajaran_id')) $query->where('tahunajaran_id', $request->tahunajaran_id);

        $data = $query->get();
        $filename = "Rekap_Nilai_AlMahir_" . date('Ymd_His') . ".xls";
        
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="Content-type" content="text/html;charset=utf-8" /></head>';
        echo '<body>';
        echo '<table border="1">';
        echo '<tr>';
        echo '<th style="background-color: #f2f2f2;">NO</th>';
        echo '<th style="background-color: #f2f2f2;">NAMA SANTRI</th>';
        echo '<th style="background-color: #f2f2f2;">NIS</th>';
        echo '<th style="background-color: #f2f2f2;">MATA PELAJARAN</th>';
        echo '<th style="background-color: #f2f2f2;">KATEGORI</th>';
        echo '<th style="background-color: #f2f2f2;">NILAI</th>';
        echo '<th style="background-color: #f2f2f2;">KKM</th>';
        echo '<th style="background-color: #f2f2f2;">STATUS</th>';
        echo '<th style="background-color: #f2f2f2;">SEMESTER</th>';
        echo '<th style="background-color: #f2f2f2;">TAHUN AJARAN</th>';
        echo '</tr>';
        
        foreach ($data as $idx => $item) {
            $status = $item->nilai >= $item->kkm ? 'LULUS' : 'REMIDIAL';
            $statusColor = $item->nilai >= $item->kkm ? '#d4edda' : '#f8d7da';
            
            echo '<tr>';
            echo '<td>' . ($idx + 1) . '</td>';
            echo '<td>' . strtoupper($item->siswa->nama ?? '-') . '</td>';
            echo '<td>' . ($item->siswa->nis ?? '-') . '</td>';
            echo '<td>' . ($item->mataPelajaran->nama ?? '-') . '</td>';
            echo '<td>' . strtoupper($item->jenis_nilai ?? 'Harian') . '</td>';
            echo '<td>' . $item->nilai . '</td>';
            echo '<td>' . $item->kkm . '</td>';
            echo '<td style="background-color: ' . $statusColor . ';">' . $status . '</td>';
            echo '<td>' . ($item->semester ?? '-') . '</td>';
            echo '<td>' . ($item->tahunAjaran->tahunajaran ?? '-') . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }

    /**
     * Display list of students for Raport generation.
     */
    public function raportIndex(Request $request): View
    {
        $activeTA = TahunAjaran::where('status', 'aktif')->first() ?: TahunAjaran::orderBy('tahunajaran', 'desc')->first();
        
        $user = auth()->user();
        $isAdmin = $user->hasRole('SUPER_ADMIN');
        $isGuru = $user->ref_type && str_contains($user->ref_type, 'Guru');
        $loggedGuruId = $user->ref_id;

        // Determine which rombels this user can see for the dropdown
        // Show ALL rombels for the year (Like in Input Nilai)
        $rombels = \App\Modules\Akademik\Models\Rombel::where('tahunajaran_id', $activeTA->id ?? 0)
            ->orderBy('nama_rombel')
            ->get();

        $query = ModelsSiswa::query();
        
        // Filter based on user role and selection
        if ($request->filled('rombel_id')) {
            $rombelId = $request->rombel_id;
            $query->whereHas('rombelSiswa', function($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status', 'aktif');
            });
        } elseif (!$isAdmin && $isGuru) {
            // If Guru and no specific rombel selected, show students from ALL rombels they manage
            $myRombelIds = $rombels->pluck('id');
            $query->whereHas('rombelSiswa', function($q) use ($myRombelIds) {
                $q->whereIn('rombel_id', $myRombelIds)->where('status', 'aktif');
            });
        } else {
            // Default: show students in ANY active rombel for the TA
            $query->whereHas('rombelSiswa', function($q) use ($activeTA) {
                $q->where('status', 'aktif')->whereHas('rombel', function($rq) use ($activeTA) {
                    $rq->where('tahunajaran_id', $activeTA->id ?? 0);
                });
            });
        }

        $siswas = $query->paginate(15);
        
        // Get existing notes for these students in the active TA
        $notes = RaportCatatan::whereIn('siswa_id', $siswas->pluck('id'))
            ->where('tahunajaran_id', $activeTA->id ?? 0)
            ->get()
            ->keyBy('siswa_id');

        return view('penilaiandanpresensi::penilaianakademik.raport_index', [
            'title' => 'Cetak Raport',
            'rombels' => $rombels,
            'siswas' => $siswas,
            'activeTA' => $activeTA,
            'notes' => $notes,
        ]);
    }

    /**
     * Save Raport Note for a student
     */
    public function saveCatatan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'catatan' => 'nullable|string',
            'catatan_tahfidz' => 'nullable|string',
        ]);

        $activeTA = TahunAjaran::where('status', 'aktif')->first();
        if (!$activeTA) {
            return response()->json(['success' => false, 'message' => 'Tahun ajaran aktif tidak ditemukan.'], 400);
        }

        RaportCatatan::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'tahunajaran_id' => $activeTA->id,
            ],
            [
                'catatan' => $validated['catatan'],
                'catatan_tahfidz' => $validated['catatan_tahfidz'],
                'semester' => $activeTA->semester,
                'author_id' => auth()->id(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Catatan berhasil disimpan.']);
    }

    /**
     * Display a specific student's Raport.
     */
    public function raportShow(string $id): View
    {
        $siswa = ModelsSiswa::with(['kelas'])->findOrFail($id);
        $activeTA = TahunAjaran::where('status', 'aktif')->first();
        
        // Determine the student's Rombel for the active academic year
        $activeRombel = \App\Modules\Akademik\Models\RombelSiswa::where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->whereHas('rombel', function($q) use ($activeTA) {
                $q->where('tahunajaran_id', $activeTA->id ?? 0);
            })->first();
        $rombelId = $activeRombel ? $activeRombel->rombel_id : 0;

        // Get all assessments for this student in the active TA
        $scores = PenilaianAkademik::with(['mataPelajaran.kategori'])
            ->where('siswa_id', $siswa->id)
            ->where('tahunajaran_id', $activeTA->id ?? 0)
            ->get();

        // Get class-wide assessments (same Rombel) to calculate Class Average (Rerata Kelas)
        $classScores = PenilaianAkademik::whereHas('siswa.rombelSiswa', function($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status', 'aktif');
            })
            ->where('tahunajaran_id', $activeTA->id ?? 0)
            ->get()
            ->groupBy('mapel_id');

        // Group by Mapel and Jenis Nilai
        $rekap = [];
        foreach ($scores as $score) {
            $mapelId = $score->mapel_id;
            if (!isset($rekap[$mapelId])) {
                $rekap[$mapelId] = [
                    'nama' => $score->mataPelajaran->nama,
                    'kategori' => $score->mataPelajaran->kategori->kategori ?? 'Umum',
                    'kkm' => $score->kkm,
                    'harian' => [],
                    'uts' => null,
                    'uas' => null,
                    'rerata_kelas' => 0,
                ];
            }

            if ($score->jenis_nilai == 'Harian') {
                $rekap[$mapelId]['harian'][] = $score->nilai;
            } elseif ($score->jenis_nilai == 'UTS') {
                $rekap[$mapelId]['uts'] = $score->nilai;
            } elseif ($score->jenis_nilai == 'UAS') {
                $rekap[$mapelId]['uas'] = $score->nilai;
            }
        }

        // Calculate Averages and Final Grades
        foreach ($rekap as $mid => &$item) {
            $avgHarian = count($item['harian']) > 0 ? array_sum($item['harian']) / count($item['harian']) : 0;
            $item['avg_harian'] = round($avgHarian, 2);
            
            // Formula: (AvgHarian + UTS + UAS) / 3
            $divider = 0;
            $total = 0;
            if ($item['avg_harian'] > 0) { $total += $item['avg_harian']; $divider++; }
            if ($item['uts'] !== null) { $total += $item['uts']; $divider++; }
            if ($item['uas'] !== null) { $total += $item['uas']; $divider++; }
            
            $item['final'] = $divider > 0 ? round($total / $divider, 2) : 0;
            $item['predikat'] = $this->getPredikat($item['final']);

            // Calculate Class Average for this subject
            if (isset($classScores[$mid])) {
                $subjectClassScores = $classScores[$mid];
                $totalClassNilai = 0;
                $studentCount = 0;
                
                // Group class scores by student to get their final grade first
                $studentFinals = [];
                foreach ($subjectClassScores as $cs) {
                    if (!isset($studentFinals[$cs->siswa_id])) {
                        $studentFinals[$cs->siswa_id] = ['harian' => [], 'uts' => null, 'uas' => null];
                    }
                    if ($cs->jenis_nilai == 'Harian') $studentFinals[$cs->siswa_id]['harian'][] = $cs->nilai;
                    elseif ($cs->jenis_nilai == 'UTS') $studentFinals[$cs->siswa_id]['uts'] = $cs->nilai;
                    elseif ($cs->jenis_nilai == 'UAS') $studentFinals[$cs->siswa_id]['uas'] = $cs->nilai;
                }

                foreach ($studentFinals as $sf) {
                    $sAvgHarian = count($sf['harian']) > 0 ? array_sum($sf['harian']) / count($sf['harian']) : 0;
                    $sDivider = 0; $sTotal = 0;
                    if ($sAvgHarian > 0) { $sTotal += $sAvgHarian; $sDivider++; }
                    if ($sf['uts'] !== null) { $sTotal += $sf['uts']; $sDivider++; }
                    if ($sf['uas'] !== null) { $sTotal += $sf['uas']; $sDivider++; }
                    
                    if ($sDivider > 0) {
                        $totalClassNilai += ($sTotal / $sDivider);
                        $studentCount++;
                    }
                }
                $item['rerata_kelas'] = $studentCount > 0 ? round($totalClassNilai / $studentCount, 2) : 0;
            }
        }

        // Group by Kategori for the view
        $rekapGrouped = [];
        foreach ($rekap as $item) {
            $cat = $item['kategori'];
            if (!isset($rekapGrouped[$cat])) {
                $rekapGrouped[$cat] = [];
            }
            $rekapGrouped[$cat][] = $item;
        }

        // Get Attendance Summary
        $attendance = [
            'Sakit' => Presensi::where('siswa_id', $siswa->id)->where('tahunajaran_id', $activeTA->id ?? 0)->where('status', 'Sakit')->count(),
            'Izin' => Presensi::where('siswa_id', $siswa->id)->where('tahunajaran_id', $activeTA->id ?? 0)->where('status', 'Izin')->count(),
            'Alpha' => Presensi::where('siswa_id', $siswa->id)->where('tahunajaran_id', $activeTA->id ?? 0)->where('status', 'Alpha')->count(),
        ];

        // Get Tahfidz Data
        $tahfidz = PenilaianTahfidz::where('siswa_id', $siswa->id)
            ->where('tahunajaran_id', $activeTA->id ?? 0)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Get Raport Note
        $catatan = RaportCatatan::where('siswa_id', $siswa->id)
            ->where('tahunajaran_id', $activeTA->id ?? 0)
            ->first();

        return view('penilaiandanpresensi::penilaianakademik.raport_show', [
            'title' => 'Raport Santri: ' . $siswa->nama,
            'siswa' => $siswa,
            'activeTA' => $activeTA,
            'rekapGrouped' => $rekapGrouped,
            'attendance' => $attendance,
            'tahfidz' => $tahfidz,
            'catatan' => $catatan,
        ]);
    }

    private function getPredikat($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        return 'D';
    }

    /**
     * Get history of scores for a student and subject (AJAX).
     */
    public function history(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'siswa_id' => 'required',
            'mapel_id' => 'required',
        ]);

        $history = PenilaianAkademik::where('siswa_id', $request->siswa_id)
            ->where('mapel_id', $request->mapel_id)
            ->with(['tahunAjaran', 'guru'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json($history);
    }

    /**
     * Get students by Rombel (AJAX)
     */
    public function getSiswaByRombel($rombelId): JsonResponse
    {
        $rombel = Rombel::with('aktifSiswa')->findOrFail($rombelId);
        return response()->json($rombel->aktifSiswa);
    }

    /**
     * Get KKM for a subject in a specific Rombel (AJAX)
     */
    public function getKkm($rombelId, $mapelId): JsonResponse
    {
        $rombel = Rombel::findOrFail($rombelId);
        $kkm = Kurikulum::where('mapel_id', $mapelId)
            ->where('tahunajaran_id', $rombel->tahunajaran_id)
            ->where('kelas_id', $rombel->kelas_id)
            ->value('kkm') ?? 75;
            
        return response()->json(['kkm' => $kkm]);
    }

    /**
     * Get Mapels and Rombels for a specific Guru (AJAX).
     */
    public function getDataByGuru(Request $request, string $guruId): JsonResponse
    {
        $taId = $request->tahunajaran_id;
        
        if (!$taId) {
            $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
            if (!$activeTahunAjaran) {
                $activeTahunAjaran = TahunAjaran::orderBy('tahunajaran', 'desc')->first();
            }
            $taId = $activeTahunAjaran->id ?? 0;
        }

        // 2. Get ALL Rombels for the selected Year (Like in Index)
        $rombels = Rombel::where('tahunajaran_id', $taId)
            ->orderBy('nama_rombel')
            ->get();

        // 3. Get Mapels based on teacher's schedule in those Rombels
        $mapelIds = JadwalPelajaran::where('guru_id', $guruId)
            ->whereIn('rombel_id', $rombels->pluck('id'))
            ->pluck('mapel_id')->unique();
            
        $mapels = MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama')->get();

        return response()->json([
            'mapels' => $mapels,
            'rombels' => $rombels
        ]);
    }
}

