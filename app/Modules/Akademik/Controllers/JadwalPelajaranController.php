<?php

namespace Modules\Akademik\Controllers;

use App\Http\Requests\AkademikRequest\StoreJadwalPelajaranRequest;
use App\Http\Requests\AkademikRequest\UpdateJadwalPelajaranRequest;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\MataPelajaran;
use Modules\Guru\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $jadwalPelajaran = JadwalPelajaran::query()
            ->with(['rombel.kelas', 'mataPelajaran', 'guru'])
            ->when($request->filled('rombel_id'), function ($query) use ($request) {
                $query->where('rombel_id', $request->rombel_id);
            })
            ->when($request->filled('hari'), function ($query) use ($request) {
                $query->where('hari', $request->hari);
            })
            ->orderBy('hari')
            ->orderBy('jamke')
            ->paginate(10)
            ->withQueryString();

        $rombels = Rombel::with('kelas')->get();
        return view('akademik::jadwal-pelajaran.index', compact('jadwalPelajaran', 'rombels'));
    }

    public function create()
    {
        $rombels = Rombel::with('kelas')->get();
        $mapels = MataPelajaran::all();
        $gurus = Guru::aktif()->get();
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
        $rombels = Rombel::with('kelas')->get();
        $mapels = MataPelajaran::all();
        $gurus = Guru::aktif()->get();
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
