<?php

namespace Modules\Akademik\Controllers;

use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Tingkat;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    public function index()
    {
        $tahun_ajaran = TahunAjaran::orderBy('id', 'desc')->get();
        $tingkat = Tingkat::all();
        $kelas = Kelas::all();
        
        return view('akademik::kenaikan-kelas.index', compact('tahun_ajaran', 'tingkat', 'kelas'));
    }

    public function getRombel(Request $request)
    {
        $rombels = Rombel::where('tahunajaran_id', $request->tahunajaran_id)
            ->with(['kelas', 'tingkat'])
            ->get();
            
        return response()->json($rombels);
    }

    public function getSiswa(Request $request)
    {
        $siswas = RombelSiswa::where('rombel_id', $request->rombel_id)
            ->where('tahunajaran_id', $request->tahunajaran_id)
            ->where('status', 'aktif')
            ->with('siswa')
            ->get();
            
        return response()->json($siswas);
    }

    public function process(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required',
            'tahunajaran_asal_id' => 'required',
            'tahunajaran_tujuan_id' => 'required',
            'kelas_tujuan_id' => 'required',
            'siswa_ids' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            $rombel = Rombel::findOrFail($request->rombel_id);
            $kelasTujuan = Kelas::findOrFail($request->kelas_tujuan_id);

            // 1. Update Rombel Record (The "Current" state moves to the next year)
            // But wait, the user said "edit aja kelas tapi masih romblenya".
            // So we update the main Rombel record to the new year and class.
            $rombel->update([
                'tahunajaran_id' => $request->tahunajaran_tujuan_id,
                'kelas_id' => $request->kelas_tujuan_id,
                'tingkat_id' => $kelasTujuan->tingkat_id
            ]);

            // 2. Archive the old students' status to 'naik' for the old year
            RombelSiswa::where('rombel_id', $rombel->id)
                ->where('tahunajaran_id', $request->tahunajaran_asal_id)
                ->where('status', 'aktif')
                ->update(['status' => 'naik']);

            // 3. Create new records for students who are promoted
            $siswaData = [];
            foreach ($request->siswa_ids as $siswaId) {
                $siswaData[] = [
                    'rombel_id' => $rombel->id,
                    'siswa_id' => $siswaId,
                    'tahunajaran_id' => $request->tahunajaran_tujuan_id,
                    'kelas_id' => $request->kelas_tujuan_id,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            RombelSiswa::insert($siswaData);

            DB::commit();
            return redirect()->route('akademik.rombel.index')->with('success', 'Proses kenaikan kelas berhasil dilakukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses kenaikan kelas: ' . $e->getMessage());
        }
    }
}
