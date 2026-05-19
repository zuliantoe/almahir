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

        // Langsung masuk ke modul Penilaian & Presensi sebagai dashboard utama
        if ($user && ($user->hasRole('GURU') || $user->hasRole('SISWA'))) {
            return redirect()->route('penilaiandanpresensi.index');
        }

        // Langsung arahkan Wali Murid ke Portal Wali
        if ($user && $user->hasRole('WALI_MURID')) {
            return redirect()->route('walimurid.portal.dashboard');
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

        // Fetch module menus for the grid
        $menuRegistry = app(\App\Services\MenuRegistry::class);
        $moduleMenus = $menuRegistry->getMenusForUser();

        return view('dashboard', [
            'totalSdm' => $totalSdm,
            'totalGuru' => $totalGuru,
            'totalStaff' => $totalStaff,
            'totalSiswa' => $totalSiswa,
            'totalKelas' => $totalKelas,
            'moduleMenus' => $moduleMenus,
        ]);
    }
}
