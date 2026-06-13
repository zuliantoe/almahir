<?php

namespace Modules\Akademik\Controllers;

use App\Http\Requests\AkademikRequest\StoreJadwalPelajaranRequest;
use App\Http\Requests\AkademikRequest\UpdateJadwalPelajaranRequest;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\RombelSiswa;
use Modules\Guru\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Modules\Akademik\Services\JadwalPelajaranService;
use App\Modules\Akademik\Models\TahunAjaran;

class JadwalPelajaranController extends Controller
{
    protected $jadwalService;

    public function __construct(JadwalPelajaranService $jadwalService)
    {
        $this->jadwalService = $jadwalService;
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        // Hari disimpan sebagai string di DB ('Senin','Selasa',dst)
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $isGuru = $user && method_exists($user, 'hasRole') ? $user->hasRole('GURU') : false;
        $isSiswa = $user && method_exists($user, 'hasRole') ? $user->hasRole('SISWA') : false;

        if ($isGuru) {
            if ($request->get('tampil') === 'all' || $request->hasAny(['rombel_id', 'hari', 'guru_id', 'mapel_id', 'tahun_ajaran_id'])) {
                return $this->renderListView($request);
            }
            return $this->renderGuruTimetable($user->ref, $hariList, $request);
        }

        if ($isSiswa) {
            return $this->renderSiswaTimetable($user->ref, $hariList, $request);
        }

        // Default for Admin / Staf
        return $this->renderListView($request);
    }

    private function renderListView(Request $request)
    {
        $activeTahunAjaran = TahunAjaran::aktif()->first();
        if (!$request->has('tahun_ajaran_id') && $activeTahunAjaran) {
            $request->merge(['tahun_ajaran_id' => $activeTahunAjaran->id]);
        }

        $jadwalPelajaran = JadwalPelajaran::query()
            ->with(['rombel.kelas', 'mataPelajaran', 'guru'])
            ->when($request->filled('rombel_id'), fn($q) => $q->where('rombel_id', $request->rombel_id))
            ->when($request->filled('hari'), fn($q) => $q->where('hari', $request->hari))
            ->when($request->filled('guru_id'), fn($q) => $q->where('guru_id', $request->guru_id))
            ->when($request->filled('mapel_id'), fn($q) => $q->where('mapel_id', $request->mapel_id))
            ->when($request->filled('tahun_ajaran_id'), function($q) use ($request) {
                return $q->whereHas('rombel', fn($sq) => $sq->where('tahunajaran_id', $request->tahun_ajaran_id));
            })
            ->orderBy('hari')
            ->orderBy('jamke')
            ->paginate(15)
            ->withQueryString();

        $rombels = Rombel::with('kelas')->get();
        $gurus   = Guru::aktif()->get();
        $mapels  = MataPelajaran::orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();

        $summaryJP = [];
        if ($request->filled('rombel_id')) {
            $rombel = Rombel::find($request->rombel_id);
            if ($rombel) {
                $mapelsInRombel = JadwalPelajaran::where('rombel_id', $rombel->id)
                    ->pluck('mapel_id')
                    ->unique();

                foreach ($mapelsInRombel as $mId) {
                    $summaryJP[$mId] = $this->jadwalService->hitungEstimasiTotalJP($mId, $rombel->id, $rombel->tahunajaran_id);
                }
            }
        }

        return view('akademik::jadwal-pelajaran.index', compact('jadwalPelajaran', 'rombels', 'gurus', 'mapels', 'tahunAjarans', 'summaryJP'));
    }

    private function renderGuruTimetable($guru, $hariList, Request $request)
    {
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();
        $activeTahunAjaran = $request->filled('tahun_ajaran_id')
            ? TahunAjaran::find($request->tahun_ajaran_id)
            : TahunAjaran::aktif()->first();

        $rawJadwal = JadwalPelajaran::with(['mataPelajaran', 'rombel.kelas'])
            ->where('guru_id', $guru?->id)
            ->when($activeTahunAjaran, function($q) use ($activeTahunAjaran) {
                return $q->whereHas('rombel', fn($sq) => $sq->where('tahunajaran_id', $activeTahunAjaran->id));
            })
            ->orderBy('hari')
            ->orderBy('jamke')
            ->get();

        [$timetable, $usedJamKes] = $this->buildTimetable($rawJadwal);

        return view('akademik::jadwal-pelajaran.timetable-guru', compact(
            'timetable', 'hariList', 'usedJamKes', 'rawJadwal', 'guru', 'tahunAjarans', 'activeTahunAjaran'
        ));
    }

    private function renderSiswaTimetable($siswa, $hariList, Request $request)
    {
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->get();
        $activeTahunAjaran = $request->filled('tahun_ajaran_id')
            ? TahunAjaran::find($request->tahun_ajaran_id)
            : TahunAjaran::aktif()->first();

        $rombelSiswa = RombelSiswa::with(['rombel.kelas'])
            ->where('siswa_id', $siswa?->id)
            ->when($activeTahunAjaran, function($q) use ($activeTahunAjaran) {
                return $q->whereHas('rombel', fn($sq) => $sq->where('tahunajaran_id', $activeTahunAjaran->id));
            })
            ->first();

        $rombelId = $rombelSiswa?->rombel_id;

        $rawJadwal = collect();
        if ($rombelId) {
            $rawJadwal = JadwalPelajaran::with(['mataPelajaran', 'guru'])
                ->where('rombel_id', $rombelId)
                ->orderBy('hari')
                ->orderBy('jamke')
                ->get();
        }

        [$timetable, $usedJamKes] = $this->buildTimetable($rawJadwal);

        return view('akademik::jadwal-pelajaran.timetable-siswa', compact(
            'timetable', 'hariList', 'usedJamKes', 'rawJadwal', 'rombelSiswa', 'tahunAjarans', 'activeTahunAjaran'
        ));
    }

    private function buildTimetable($rawJadwal)
    {
        $timetable = [];
        foreach ($rawJadwal as $j) {
            $timetable[$j->hari][$j->jamke] = $j;
        }
        $usedJamKes = $rawJadwal->pluck('jamke')->unique()->sort()->values()->toArray();

        return [$timetable, $usedJamKes];
    }


    public function create(Request $request)
    {
        $rombels  = Rombel::with('kelas')->get();
        $mapels   = MataPelajaran::orderBy('nama')->get();
        $gurus    = Guru::aktif()->get();
        // Hari disimpan sebagai string di DB (bukan integer)
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $masterJams = \App\Modules\Akademik\Models\MasterJamPelajaran::orderBy('jamke')->get();

        $duplicateData = null;
        if ($request->has('rombel_id') || $request->has('mapel_id') || $request->has('guru_id')) {
            $duplicateData = (object) [
                'rombel_id' => $request->get('rombel_id'),
                'mapel_id'  => $request->get('mapel_id'),
                'guru_id'   => $request->get('guru_id'),
                'hari'      => $request->get('hari'),
                'jamke'     => $request->get('jamke'),
                'jamawal'   => $request->get('jamawal'),
                'jamakhir'  => $request->get('jamakhir'),
                'master_jam_pelajaran_id' => $request->get('master_jam_pelajaran_id'),
            ];
        }

        return view('akademik::jadwal-pelajaran.create', compact('rombels', 'mapels', 'gurus', 'hariList', 'duplicateData', 'masterJams'));
    }

    public function store(StoreJadwalPelajaranRequest $request)
    {
        try {
            JadwalPelajaran::create($request->validated());

            return redirect()
                ->route('akademik.jadwal-pelajaran.index')
                ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function show(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->load(['rombel.kelas', 'mataPelajaran', 'guru']);
        return view('akademik::jadwal-pelajaran.show', compact('jadwalPelajaran'));
    }

    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $rombels  = Rombel::with('kelas')->get();
        $mapels   = MataPelajaran::orderBy('nama')->get();
        $gurus    = Guru::aktif()->get();
        // Hari disimpan sebagai string di DB (bukan integer)
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $masterJams = \App\Modules\Akademik\Models\MasterJamPelajaran::orderBy('jamke')->get();

        // master_jam_pelajaran_id belum tersimpan di jadwal_pelajaran,
        // jadi saat edit kita cocokkan berdasarkan jamke
        $selectedMasterJamId = $masterJams->firstWhere('jamke', $jadwalPelajaran->jamke)?->id;

        return view('akademik::jadwal-pelajaran.edit', compact(
            'jadwalPelajaran',
            'rombels',
            'mapels',
            'gurus',
            'hariList',
            'masterJams',
            'selectedMasterJamId'
        ));
    }

    public function update(UpdateJadwalPelajaranRequest $request, JadwalPelajaran $jadwalPelajaran)
    {
        try {
            $jadwalPelajaran->update($request->validated());

            return redirect()
                ->route('akademik.jadwal-pelajaran.index')
                ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        try {
            $jadwalPelajaran->delete();

            return redirect()
                ->route('akademik.jadwal-pelajaran.index')
                ->with('success', 'Jadwal pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('akademik.jadwal-pelajaran.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Copy Jadwal from one Rombel to another
     */
    public function copyJadwal(Request $request)
    {
        $request->validate([
            'from_rombel_id' => 'required|exists:rombel,id',
            'to_rombel_id' => 'required|exists:rombel,id',
        ]);

        $sourceJadwal = JadwalPelajaran::where('rombel_id', $request->from_rombel_id)->get();
        $targetRombel = \App\Modules\Akademik\Models\Rombel::find($request->to_rombel_id);
        $targetTahunAjaranId = $targetRombel ? $targetRombel->tahunajaran_id : null;

        $copied = 0;
        foreach ($sourceJadwal as $item) {
            // Check if target already has this schedule
            $existsRombel = JadwalPelajaran::where('rombel_id', $request->to_rombel_id)
                ->where('hari', $item->hari)
                ->where('jamke', $item->jamke)
                ->exists();

            // Check if guru already teaches in another class in the same tahun_ajaran (excluding source and target rombel)
            $existsGuru = JadwalPelajaran::where('guru_id', $item->guru_id)
                ->where('hari', $item->hari)
                ->where('jamke', $item->jamke)
                ->whereNotIn('rombel_id', [$request->from_rombel_id, $request->to_rombel_id])
                ->whereHas('rombel', function($q) use ($targetTahunAjaranId) {
                    $q->where('tahunajaran_id', $targetTahunAjaranId);
                })
                ->first();

            $bothDouble = false;
            if ($existsGuru) {
                $currentMapel = \App\Modules\Akademik\Models\MataPelajaran::find($item->mapel_id);
                $isDoubleMapel = $currentMapel && $currentMapel->bisa_double;
                $conflictingMapel = $existsGuru->mataPelajaran;
                $bothDouble = $isDoubleMapel && $conflictingMapel && $conflictingMapel->bisa_double;
            }

            if (!$existsRombel && (!$existsGuru || $bothDouble)) {
                $newItem = $item->replicate();
                $newItem->rombel_id = $request->to_rombel_id;
                $newItem->save();
                $copied++;
            }
        }

        return redirect()->back()->with('success', "Berhasil menyalin $copied jadwal pelajaran.");
    }

    /**
     * Bulk Store multiple schedules at once
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.rombel_id' => 'required|exists:rombel,id',
            'schedules.*.mapel_id' => 'required|exists:mata_pelajaran,id',
            'schedules.*.guru_id' => 'required|exists:guru,id',
            'schedules.*.hari' => 'required|string',
            'schedules.*.jamke' => 'required|integer',
            'schedules.*.jamawal' => 'required',
            'schedules.*.jamakhir' => 'required',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $created = 0;
            foreach ($request->schedules as $index => $data) {
                $rombel = \App\Modules\Akademik\Models\Rombel::find($data['rombel_id']);
                $tahunAjaranId = $rombel ? $rombel->tahunajaran_id : null;

                // Check Rombel Conflict
                $rombelConflict = JadwalPelajaran::where('hari', $data['hari'])
                    ->where('jamke', $data['jamke'])
                    ->where('rombel_id', $data['rombel_id'])
                    ->first();

                if ($rombelConflict) {
                    throw new \Exception("Baris ke-" . ($index + 1) . " bentrok: Rombel sudah memiliki mata pelajaran lain pada hari {$data['hari']} jam ke-{$data['jamke']}.");
                }

                // Check Guru Conflict
                $guruConflict = JadwalPelajaran::where('hari', $data['hari'])
                    ->where('jamke', $data['jamke'])
                    ->where('guru_id', $data['guru_id'])
                    ->whereHas('rombel', function($q) use ($tahunAjaranId) {
                        $q->where('tahunajaran_id', $tahunAjaranId);
                    })
                    ->first();

                if ($guruConflict) {
                    $currentMapel = \App\Modules\Akademik\Models\MataPelajaran::find($data['mapel_id']);
                    $isDoubleMapel = $currentMapel && $currentMapel->bisa_double;
                    $conflictingMapel = $guruConflict->mataPelajaran;
                    $bothDouble = $isDoubleMapel && $conflictingMapel && $conflictingMapel->bisa_double;

                    if (!$bothDouble) {
                        throw new \Exception("Baris ke-" . ($index + 1) . " bentrok: Guru sudah mengajar di kelas lain pada hari {$data['hari']} jam ke-{$data['jamke']}.");
                    }
                }

                JadwalPelajaran::create($data);
                $created++;
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('akademik.jadwal-pelajaran.create')
                ->with('success', "Berhasil menyimpan $created jadwal! Silakan tambahkan jadwal lainnya.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
