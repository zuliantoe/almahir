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

        // Jika user adalah GURU
        if ($user && $user->hasRole('GURU')) {
            $guru = $user->ref; // Guru model via morphTo
            $pegawai = $user->pegawai; // Pegawai model
            
            $hariIni = now()->locale('id')->dayName; // e.g. "Senin"
            $hariIniEn = now()->format('l'); // e.g. "Monday"
            $today = \Carbon\Carbon::now()->locale('id')->translatedFormat('l'); // e.g. "Senin"
            
            // 1. Jadwal Mengajar Hari Ini
            $jadwalHariIni = collect();
            if ($guru && class_exists(\App\Modules\Akademik\Models\JadwalPelajaran::class)) {
                $jadwalHariIni = \App\Modules\Akademik\Models\JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran'])
                    ->where('guru_id', $guru->id)
                    ->where('hari', $today)
                    ->orderBy('jamawal')
                    ->get();
            }

            // 2. Kalender Akademik (Event Terdekat)
            $eventTerdekat = collect();
            $tahunAjaranAktif = '2025/2026';
            if (class_exists(\App\Modules\Akademik\Models\TahunAjaran::class)) {
                $ta = \App\Modules\Akademik\Models\TahunAjaran::current();
                if ($ta) {
                    $tahunAjaranAktif = $ta->tahunajaran . ' (' . ($ta->keterangan ?? 'Aktif') . ')';
                }
            }
            if (class_exists(\App\Modules\Akademik\Models\KalenderAkademik::class)) {
                $eventTerdekat = \App\Modules\Akademik\Models\KalenderAkademik::where('tanggal_awal', '>=', now()->toDateString())
                    ->orderBy('tanggal_awal')
                    ->limit(3)
                    ->get();
            }

            // 3. Rekap & Statistik untuk Guru
            $totalJadwal = 0;
            $totalRombel = 0;
            if ($guru && class_exists(\App\Modules\Akademik\Models\JadwalPelajaran::class)) {
                $totalJadwal = \App\Modules\Akademik\Models\JadwalPelajaran::where('guru_id', $guru->id)->count();
                $totalRombel = \App\Modules\Akademik\Models\JadwalPelajaran::where('guru_id', $guru->id)
                    ->distinct('rombel_id')
                    ->count('rombel_id');
            }

            $pendingIzinSiswa = 0;
            if (class_exists(\Modules\PenilaianDanPresensi\Models\IzinSakit::class)) {
                $pendingIzinSiswa = \Modules\PenilaianDanPresensi\Models\IzinSakit::where('status', 'Pending')->count();
            }

            // 4. Status Absensi Pegawai Hari Ini
            $absensiHariIni = null;
            if ($pegawai && class_exists(\Modules\Absensi\Models\Absensi::class)) {
                $absensiHariIni = \Modules\Absensi\Models\Absensi::where('pegawai_id', $pegawai->id)
                    ->whereDate('tanggal', now()->toDateString())
                    ->first();
            }

            return view('dashboard_guru', compact(
                'guru', 'pegawai', 'tahunAjaranAktif', 'jadwalHariIni', 'eventTerdekat',
                'totalJadwal', 'totalRombel', 'pendingIzinSiswa', 'absensiHariIni'
            ));
        }

        // Jika user adalah siswa
        if ($user && $user->hasRole('SISWA')) {
            $siswa = null;
            if ($user->ref_type === \Modules\Siswa\Models\Siswa::class && $user->ref_id) {
                $siswa = \Modules\Siswa\Models\Siswa::with('kelas')->find($user->ref_id);
            }

            $hariIni = now()->locale('id')->dayName; // e.g. "Senin"
            $hariIniEn = now()->format('l'); // e.g. "Monday"

            // ─── MODUL AKADEMIK ───────────────────────────────────────────
            $jadwalHariIni = collect();
            $eventTerdekat = collect();
            $tahunAjaranAktif = '2025/2026';
            $ta = null;
            if (class_exists(\App\Modules\Akademik\Models\TahunAjaran::class)) {
                $ta = \App\Modules\Akademik\Models\TahunAjaran::current();
                if ($ta) {
                    $tahunAjaranAktif = $ta->tahunajaran . ' (' . ($ta->keterangan ?? 'Aktif') . ')';
                }
            }

            // Ambil Rombel Aktif Siswa
            $rombelInfo = null;
            if ($siswa && class_exists(\App\Modules\Akademik\Models\Rombel::class)) {
                $rombelQuery = \App\Modules\Akademik\Models\Rombel::with(['walikelas', 'kelas'])
                    ->whereHas('siswa', function($q) use ($siswa, $ta) {
                        $q->where('siswa.id', $siswa->id);
                        if ($ta) {
                            $q->where('rombel_siswa.tahunajaran_id', $ta->id);
                        }
                    });
                
                $rombelInfo = $rombelQuery->first();
                
                // Fallback: Jika di pivot belum diset, cari via kelas_id siswa di tahun ajaran aktif
                if (!$rombelInfo && $siswa->kelas_id) {
                    $rombelQueryFallback = \App\Modules\Akademik\Models\Rombel::with(['walikelas', 'kelas'])
                        ->where('kelas_id', $siswa->kelas_id);
                    if ($ta) {
                        $rombelQueryFallback->where('tahunajaran_id', $ta->id);
                    }
                    $rombelInfo = $rombelQueryFallback->first();
                }
            }

            // Fetch Jadwal Hari Ini
            if ($rombelInfo && class_exists(\App\Modules\Akademik\Models\JadwalPelajaran::class)) {
                $jadwalHariIni = \App\Modules\Akademik\Models\JadwalPelajaran::with(['mataPelajaran', 'guru'])
                    ->where('rombel_id', $rombelInfo->id)
                    ->where('hari', $hariIniEn)
                    ->orderBy('jamke')
                    ->get();
            }

            if (class_exists(\App\Modules\Akademik\Models\KalenderAkademik::class)) {
                $eventTerdekat = \App\Modules\Akademik\Models\KalenderAkademik::where('tanggal_awal', '>=', now()->toDateString())
                    ->orderBy('tanggal_awal')
                    ->limit(3)
                    ->get();
            }

            // ─── MODUL KEHADIRAN & NILAI ─────────────────────────────────
            $presensiHadir = 0; $presensiSakit = 0; $presensiIzin = 0; $presensiAlpa = 0;
            $rataNilai = 0; $totalNilai = 0;
            $lastTahfidz = null;
            $izinPending = 0;
            if ($siswa && class_exists(\Modules\PenilaianDanPresensi\Models\Presensi::class)) {
                $presensiHadir = \Modules\PenilaianDanPresensi\Models\Presensi::where('siswa_id', $siswa->id)->where('status', 'Hadir')->count();
                $presensiSakit = \Modules\PenilaianDanPresensi\Models\Presensi::where('siswa_id', $siswa->id)->where('status', 'Sakit')->count();
                $presensiIzin  = \Modules\PenilaianDanPresensi\Models\Presensi::where('siswa_id', $siswa->id)->where('status', 'Izin')->count();
                $presensiAlpa  = \Modules\PenilaianDanPresensi\Models\Presensi::where('siswa_id', $siswa->id)->where('status', 'Alpa')->count();
            }
            if ($siswa && class_exists(\Modules\PenilaianDanPresensi\Models\PenilaianAkademik::class)) {
                $rataNilai  = round(\Modules\PenilaianDanPresensi\Models\PenilaianAkademik::where('siswa_id', $siswa->id)->avg('nilai') ?? 0, 1);
                $totalNilai = \Modules\PenilaianDanPresensi\Models\PenilaianAkademik::where('siswa_id', $siswa->id)->count();
            }
            if ($siswa && class_exists(\Modules\PenilaianDanPresensi\Models\PenilaianTahfidz::class)) {
                $lastTahfidz = \Modules\PenilaianDanPresensi\Models\PenilaianTahfidz::where('siswa_id', $siswa->id)->latest('tanggal')->first();
            }
            if ($siswa && class_exists(\Modules\PenilaianDanPresensi\Models\IzinSakit::class)) {
                $izinPending = \Modules\PenilaianDanPresensi\Models\IzinSakit::where('siswa_id', $siswa->id)->where('status', 'Pending')->count();
            }

            // ─── MODUL KEUANGAN ───────────────────────────────────────────
            $totalUangSaku = 0;
            $uangSakuTerbaru = collect();
            if ($siswa && class_exists(\Modules\Keuangan\Models\UangSaku::class)) {
                $totalUangSaku  = \Modules\Keuangan\Models\UangSaku::where('siswa_id', $siswa->id)->where('status', 'Sudah Diterima Santri')->sum('jumlah');
                $uangSakuTerbaru = \Modules\Keuangan\Models\UangSaku::where('siswa_id', $siswa->id)->latest('tanggal')->limit(3)->get();
            }

            // ─── MODUL ASRAMA ─────────────────────────────────────────────
            $kamarInfo = null;
            $piketMendatang = collect();
            if ($siswa) {
                if (class_exists(\App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni::class)) {
                    $penghuni = \App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni::with('kamar')->where('siswa_id', $siswa->id)->first();
                    if ($penghuni && $penghuni->kamar) {
                        $kamarInfo = $penghuni->kamar;
                    }
                }
                if (class_exists(\App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket::class)) {
                    $piketMendatang = \App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket::where('siswa_id', $siswa->id)
                        ->where('tanggal', '>=', now()->toDateString())
                        ->orderBy('tanggal')
                        ->limit(3)
                        ->get();
                }
            }

            // ─── TEMAN SEKELAS ─────────────────────────────────────────────
            $temanSekelas = collect();
            if ($rombelInfo) {
                $temanSekelas = $rombelInfo->aktifSiswa()
                    ->where('siswa.id', '!=', $siswa->id)
                    ->limit(12)
                    ->get();
            }

            // ─── KURIKULUM KELAS ──────────────────────────────────────────
            $kurikulumKelas = collect();
            $kelasIdUntukKurikulum = $rombelInfo ? $rombelInfo->kelas_id : ($siswa ? $siswa->kelas_id : null);
            if ($kelasIdUntukKurikulum && class_exists(\App\Modules\Akademik\Models\Kurikulum::class)) {
                $kurikulumQuery = \App\Modules\Akademik\Models\Kurikulum::with(['mataPelajaran'])
                    ->where('kelas_id', $kelasIdUntukKurikulum);
                
                if (isset($ta) && $ta) {
                    $kurikulumQuery->where('tahunajaran_id', $ta->id);
                }
                
                $kurikulumKelas = $kurikulumQuery->orderBy('totaljam', 'desc')->get();
            }

            return view('dashboard_siswa', compact(
                'siswa', 'tahunAjaranAktif',
                // Akademik
                'jadwalHariIni', 'eventTerdekat', 'rombelInfo', 'temanSekelas', 'kurikulumKelas',
                // Kehadiran & Nilai
                'presensiHadir', 'presensiSakit', 'presensiIzin', 'presensiAlpa',
                'rataNilai', 'totalNilai', 'lastTahfidz', 'izinPending',
                // Keuangan
                'totalUangSaku', 'uangSakuTerbaru',
                // Asrama
                'kamarInfo', 'piketMendatang'
            ));
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
