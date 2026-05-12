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
            'siswa_ids' => 'required|array',
            'status' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            $rombelAsal = Rombel::findOrFail($request->rombel_id);
            $kelasTujuan = Kelas::findOrFail($request->kelas_tujuan_id);

            // 1. CLONE Rombel (Create NEW record for the target year)
            // This preserves the old Rombel record as history in the old year.
            $rombelBaru = Rombel::create([
                'nama_rombel' => $rombelAsal->nama_rombel, // You can also customize this if needed
                'tahunajaran_id' => $request->tahunajaran_tujuan_id,
                'kelas_id' => $request->kelas_tujuan_id,
                'tingkat_id' => $kelasTujuan->tingkat_id,
                'guru_id' => $rombelAsal->guru_id, // Carry over the teacher
                'keterangan' => 'Hasil kenaikan dari ' . $rombelAsal->nama_rombel . ' (' . $rombelAsal->tahunAjaran->tahunajaran . ')'
            ]);

            $siswaData = [];
            foreach ($request->siswa_ids as $siswaId) {
                $statusPilihan = $request->status[$siswaId] ?? 'naik';

                // 2. Archive the old students' status in the OLD Rombel
                RombelSiswa::where('rombel_id', $rombelAsal->id)
                    ->where('tahunajaran_id', $request->tahunajaran_asal_id)
                    ->where('siswa_id', $siswaId)
                    ->update(['status' => $statusPilihan]);

                // 3. Create new records for students who are promoted (naik) to the NEW Rombel
                if ($statusPilihan === 'naik') {
                    $siswaData[] = [
                        'rombel_id' => $rombelBaru->id,
                        'siswa_id' => $siswaId,
                        'tahunajaran_id' => $request->tahunajaran_tujuan_id,
                        'kelas_id' => $request->kelas_tujuan_id,
                        'status' => 'aktif',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
            
            if (!empty($siswaData)) {
                RombelSiswa::insert($siswaData);
            }

            DB::commit();
            return redirect()->route('akademik.rombel.index')->with('success', 'Proses kenaikan kelas berhasil. Rombel baru telah dibuat untuk tahun ajaran tujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }
}
