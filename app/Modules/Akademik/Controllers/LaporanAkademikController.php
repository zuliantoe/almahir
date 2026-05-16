<?php

namespace Modules\Akademik\Controllers;

use App\Modules\Akademik\Models\Guru;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\Siswa;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LaporanAkademikController extends Controller
{
    /**
     * Halaman Utama Laporan Akademik (Dashboard Laporan)
     */
    public function index()
    {
        $user = auth()->user();
        $tahunAktif = TahunAjaran::where('status', true)->first();
        
        // Ringkasan data untuk dashboard laporan
        $stats = [
            'total_rombel' => 0,
            'total_siswa_aktif' => 0,
            'total_guru_mengajar' => 0,
            'total_jadwal' => 0,
        ];

        if ($user->hasRole('SUPER_ADMIN') || $user->hasRole('STAFF') || $user->hasRole('KEPALA_SEKOLAH')) {
            if ($tahunAktif) {
                $stats['total_rombel'] = Rombel::where('tahunajaran_id', $tahunAktif->id)->count();
                $stats['total_siswa_aktif'] = RombelSiswa::where('tahunajaran_id', $tahunAktif->id)
                    ->where('status', 'aktif')
                    ->count();
                $stats['total_guru_mengajar'] = JadwalPelajaran::whereHas('rombel', function($q) use ($tahunAktif) {
                        $q->where('tahunajaran_id', $tahunAktif->id);
                    })->distinct('guru_id')->count('guru_id');
                $stats['total_jadwal'] = JadwalPelajaran::whereHas('rombel', function($q) use ($tahunAktif) {
                        $q->where('tahunajaran_id', $tahunAktif->id);
                    })->count();
            }
        } elseif ($user->hasRole('GURU')) {
            // Stats untuk Guru
            $guru = $user->guru; // Asumsi ada relasi
            if ($guru && $tahunAktif) {
                $stats['total_rombel'] = JadwalPelajaran::where('guru_id', $guru->id)
                    ->whereHas('rombel', fn($q) => $q->where('tahunajaran_id', $tahunAktif->id))
                    ->distinct('rombel_id')->count('rombel_id');
                $stats['total_jadwal'] = JadwalPelajaran::where('guru_id', $guru->id)
                    ->whereHas('rombel', fn($q) => $q->where('tahunajaran_id', $tahunAktif->id))
                    ->count();
                // Hitung estimasi JP (simpel saja, sum jamke atau semacamnya)
                $stats['total_siswa_aktif'] = RombelSiswa::whereIn('rombel_id', function($q) use ($guru, $tahunAktif) {
                    $q->select('rombel_id')->from('jadwal_pelajaran')
                      ->where('guru_id', $guru->id);
                })->where('status', 'aktif')->count();
            }
        } elseif ($user->hasRole('SISWA')) {
            // Stats untuk Siswa
            $siswa = $user->siswa;
            if ($siswa && $tahunAktif) {
                $rombelSiswa = RombelSiswa::where('siswa_id', $siswa->id)
                    ->where('tahunajaran_id', $tahunAktif->id)
                    ->first();
                if ($rombelSiswa) {
                    $stats['total_rombel'] = 1; // Kelas sendiri
                    $stats['total_siswa_aktif'] = RombelSiswa::where('rombel_id', $rombelSiswa->rombel_id)
                        ->where('status', 'aktif')->count();
                    $stats['total_jadwal'] = JadwalPelajaran::where('rombel_id', $rombelSiswa->rombel_id)->count();
                }
            }
        }

        return view('akademik::laporan.index', compact('stats', 'tahunAktif'));
    }
}
