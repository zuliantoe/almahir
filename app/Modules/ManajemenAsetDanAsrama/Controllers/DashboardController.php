<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\Aset;
use Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use Modules\ManajemenAsetDanAsrama\Models\PengadaanAset;
use Modules\ManajemenAsetDanAsrama\Models\Kerusakan;
use Modules\ManajemenAsetDanAsrama\Models\Pemeliharaan;
use Modules\ManajemenAsetDanAsrama\Models\Kamar;
use Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni;
use Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;

class DashboardController extends BaseController
{
    /**
     * Display dashboard / index page.
     */
    public function index(Request $request): View
    {
        $totalAset = Aset::count();
        $totalPengajuan = PengajuanAset::count();
        $totalPengadaan = PengadaanAset::count();
        $totalKerusakan = Kerusakan::count();
        $totalPemeliharaan = Pemeliharaan::count();
        $totalKamar = Kamar::count();
        $totalPenghuni = KamarPenghuni::count();
        
        $pengajuanTerbaru = PengajuanAset::with('pengaju')
                                ->latest()
                                ->take(5)
                                ->get();
        
        $asetTerbaru = Aset::with('pengadaan')
                            ->latest()
                            ->take(5)
                            ->get();
        
        $jadwalPiketHariIni = JadwalPiket::with('siswa')
                                ->where('hari', $this->getHariIndo(date('l')))
                                ->where('status', 'belum')
                                ->take(5)
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
            'pengajuanTerbaru'  => $pengajuanTerbaru,
            'asetTerbaru'       => $asetTerbaru,
            'jadwalPiketHariIni'=> $jadwalPiketHariIni,
            'asetByStatus'      => $asetByStatus,
        ]);
    }
}