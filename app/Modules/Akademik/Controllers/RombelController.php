<?php

namespace Modules\Akademik\Controllers;

use App\Http\Requests\AkademikRequest\StoreRombelRequest;
use App\Http\Requests\AkademikRequest\UpdateRombelRequest;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
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
        // Ambil tahun ajaran aktif atau terpilih
        $currentYear = TahunAjaran::where('status', 1)->first() ?? TahunAjaran::latest()->first();
        
        // Filter Siswa: Hanya yang AKTIF dan BELUM masuk rombel di tahun ajaran ini
        $siswas = Siswa::aktif()
            ->whereDoesntHave('rombelSiswa', function($q) use ($currentYear) {
                if ($currentYear) {
                    $q->where('tahunajaran_id', $currentYear->id);
                }
            })->get();

        if ($currentYear) {
            $tahun_ajaran = TahunAjaran::where('id', '>=', $currentYear->id - 1)
                                       ->orderBy('id', 'asc')
                                       ->limit(3)
                                       ->get();
        } else {
            $tahun_ajaran = TahunAjaran::orderBy('id', 'desc')->limit(3)->get();
        }
        
        return view('akademik::rombel.create', compact('kelas', 'gurus', 'siswas', 'tahun_ajaran'));
    }

    public function store(StoreRombelRequest $request)
    {
        DB::beginTransaction();
        try {
            $rombel = Rombel::create($request->only([
                'nama_rombel', 'kelas_id', 'tahunajaran_id', 'guru_id', 'keterangan'
            ]));

            $siswaData = collect($request->siswa_ids)->map(fn($id) => [
                'rombel_id' => $rombel->id,
                'siswa_id' => $id,
                'tahunajaran_id' => $rombel->tahunajaran_id,
                'kelas_id' => $rombel->kelas_id,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now()
            ])->toArray();

            RombelSiswa::insert($siswaData);

            DB::commit();
            return redirect()->route('akademik.rombel.index')->with('success', 'Rombel berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        // Filter Siswa: Yang belum punya rombel di tahun ini + yang sudah ada di rombel ini
        $siswas = Siswa::aktif()
            ->where(function($query) use ($rombel) {
                $query->whereDoesntHave('rombelSiswa', function($q) use ($rombel) {
                    $q->where('tahunajaran_id', $rombel->tahunajaran_id);
                })
                ->orWhereHas('rombelSiswa', function($q) use ($rombel) {
                    $q->where('rombel_id', $rombel->id)
                      ->where('tahunajaran_id', $rombel->tahunajaran_id);
                });
            })->get();
        
        // Smart Filter: Sama seperti create, tapi pastikan tahun rombel saat ini juga muncul
        $currentYear = TahunAjaran::where('status', 1)->first();
        $query = TahunAjaran::query();
        
        if ($currentYear) {
            $query->where('id', '>=', $currentYear->id - 1)->limit(5); // Kasih range lebih dikit buat edit
        }
        
        // Pastikan tahun yang sedang diedit TETAP muncul di list
        $tahun_ajaran = $query->orWhere('id', $rombel->tahunajaran_id)
                             ->orderBy('id', 'asc')
                             ->get();

        $selected_siswas = $rombel->riwayatSiswa()
            ->where('tahunajaran_id', $rombel->tahunajaran_id)
            ->pluck('siswa_id')->toArray();

        return view('akademik::rombel.edit', compact('rombel', 'kelas', 'gurus', 'siswas', 'tahun_ajaran', 'selected_siswas'));
    }

    public function update(UpdateRombelRequest $request, Rombel $rombel)
    {
        DB::beginTransaction();
        try {
            $rombel->update($request->only([
                'nama_rombel', 'kelas_id', 'tahunajaran_id', 'guru_id', 'keterangan'
            ]));

            // Pintar: Jangan asal delete. Jika tahun/kelas berubah, ini adalah "Kenakan Kelas" versi edit.
            // Untuk mempermudah sesuai request user: "edit aja kelas tapi masih romblenya"
            // Kita pastikan data siswa yang ada sekarang tercatat di snapshot baru jika berubah.
            
            $oldTahunId = $rombel->getOriginal('tahunajaran_id');
            $oldKelasId = $rombel->getOriginal('kelas_id');

            if ($request->tahunajaran_id != $oldTahunId || $request->kelas_id != $oldKelasId) {
                // Tandai yang lama sebagai 'naik' jika tahun/kelas berubah
                RombelSiswa::where('rombel_id', $rombel->id)
                    ->where('tahunajaran_id', $oldTahunId)
                    ->where('kelas_id', $oldKelasId)
                    ->where('status', 'aktif')
                    ->update(['status' => 'naik']);
            } else {
                // Jika tidak berubah (cuma edit biasa), baru boleh hapus/sync yang di tahun/kelas saat ini
                RombelSiswa::where('rombel_id', $rombel->id)
                    ->where('tahunajaran_id', $rombel->tahunajaran_id)
                    ->where('kelas_id', $rombel->kelas_id)
                    ->delete();
            }
            
            $siswaData = collect($request->siswa_ids)->map(fn($id) => [
                'rombel_id' => $rombel->id,
                'siswa_id' => $id,
                'tahunajaran_id' => $rombel->tahunajaran_id,
                'kelas_id' => $rombel->kelas_id,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now()
            ])->toArray();

            RombelSiswa::insert($siswaData);

            DB::commit();
            return redirect()->route('akademik.rombel.index')->with('success', 'Rombel berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Rombel $rombel)
    {
        $rombel->delete();
        return redirect()->route('akademik.rombel.index')->with('success', 'Rombel berhasil dihapus');
    }
}
