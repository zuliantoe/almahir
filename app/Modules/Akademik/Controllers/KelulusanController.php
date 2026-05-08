<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use Modules\Siswa\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class KelulusanController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();
        $ta_id = $request->get('ta_id');
        $rombel_id = $request->get('rombel_id');

        $rombelList = collect();
        $siswaList = collect();

        if ($ta_id) {
            $rombelList = Rombel::with('kelas')
                                ->where('tahunajaran_id', $ta_id)
                                ->whereHas('riwayatSiswa', function($q) {
                                    $q->where('status', 'aktif');
                                })->get();
        }

        if ($rombel_id) {
            $siswaList = RombelSiswa::with('siswa')
                                    ->where('rombel_id', $rombel_id)
                                    ->where('status', 'aktif')
                                    ->get();
        }

        return view('akademik::kelulusan.index', [
            'title' => 'Kelulusan Siswa (Wisuda)',
            'tahunAjarans' => $tahunAjarans,
            'rombelList' => $rombelList,
            'siswaList' => $siswaList,
            'ta_id' => $ta_id,
            'rombel_id' => $rombel_id,
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombel,id',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id'
        ]);

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($request->siswa_ids as $siswaId) {
                // 1. Update status di rombel terakhir menjadi 'lulus'
                RombelSiswa::where('rombel_id', $request->rombel_id)
                            ->where('siswa_id', $siswaId)
                            ->update(['status' => 'lulus']);

                // 2. Update status global siswa menjadi 'lulus' (Alumni)
                $siswa = Siswa::find($siswaId);
                if ($siswa) {
                    $siswa->update(['status' => 'lulus']);

                    // 3. Matikan akses login User (Jika ada akun terkait)
                    \App\Models\User::where('ref_type', Siswa::class)
                                    ->where('ref_id', $siswaId)
                                    ->update(['account_status' => 'inactive']);
                }
                
                $count++;
            }
            DB::commit();
            return redirect()->route('akademik.kelulusan.index')->with('success', "Berhasil meluluskan $count siswa. Sekarang mereka berstatus Alumni.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses kelulusan: ' . $e->getMessage());
        }
    }
}
