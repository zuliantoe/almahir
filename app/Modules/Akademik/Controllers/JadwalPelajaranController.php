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
        $user = auth()->user();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        if ($user && $user->hasRole('GURU')) {
            if ($request->get('tampil') === 'all' || $request->hasAny(['rombel_id', 'hari', 'guru_id', 'mapel_id', 'tahun_ajaran_id'])) {
                return $this->renderListView($request);
            }
            return $this->renderGuruTimetable($user->ref, $hariList, $request);
        }

        if ($user && $user->hasRole('SISWA')) {
            return $this->renderSiswaTimetable($user->ref, $hariList, $request);
        }

        // Default for Admin / Staf
        return $this->renderListView($request);
    }

    private function renderListView(Request $request)
    {
        $jadwalPelajaran = JadwalPelajaran::query()
            ->with(['rombel.kelas', 'mataPelajaran', 'guru'])
            ->when($request->filled('rombel_id'), fn($q) => $q->where('rombel_id', $request->rombel_id))
            ->when($request->filled('hari'), fn($q) => $q->where('hari', $request->hari))
            ->when($request->filled('guru_id'), fn($q) => $q->where('guru_id', $request->guru_id))
            ->when($request->filled('mapel_id'), fn($q) => $q->where('mapel_id', $request->mapel_id))
            ->when($request->filled('tahun_ajaran_id'), function($q) use ($request) {
                return $q->whereHas('rombel', fn($sq) => $sq->where('tahunajaran_id', $request->tahun_ajaran_id));
            })
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jamke')
            ->paginate(15)
            ->withQueryString();

        $rombels = Rombel::with('kelas')->get();
        $gurus   = Guru::aktif()->get();
        $mapels  = MataPelajaran::orderBy('nama')->get();
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->orderBy('semester', 'desc')->get();

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
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->orderBy('semester', 'desc')->get();
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
        $tahunAjarans = TahunAjaran::orderBy('tahunajaran', 'desc')->orderBy('semester', 'desc')->get();
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


    public function create()
    {
        $rombels  = Rombel::with('kelas')->get();
        $mapels   = MataPelajaran::all();
        $gurus    = Guru::aktif()->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('akademik::jadwal-pelajaran.create', compact('rombels', 'mapels', 'gurus', 'hariList'));
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
        $mapels   = MataPelajaran::all();
        $gurus    = Guru::aktif()->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('akademik::jadwal-pelajaran.edit', compact('jadwalPelajaran', 'rombels', 'mapels', 'gurus', 'hariList'));
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
        
        $copied = 0;
        foreach ($sourceJadwal as $item) {
            // Check if target already has this schedule
            $exists = JadwalPelajaran::where('rombel_id', $request->to_rombel_id)
                ->where('hari', $item->hari)
                ->where('jamke', $item->jamke)
                ->exists();

            if (!$exists) {
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
                // Reuse existing conflict logic if possible, or simple check here
                $conflict = JadwalPelajaran::where('hari', $data['hari'])
                    ->where('jamke', $data['jamke'])
                    ->where(function($q) use ($data) {
                        $q->where('guru_id', $data['guru_id'])
                          ->orWhere('rombel_id', $data['rombel_id']);
                    })->first();

                if ($conflict) {
                    throw new \Exception("Baris ke-" . ($index + 1) . " bentrok: Guru/Rombel sudah ada jadwal di hari {$data['hari']} jam ke-{$data['jamke']}.");
                }

                JadwalPelajaran::create($data);
                $created++;
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('akademik.jadwal-pelajaran.index')
                ->with('success', "Berhasil menyimpan $created jadwal sekaligus!");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
