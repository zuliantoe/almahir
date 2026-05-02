<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\PegawaiManager\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        // Jika Guru atau Siswa, jadikan modul Akademik sebagai halaman utama mereka
        if ($user && ($user->hasRole('GURU') || $user->hasRole('SISWA'))) {
            return redirect()->route('akademik.index');
        }

        // 1. Menghitung Total Guru (berdasarkan role GURU)
        $totalGuru = User::withRole('GURU')->count();

        // 2. Menghitung Total Staf (berdasarkan role STAFF)
        $totalStaff = User::withRole('STAFF')->count();

        // 3. Menghitung Total Pegawai (Gabungan seluruh SDM di modul Pegawai)
        $totalSdm = Pegawai::count();

        // 4. Data Siswa (Dummy dulu karena modul belum ada)
        $totalSiswa = 0;
        $totalKelas = 0;

        return view('dashboard', [
            'totalSdm' => $totalSdm,
            'totalGuru' => $totalGuru,
            'totalStaff' => $totalStaff,
            'totalSiswa' => $totalSiswa,
            'totalKelas' => $totalKelas,
        ]);
    }
}
