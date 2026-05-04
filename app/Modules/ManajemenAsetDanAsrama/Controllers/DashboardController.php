<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;
use App\Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use App\Modules\ManajemenAsetDanAsrama\Models\PengadaanAset;
use App\Modules\ManajemenAsetDanAsrama\Models\Kerusakan;
use App\Modules\ManajemenAsetDanAsrama\Models\Pemeliharaan;
use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;
use App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni;
use App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;

class DashboardController extends BaseController
{
    /**
     * Display dashboard / index page.
     */
    public function index(Request $request): View
    {
        $totalAset = Aset::count();
        $totalPengajuan = PengajuanAset::count();
        $totalPengadaan = PengadaanAset::whereHas('pengajuan')->count();
        $totalKerusakan = Kerusakan::whereHas('aset')->count();
        $totalPemeliharaan = Pemeliharaan::whereHas('aset')->count();
        $totalKamar = Kamar::count();
        $totalPenghuni = KamarPenghuni::aktif()->count();
        
        $jadwalPiketHariIni = JadwalPiket::with(['siswa', 'kamar'])
                                ->whereDate('tanggal', date('Y-m-d'))
                                ->where('status', 'belum')
                                ->take(10)
                                ->get();

        $asetByStatus = [
            'baik'              => Aset::where('status_kondisi', 'baik')->count(),
            'rusak'             => Aset::where('status_kondisi', 'rusak')->count(),
            'dalam_perbaikan'   => Aset::where('status_kondisi', 'dalam_perbaikan')->count(),
            'sudah_diperbaiki'  => Aset::where('status_kondisi', 'sudah_diperbaiki')->count(),
        ];
        
        return view('manajemenasetdanasrama::index', [
            'title'             => 'Dashboard Manajemen Aset & Asrama',
            'totalAset'         => $totalAset,
            'totalPengajuan'    => $totalPengajuan,
            'totalPengadaan'    => $totalPengadaan,
            'totalKerusakan'    => $totalKerusakan,
            'totalPemeliharaan' => $totalPemeliharaan,
            'totalKamar'        => $totalKamar,
            'totalPenghuni'     => $totalPenghuni,
            'jadwalPiketHariIni'=> $jadwalPiketHariIni,
            'asetByStatus'      => $asetByStatus,
        ]);
    }
}