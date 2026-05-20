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

        // Prioritas: jika user adalah SISWA, kembalikan view dashboard khusus siswa
        if ($user && $user->hasRole('SISWA')) {
            $siswa = $user->ref;
            $totalP = \Modules\PenilaianDanPresensi\Models\Presensi::where('siswa_id', $siswa?->id)->count();
            $hadirP = \Modules\PenilaianDanPresensi\Models\Presensi::where('siswa_id', $siswa?->id)->where('status', 'Hadir')->count();
            $percent = $totalP > 0 ? round(($hadirP / $totalP) * 100) : 0;

            // Tahfidz Terbaru
            $hafalanTerbaru = class_exists(\Modules\PenilaianDanPresensi\Models\PenilaianTahfidz::class) 
                ? \Modules\PenilaianDanPresensi\Models\PenilaianTahfidz::where('siswa_id', $siswa?->id)->latest('tanggal')->first() 
                : null;

            // Tagihan
            $tagihanBelumLunas = 0;
            if (class_exists(\Modules\Keuangan\Models\TagihanSiswa::class)) {
                $tagihans = \Modules\Keuangan\Models\TagihanSiswa::where('target_id', $siswa?->id)
                            ->where('target_type', get_class($siswa))
                            ->where('status', '!=', 'Lunas')
                            ->get();
                $tagihanBelumLunas = $tagihans->sum('sisa_tagihan');
            }

            // Uang Saku Terakhir
            $uangSakuTerakhir = class_exists(\Modules\Keuangan\Models\UangSaku::class)
                ? \Modules\Keuangan\Models\UangSaku::where('siswa_id', $siswa?->id)->latest('tanggal')->first()
                : null;

            return view('siswa::dashboard', [
                'title' => 'Dashboard Santri',
                'breadcrumb' => 'Dashboard',
                'stats' => ['kehadiran' => $percent],
                'siswa' => $siswa,
                'currentRombel' => $siswa?->currentRombel()->with('kelas')->first(),
                'kamarInfo' => $siswa?->kamarPenghuni()->aktif()->first()?->kamar,
                'jadwalPiketHariIni' => $siswa?->jadwalPiket()->whereDate('tanggal', today())->get(),
                'hafalanTerbaru' => $hafalanTerbaru,
                'tagihanBelumLunas' => $tagihanBelumLunas,
                'uangSakuTerakhir' => $uangSakuTerakhir,
            ]);
        }

        // Jika bukan SISWA dan role-nya GURU, redirect ke penilaiandanpresensi.
        if ($user && !$user->hasRole('SISWA') && $user->hasRole('GURU')) {
            return redirect()->route('penilaiandanpresensi.index');
        }

        // 1. Menghitung Total Guru (berdasarkan role GURU)
        $totalGuru = User::withRole('GURU')->count();

        // 2. Menghitung Total Staf (berdasarkan role STAFF)
        $totalStaff = User::withRole('STAFF')->count();

        // 3. Menghitung Total Pegawai (Gabungan seluruh SDM di modul Pegawai)
        $totalSdm = class_exists(Pegawai::class) ? Pegawai::count() : 0;

        // 4. Data Modul Lainnya (Akademik, Siswa, Asrama, dsb)
        $totalSiswa = class_exists(\Modules\Siswa\Models\Siswa::class) ? \Modules\Siswa\Models\Siswa::count() : 0;
        $totalRombel = class_exists(\App\Modules\Akademik\Models\Rombel::class) ? \App\Modules\Akademik\Models\Rombel::count() : 0;
        $totalKelas = class_exists(\App\Modules\Akademik\Models\Kelas::class) ? \App\Modules\Akademik\Models\Kelas::count() : 0;
        $totalMapel = class_exists(\App\Modules\Akademik\Models\MataPelajaran::class) ? \App\Modules\Akademik\Models\MataPelajaran::count() : 0;

        $totalKamar = class_exists(\App\Modules\ManajemenAsetDanAsrama\Models\Kamar::class) ? \App\Modules\ManajemenAsetDanAsrama\Models\Kamar::count() : 0;
        $totalAset = class_exists(\App\Modules\ManajemenAsetDanAsrama\Models\Aset::class) ? \App\Modules\ManajemenAsetDanAsrama\Models\Aset::count() : 0;
        $totalKerusakan = class_exists(\App\Modules\ManajemenAsetDanAsrama\Models\Kerusakan::class) ? \App\Modules\ManajemenAsetDanAsrama\Models\Kerusakan::where('status_penanganan', '!=', 'selesai')->count() : 0;
        $totalCalonPegawai = class_exists(\Modules\PegawaiManager\Models\CalonPegawai::class) ? \Modules\PegawaiManager\Models\CalonPegawai::count() : 0;

        $tahunAjaranAktif = '2025/2026';
        if (class_exists(\App\Modules\Akademik\Models\TahunAjaran::class)) {
            $ta = \App\Modules\Akademik\Models\TahunAjaran::current();
            if ($ta) {
                $tahunAjaranAktif = $ta->tahunajaran . ' (' . ($ta->keterangan ?? 'Aktif') . ')';
            }
        }

        // Fetch module menus for the grid
        $menuRegistry = app(\App\Services\MenuRegistry::class);
        $moduleMenus = $menuRegistry->getMenusForUser();

        return view('dashboard', [
            'totalSdm' => $totalSdm,
            'totalGuru' => $totalGuru,
            'totalStaff' => $totalStaff,
            'totalSiswa' => $totalSiswa,
            'totalRombel' => $totalRombel,
            'totalKelas' => $totalKelas,
            'totalMapel' => $totalMapel,
            'totalKamar' => $totalKamar,
            'totalAset' => $totalAset,
            'totalKerusakan' => $totalKerusakan,
            'totalCalonPegawai' => $totalCalonPegawai,
            'tahunAjaranAktif' => $tahunAjaranAktif,
            'moduleMenus' => $moduleMenus,
        ]);
    }
}
