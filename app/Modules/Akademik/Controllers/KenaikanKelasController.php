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
        // 1. Identifikasi Tahun Aktif (Tujuan)
        $destinationYear = TahunAjaran::where('status', 1)->first();
        if (!$destinationYear) {
            $destinationYear = TahunAjaran::orderBy('id', 'desc')->first();
        }

        // 2. Identifikasi Tahun Asal (Biasanya tahun sebelum yang aktif / tahun yang baru dimatikan)
        $sourceYear = TahunAjaran::where('id', '<', $destinationYear->id)
            ->orderBy('id', 'desc')
            ->first();

        // Jika tidak ada tahun sebelumnya, gunakan tahun aktif (mungkin sistem baru)
        if (!$sourceYear) {
            $sourceYear = $destinationYear;
        }

        // Only show rombels from the source academic year that need promotion
        $rombels = Rombel::where('tahunajaran_id', $sourceYear->id)
            ->with(['kelas', 'tingkat', 'riwayatSiswa' => function($q) use ($sourceYear) {
                $q->where('tahunajaran_id', $sourceYear->id)->where('status', 'aktif');
            }])
            ->get();
        
        $maxTingkatId = Tingkat::max('id');
        
        return view('akademik::kenaikan-kelas.index', compact('rombels', 'maxTingkatId', 'destinationYear', 'sourceYear'));
    }

    public function getRombel(Request $request)
    {
        $activeYear = TahunAjaran::where('status', 1)->first();
        if (!$activeYear) $activeYear = TahunAjaran::orderBy('id', 'desc')->first();

        $rombels = Rombel::where('tahunajaran_id', $activeYear->id)
            ->with(['kelas', 'tingkat'])
            ->get();
            
        return response()->json($rombels);
    }

    public function getSiswa(Request $request)
    {
        $siswas = RombelSiswa::where('rombel_id', $request->rombel_id)
            ->where('status', 'aktif')
            ->with('siswa')
            ->get();
            
        return response()->json($siswas);
    }

    public function process(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombel,id',
            'siswa_ids' => 'required|array',
            'status'    => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            $rombel = Rombel::with(['kelas', 'tingkat'])->findOrFail($request->rombel_id);
            $tahunAsalId = $rombel->tahunajaran_id;
            
            // Cari tahun ajaran berikutnya
            $tahunTujuan = TahunAjaran::where('id', '>', $tahunAsalId)->orderBy('id', 'asc')->first();
            
            if (!$tahunTujuan) {
                throw new \Exception("Tahun ajaran berikutnya belum tersedia. Silakan buat Tahun Ajaran baru terlebih dahulu.");
            }

            // 1. Identifikasi Tingkat dan Kelas Baru (untuk yang naik)
            $tingkatBaru = Tingkat::where('id', '>', $rombel->tingkat_id)->orderBy('id', 'asc')->first();
            $kelasBaruId = $rombel->kelas_id;
            if ($tingkatBaru) {
                $kelasBaruId = $this->findKelasPadanan($rombel->kelas_id, $tingkatBaru);
            }

            foreach ($request->siswa_ids as $siswaId) {
                $statusPilihan = $request->status[$siswaId] ?? 'naik';
                
                if ($statusPilihan === 'naik') {
                    if (!$tingkatBaru) {
                        $statusPilihan = 'lulus';
                    } else {
                        // History: Update status lama
                        RombelSiswa::where('rombel_id', $rombel->id)
                            ->where('siswa_id', $siswaId)
                            ->where('status', 'aktif')
                            ->update(['status' => 'naik']);

                        // Record Baru: Tetap di Rombel yang sama, tapi tahun dan kelas baru
                        RombelSiswa::create([
                            'siswa_id'       => $siswaId,
                            'rombel_id'      => $rombel->id,
                            'tahunajaran_id' => $tahunTujuan->id,
                            'kelas_id'       => $kelasBaruId,
                            'status'         => 'aktif'
                        ]);
                    }
                } 
                
                if ($statusPilihan === 'tidak_naik') {
                    // History: Update status lama
                    RombelSiswa::where('rombel_id', $rombel->id)
                        ->where('siswa_id', $siswaId)
                        ->where('status', 'aktif')
                        ->update(['status' => 'tidak_naik']);

                    // PINDAH ke Rombel Lain (Rombel yang akan menempati tingkat/kelas ini di tahun baru)
                    $rombelRetensi = Rombel::firstOrCreate([
                        'tahunajaran_id' => $tahunTujuan->id,
                        'tingkat_id'     => $rombel->tingkat_id,
                        'kelas_id'      => $rombel->kelas_id,
                    ], [
                        'nama_rombel'   => $rombel->nama_rombel, // Nama sama tapi ID beda
                        'guru_id'       => $rombel->guru_id
                    ]);

                    RombelSiswa::create([
                        'siswa_id'       => $siswaId,
                        'rombel_id'      => $rombelRetensi->id,
                        'tahunajaran_id' => $tahunTujuan->id,
                        'kelas_id'       => $rombel->kelas_id,
                        'status'         => 'aktif'
                    ]);
                }

                if ($statusPilihan === 'lulus') {
                    RombelSiswa::where('rombel_id', $rombel->id)
                        ->where('siswa_id', $siswaId)
                        ->where('status', 'aktif')
                        ->update(['status' => 'lulus']);
                        
                    DB::table('siswa')->where('id', $siswaId)->update(['status' => 'lulus']);
                }
            }

            // 2. Update Rombel Utama ke Tahun/Tingkat Baru (Progression)
            if ($tingkatBaru) {
                $rombel->tahunajaran_id = $tahunTujuan->id;
                $rombel->tingkat_id     = $tingkatBaru->id;
                $rombel->kelas_id      = $kelasBaruId;
                $rombel->save();
            } else {
                // Jika semua lulus/tinggal, rombel ini mungkin "selesai" di tahun asal
                // Tapi untuk konsistensi, kita pindahkan saja ke tahun baru sebagai 'archived' atau update jika ada
                $rombel->tahunajaran_id = $tahunTujuan->id;
                $rombel->save();
            }

            DB::commit();
            return redirect()->route('akademik.kenaikan-kelas.index')->with('success', "Berhasil memproses rombel {$rombel->nama_rombel} ke Tahun Ajaran {$tahunTujuan->tahunajaran}");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk mencari kelas dengan nama mirip di tingkat berbeda
     */
    private function findKelasPadanan($kelasAsalId, $tingkatBaru)
    {
        $kelasAsal = Kelas::with('tingkat')->find($kelasAsalId);
        if (!$kelasAsal) return null;

        $namaAsal = $kelasAsal->nama_kelas;
        $kodeTingkatLama = $kelasAsal->tingkat->kode_tingkat;
        $kodeTingkatBaru = $tingkatBaru->kode_tingkat;

        // X IPA 1 -> XI IPA 1
        $suffix = trim(str_replace($kodeTingkatLama, '', $namaAsal));
        $namaBaru = $kodeTingkatBaru . ' ' . $suffix;

        $kelasBaru = Kelas::where('nama_kelas', 'like', '%' . $suffix . '%')
            ->where('tingkat_id', $tingkatBaru->id)
            ->first();

        return $kelasBaru ? $kelasBaru->id : $kelasAsalId;
    }

    /**
     * Helper untuk generate nama rombel
     */
    private function generateNamaRombel($namaLama, $tingkatBaru)
    {
        return $namaLama; 
    }
}
