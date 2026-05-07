<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();
        
        $tahunAjaranAsalId = $request->get('ta_asal');
        $tahunAjaranTujuanId = $request->get('ta_tujuan');
        $rombelAsalId = $request->get('rombel_asal');
        $rombelTujuanId = $request->get('rombel_tujuan');

        $rombelAsalList = collect();
        $rombelTujuanList = collect();
        $siswaList = collect();

        if ($tahunAjaranAsalId) {
            $rombelAsalList = Rombel::with('kelas')
                                    ->where('tahunajaran_id', $tahunAjaranAsalId)
                                    ->whereHas('riwayatSiswa', function($q) {
                                        $q->where('status', 'aktif');
                                    })->get();
        }
        if ($tahunAjaranTujuanId) {
            // Untuk rombel TUJUAN, tampilkan semua (karena mungkin masih kosong)
            $rombelTujuanList = Rombel::with('kelas')->where('tahunajaran_id', $tahunAjaranTujuanId)->get();
        }

        if ($rombelAsalId) {
            $siswaList = RombelSiswa::with('siswa')->where('rombel_id', $rombelAsalId)->get();
        }

        return view('akademik::kenaikan-kelas.index', [
            'title' => 'Kenaikan Kelas (Promosi Siswa)',
            'tahunAjarans' => $tahunAjarans,
            'rombelAsalList' => $rombelAsalList,
            'rombelTujuanList' => $rombelTujuanList,
            'siswaList' => $siswaList,
            'ta_asal' => $tahunAjaranAsalId,
            'ta_tujuan' => $tahunAjaranTujuanId,
            'rombel_asal' => $rombelAsalId,
            'rombel_tujuan' => $rombelTujuanId,
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'rombel_asal' => 'required|exists:rombel,id',
            'rombel_tujuan' => 'required|exists:rombel,id',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id'
        ]);

        if ($request->rombel_asal == $request->rombel_tujuan) {
            return redirect()->back()->with('error', 'Rombel tujuan tidak boleh sama dengan rombel asal.');
        }

        DB::beginTransaction();
        try {
            $count = 0;
            $rombelTujuan = Rombel::find($request->rombel_tujuan);
            
            foreach ($request->siswa_ids as $siswaId) {
                // 1. Matikan status di rombel asal (Ubah dari 'aktif' jadi 'naik')
                RombelSiswa::where('rombel_id', $request->rombel_asal)
                            ->where('siswa_id', $siswaId)
                            ->update(['status' => 'naik']);

                // 2. Cek apakah sudah ada di rombel tujuan (mencegah duplikasi)
                $exists = RombelSiswa::where('rombel_id', $request->rombel_tujuan)
                                     ->where('siswa_id', $siswaId)
                                     ->exists();
                
                if (!$exists) {
                    // 3. Buat baris baru di rombel tujuan dengan status 'aktif'
                    RombelSiswa::create([
                        'rombel_id' => $request->rombel_tujuan,
                        'siswa_id' => $siswaId,
                        'status' => 'aktif'
                    ]);
                    
                    // 4. Update data utama siswa (kolom kelas_id di tabel siswa)
                    if ($rombelTujuan && $rombelTujuan->kelas_id) {
                        \Modules\Siswa\Models\Siswa::where('id', $siswaId)->update([
                            'kelas_id' => $rombelTujuan->kelas_id
                        ]);
                    }
                    
                    $count++;
                }
            }
            DB::commit();
            return redirect()->route('akademik.kenaikan-kelas.index')->with('success', "Berhasil mempromosikan $count siswa ke rombel baru.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal melakukan promosi: ' . $e->getMessage());
        }
    }
}
