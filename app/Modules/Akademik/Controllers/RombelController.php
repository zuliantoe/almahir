<?php

namespace Modules\Akademik\Controllers;

use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;

class RombelController extends Controller
{
    public function index()
    {
        $rombel = Rombel::with(['kelas', 'tahunAjaran', 'walikelas'])->paginate(10);
        return view('akademik::rombel.index', compact('rombel'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $gurus = Guru::all();
        $siswas = Siswa::aktif()->get();
        $tahun_ajaran = TahunAjaran::aktif()->get();
        
        return view('akademik::rombel.create', compact('kelas', 'gurus', 'siswas', 'tahun_ajaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_rombel' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'tahunajaran_id' => 'required|exists:tahun_ajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id'
        ]);

        $rombel = Rombel::create([
            'nama_rombel' => $request->nama_rombel,
            'kelas_id' => $request->kelas_id,
            'tahunajaran_id' => $request->tahunajaran_id,
            'guru_id' => $request->guru_id,
            'keterangan' => $request->keterangan
        ]);

        foreach ($request->siswa_ids as $siswa_id) {
            RombelSiswa::create([
                'rombel_id' => $rombel->id,
                'siswa_id' => $siswa_id
            ]);
        }

        return redirect()->route('akademik.rombel.index')->with('success', 'Rombel berhasil dibuat');
    }

    public function show(Rombel $rombel)
    {
        $rombel->load(['kelas', 'tahunAjaran', 'walikelas', 'riwayatSiswa.siswa']);
        return view('akademik::rombel.show', compact('rombel'));
    }

    public function edit(Rombel $rombel)
    {
        $kelas = Kelas::all();
        $gurus = Guru::all();
        $siswas = Siswa::aktif()->get();
        $tahun_ajaran = TahunAjaran::all();
        $selected_siswas = $rombel->riwayatSiswa->pluck('siswa_id')->toArray();

        return view('akademik::rombel.edit', compact('rombel', 'kelas', 'gurus', 'siswas', 'tahun_ajaran', 'selected_siswas'));
    }

    public function update(Request $request, Rombel $rombel)
    {
        $request->validate([
            'nama_rombel' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'tahunajaran_id' => 'required|exists:tahun_ajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id'
        ]);

        $rombel->update([
            'nama_rombel' => $request->nama_rombel,
            'kelas_id' => $request->kelas_id,
            'tahunajaran_id' => $request->tahunajaran_id,
            'guru_id' => $request->guru_id,
            'keterangan' => $request->keterangan
        ]);

        // Sync students
        $rombel->riwayatSiswa()->delete();
        foreach ($request->siswa_ids as $siswa_id) {
            RombelSiswa::create([
                'rombel_id' => $rombel->id,
                'siswa_id' => $siswa_id
            ]);
        }

        return redirect()->route('akademik.rombel.index')->with('success', 'Rombel berhasil diupdate');
    }

    public function destroy(Rombel $rombel)
    {
        $rombel->delete();
        return redirect()->route('akademik.rombel.index')->with('success', 'Rombel berhasil dihapus');
    }
}
