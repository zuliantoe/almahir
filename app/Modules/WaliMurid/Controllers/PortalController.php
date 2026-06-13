<?php

namespace Modules\WaliMurid\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    /**
     * Display Wali Murid Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ensure user is linked to a WaliMurid record
        if (!$user->ref_id || $user->ref_type !== \Modules\WaliMurid\Models\WaliMurid::class) {
            return redirect('/dashboard')->with('error', 'Akun Anda tidak terhubung dengan data Wali Murid.');
        }

        $wali = $user->ref;
        $siswas = $wali->siswa()->with(['kelas'])->get();

        return view('walimurid::portal.dashboard', [
            'title' => 'Portal Wali Murid',
            'wali' => $wali,
            'siswas' => $siswas,
        ]);
    }

    /**
     * Display specific student detail for parent
     */
    public function siswaDetail($id)
    {
        $user = Auth::user();
        $wali = $user->ref;

        // Ensure this wali is actually linked to this student
        $siswa = $wali->siswa()->findOrFail($id);

        return view('walimurid::portal.siswa_detail', [
            'title' => 'Detail Siswa: ' . $siswa->nama,
            'siswa' => $siswa,
        ]);
    }

    /**
     * Display student schedule for parent
     */
    public function siswaJadwal($id)
    {
        $user = Auth::user();
        $wali = $user->ref;

        // Ensure this wali is actually linked to this student
        $siswa = $wali->siswa()->findOrFail($id);

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $activeTahunAjaran = null;
        if (class_exists(\App\Modules\Akademik\Models\TahunAjaran::class)) {
            $activeTahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::current();
            if (!$activeTahunAjaran) {
                $activeTahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::orderBy('tahunajaran', 'desc')->first();
            }
        }

        $rombelSiswa = null;
        if (class_exists(\App\Modules\Akademik\Models\RombelSiswa::class)) {
            $rombelSiswa = \App\Modules\Akademik\Models\RombelSiswa::with(['rombel.kelas'])
                ->where('siswa_id', $siswa->id)
                ->when($activeTahunAjaran, function($q) use ($activeTahunAjaran) {
                    return $q->whereHas('rombel', fn($sq) => $sq->where('tahunajaran_id', $activeTahunAjaran->id));
                })
                ->first();
        }

        $rombelId = $rombelSiswa?->rombel_id;

        $rawJadwal = collect();
        if ($rombelId && class_exists(\App\Modules\Akademik\Models\JadwalPelajaran::class)) {
            $rawJadwal = \App\Modules\Akademik\Models\JadwalPelajaran::with(['mataPelajaran', 'guru'])
                ->where('rombel_id', $rombelId)
                ->orderBy('hari')
                ->orderBy('jamke')
                ->get();
        }

        $timetable = [];
        foreach ($rawJadwal as $j) {
            $timetable[$j->hari][$j->jamke] = $j;
        }
        $usedJamKes = $rawJadwal->pluck('jamke')->unique()->sort()->values()->toArray();

        return view('walimurid::portal.siswa_jadwal', [
            'title' => 'Jadwal Pelajaran: ' . $siswa->nama,
            'siswa' => $siswa,
            'rombelSiswa' => $rombelSiswa,
            'timetable' => $timetable,
            'hariList' => $hariList,
            'usedJamKes' => $usedJamKes,
            'rawJadwal' => $rawJadwal,
            'activeTahunAjaran' => $activeTahunAjaran,
        ]);
    }

    /**
     * Display student payments & bills for parent
     */
    public function siswaPembayaran($id)
    {
        $user = Auth::user();
        $wali = $user->ref;

        // Ensure this wali is actually linked to this student
        $siswa = $wali->siswa()->findOrFail($id);

        $tagihans = collect();
        if (class_exists(\Modules\Keuangan\Models\TagihanSiswa::class)) {
            $tagihans = \Modules\Keuangan\Models\TagihanSiswa::where('target_id', $siswa->id)
                ->whereIn('target_type', ['Modules\Siswa\Models\Siswa', 'App\Modules\Siswa\Models\Siswa'])
                ->orderBy('tanggal_tagihan', 'desc')
                ->get();
        }

        $pembayarans = collect();
        if (class_exists(\Modules\Keuangan\Models\PembayaranSiswa::class)) {
            $pembayarans = \Modules\Keuangan\Models\PembayaranSiswa::with('tagihanSiswa')
                ->where('siswa_id', $siswa->id)
                ->orderBy('tanggal_pembayaran', 'desc')
                ->get();
        }

        return view('walimurid::portal.siswa_pembayaran', [
            'title' => 'Tagihan & Pembayaran: ' . $siswa->nama,
            'siswa' => $siswa,
            'tagihans' => $tagihans,
            'pembayarans' => $pembayarans,
        ]);
    }
}
