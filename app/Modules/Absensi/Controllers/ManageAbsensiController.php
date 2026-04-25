<?php

namespace Modules\Absensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Absensi\Models\Absensi;
use Modules\PegawaiManager\Models\Pegawai;
use Modules\Perizinan\Models\Perizinan;
use Carbon\Carbon;
use Illuminate\View\View;

class ManageAbsensiController extends Controller
{
    /**
     * Display a listing of all attendance records for Management.
     * This version integrates with Perizinan module.
     */
    public function index(Request $request): View
    {
        $date = $request->date ?: Carbon::today()->toDateString();
        $carbonDate = Carbon::parse($date);
        
        // 1. Ambil semua pegawai aktif
        $pegawaiQuery = Pegawai::with(['typePegawai', 'user']);

        if ($request->search) {
            $pegawaiQuery->where('nama', 'like', "%{$request->search}%");
        }

        $allPegawai = $pegawaiQuery->get();

        // 2. Ambil data absensi hari tersebut
        $absensiHariIni = Absensi::whereDate('tanggal', $date)->get()->keyBy('pegawai_id');

        // 3. Ambil data perizinan yang disetujui untuk hari tersebut
        $perizinanHariIni = Perizinan::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->get()
            ->keyBy('user_id'); // user_id di tabel ini adalah id pegawai

        // 4. Gabungkan data
        $rekap = $allPegawai->map(function($p) use ($absensiHariIni, $perizinanHariIni, $carbonDate) {
            $absensi = $absensiHariIni->get($p->id);
            $izin = $perizinanHariIni->get($p->id);

            $status = 'ALPA';
            $color = 'danger';
            $jamMasuk = '-';
            $jamPulang = '-';

            if ($absensi) {
                $status = $absensi->status;
                $color = $status == 'TEPAT WAKTU' ? 'success' : 'warning';
                $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i') : '-';
                $jamPulang = $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i') : '-';
            } elseif ($izin) {
                $status = strtoupper($izin->jenis_izin);
                $color = 'info';
            } elseif ($carbonDate->isWeekend()) {
                $status = 'LIBUR';
                $color = 'secondary';
            }

            return (object) [
                'pegawai' => $p,
                'status' => $status,
                'color' => $color,
                'jam_masuk' => $jamMasuk,
                'jam_pulang' => $jamPulang,
                'absensi_id' => $absensi ? $absensi->id : null,
                'izin_id' => $izin ? $izin->id : null
            ];
        });

        // Pagination Manual (Simple)
        $perPage = 10;
        $page = $request->get('page', 1);
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $rekap->forPage($page, $perPage),
            $rekap->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Stats
        $stats = [
            'total' => $allPegawai->count(),
            'hadir' => $rekap->whereIn('status', ['TEPAT WAKTU', 'TERLAMBAT'])->count(),
            'izin' => $rekap->whereIn('status', ['IZIN', 'SAKIT', 'CUTI', 'DINAS LUAR'])->count(),
            'alpa' => $rekap->where('status', 'ALPA')->count(),
        ];

        return view('absensi::manage.index', [
            'title' => 'Monitoring Kehadiran Pegawai',
            'rekap' => $paginatedItems,
            'stats' => $stats,
            'selectedDate' => $date
        ]);
    }
}
