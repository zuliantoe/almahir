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

    /**
     * Tampilkan jadwal pelajaran.

     * - GURU : hanya jadwal miliknya (tabel mingguan)
     * - SISWA : jadwal kelasnya (tabel mingguan)
     * - Admin : semua jadwal (list paginated + filter)
     */
    public function index(Request $request)
    {
        $user    = auth()->user();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jamKes   = [1, 2, 3, 4, 5, 6, 7, 8];

        // ── KONTEKS GURU ─────────────────────────────────────────────────
        if ($user && $user->hasRole('GURU')) {
            $guru = $user->ref;

            // Jika minta tampil ALL atau ada filter pencarian, tampilkan view index (list)
            if ($request->get('tampil') === 'all' || $request->hasAny(['rombel_id', 'hari', 'guru_id'])) {
                $jadwalPelajaran = JadwalPelajaran::query()
                    ->with(['rombel.kelas', 'mataPelajaran', 'guru'])
                    ->when($request->filled('rombel_id'), fn($q) => $q->where('rombel_id', $request->rombel_id))
                    ->when($request->filled('hari'), fn($q) => $q->where('hari', $request->hari))
                    ->when($request->filled('guru_id'), fn($q) => $q->where('guru_id', $request->guru_id))
                    ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
                    ->orderBy('jamke')
                    ->paginate(15)
                    ->withQueryString();

                $rombels = Rombel::with('kelas')->get();
                $gurus   = Guru::aktif()->get();

                return view('akademik::jadwal-pelajaran.index', compact('jadwalPelajaran', 'rombels', 'gurus'));
            }

            // Default: Tampilkan Jadwal Mengajar Guru Terkait (Timetable)
            $rawJadwal = JadwalPelajaran::with(['mataPelajaran', 'rombel.kelas'])
                ->where('guru_id', $guru?->id)
                ->orderBy('hari')
                ->orderBy('jamke')
                ->get();

            $timetable = [];
            foreach ($rawJadwal as $j) {
                $timetable[$j->hari][$j->jamke] = $j;
            }

            $usedJamKes = $rawJadwal->pluck('jamke')->unique()->sort()->values()->toArray();

            return view('akademik::jadwal-pelajaran.timetable-guru', compact(
                'timetable', 'hariList', 'usedJamKes', 'rawJadwal', 'guru'
            ));
        }

        // ── KONTEKS SISWA ─────────────────────────────────────────────────
        if ($user && $user->hasRole('SISWA')) {
            $siswa       = $user->ref;
            $rombelSiswa = RombelSiswa::with('rombel.kelas')
                ->where('siswa_id', $siswa?->id)
                ->first();
            $rombelId    = $rombelSiswa?->rombel_id;

            $rawJadwal = collect();
            if ($rombelId) {
                $rawJadwal = JadwalPelajaran::with(['mataPelajaran', 'guru'])
                    ->where('rombel_id', $rombelId)
                    ->orderBy('hari')
                    ->orderBy('jamke')
                    ->get();
            }

            // Susun sebagai pivot [hari][jamke] => jadwal
            $timetable = [];
            foreach ($rawJadwal as $j) {
                $timetable[$j->hari][$j->jamke] = $j;
            }

            $usedJamKes = $rawJadwal->pluck('jamke')->unique()->sort()->values()->toArray();

            return view('akademik::jadwal-pelajaran.timetable-siswa', compact(
                'timetable', 'hariList', 'usedJamKes', 'rawJadwal', 'rombelSiswa'
            ));
        }

        // ── KONTEKS ADMIN / STAF ──────────────────────────────────────────
        $jadwalPelajaran = JadwalPelajaran::query()
            ->with(['rombel.kelas', 'mataPelajaran', 'guru'])
            ->when($request->filled('rombel_id'), fn($q) => $q->where('rombel_id', $request->rombel_id))
            ->when($request->filled('hari'), fn($q) => $q->where('hari', $request->hari))
            ->when($request->filled('guru_id'), fn($q) => $q->where('guru_id', $request->guru_id))
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jamke')
            ->paginate(15)
            ->withQueryString();

        $rombels = Rombel::with('kelas')->get();
        $gurus   = Guru::aktif()->get();

        $summaryJP = [];
        if ($request->filled('rombel_id')) {
            $rombel = Rombel::find($request->rombel_id);
            if ($rombel) {
                $mapels = JadwalPelajaran::where('rombel_id', $rombel->id)
                    ->pluck('mapel_id')
                    ->unique();
                
                foreach ($mapels as $mId) {
                    $summaryJP[$mId] = $this->jadwalService->hitungEstimasiTotalJP($mId, $rombel->id, $rombel->tahunajaran_id);
                }
            }
        }

        return view('akademik::jadwal-pelajaran.index', compact('jadwalPelajaran', 'rombels', 'gurus', 'summaryJP'));
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
}
