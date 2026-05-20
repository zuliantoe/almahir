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
        
        // Tambahan data detail asrama
        $totalKapasitas = Kamar::sum('kapasitas');
        $kamarPenuh = Kamar::all()->filter(fn($k) => $k->sisa <= 0)->count();
        $sisaKapasitas = $totalKapasitas - $totalPenghuni;

        // Ambil Jadwal Piket Hari Ini untuk Dashboard
        $today = date('Y-m-d');
        $jadwalToday = JadwalPiket::with(['siswa'])
                        ->where('tanggal', $today)
                        ->orderBy('shift', 'asc')
                        ->orderBy('lokasi_piket', 'asc')
                        ->get();
        
        $asetByStatus = [
            'baik'              => Aset::whereIn('status_kondisi', ['baik', 'sudah_diperbaiki'])->count(),
            'rusak'             => Aset::where('status_kondisi', 'rusak')->count(),
            'dalam_perbaikan'   => Aset::where('status_kondisi', 'dalam_perbaikan')->count(),
            'sudah_diperbaiki'  => Aset::where('status_kondisi', 'sudah_diperbaiki')->count(),
        ];
        
        return view('manajemenasetdanasrama::index', [
            'title'             => 'Manajemen Aset & Asrama',
            'totalAset'         => $totalAset,
            'totalPengajuan'    => $totalPengajuan,
            'totalPengadaan'    => $totalPengadaan,
            'totalKerusakan'    => $totalKerusakan,
            'totalPemeliharaan' => $totalPemeliharaan,
            'totalKamar'        => $totalKamar,
            'totalPenghuni'     => $totalPenghuni,
            'totalKapasitas'    => $totalKapasitas,
            'kamarPenuh'        => $kamarPenuh,
            'sisaKapasitas'     => $sisaKapasitas,
            'jadwalToday'       => $jadwalToday->groupBy('lokasi_piket'),
            'asetByStatus'      => $asetByStatus,
        ]);
    }
}