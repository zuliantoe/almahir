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
        
        $pegawaiQuery = Pegawai::with(['typePegawai', 'user']);

        if ($request->search) {
            $pegawaiQuery->where('nama', 'like', "%{$request->search}%");
        }

        // ==========================================
        // 1. PENGHITUNGAN STATISTIK (LEVEL DATABASE)
        // ==========================================
        $totalPegawai = (clone $pegawaiQuery)->count();
        
        $izinQuery = Perizinan::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date);

        $absensiQuery = Absensi::whereDate('tanggal', $date);

        // Filter stats jika ada pencarian nama spesifik
        // PENTING: Perizinan menyimpan pegawai.id di kolom user_id-nya (bukan sys_users.id)
        if ($request->search) {
            $pegawaiIds = (clone $pegawaiQuery)->pluck('id');
            $izinQuery->whereIn('user_id', $pegawaiIds); // FIX: gunakan pegawai.id bukan pegawai.user_id
            $absensiQuery->whereIn('pegawai_id', $pegawaiIds);
        }

        $izinCount = $izinQuery->count();
        $absensiCount = $absensiQuery->count();

        $alpaCount = 0;
        if (!$carbonDate->isWeekend()) {
            $alpaCount = $totalPegawai - $absensiCount - $izinCount;
            if ($alpaCount < 0) $alpaCount = 0;
        }

        $stats = [
            'total' => $totalPegawai,
            'hadir' => $absensiCount,
            'izin'  => $izinCount,
            'alpa'  => $alpaCount,
        ];

        // ==========================================
        // 2. PAGINATION & PENGAMBILAN DATA (SQL LEVEL)
        // ==========================================
        $perPage = 10;
        $paginatedPegawai = $pegawaiQuery->paginate($perPage)->withQueryString();

        // 3. Ambil data relasi HANYA untuk 10 data di halaman ini
        // PENTING: Perizinan menyimpan pegawai.id di kolom user_id-nya (bukan sys_users.id)
        $idsOnPage = $paginatedPegawai->pluck('id');

        $absensiHariIni = Absensi::whereDate('tanggal', $date)
            ->whereIn('pegawai_id', $idsOnPage)
            ->get()
            ->keyBy('pegawai_id');

        // FIX: gunakan pegawai.id (bukan pegawai.user_id) karena Perizinan menyimpan pegawai.id
        $perizinanHariIni = Perizinan::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->whereIn('user_id', $idsOnPage)
            ->get()
            ->keyBy('user_id');

        // 4. Modifikasi map collection yang akan dilempar ke View
        $rekapItems = $paginatedPegawai->getCollection()->map(function($p) use ($absensiHariIni, $perizinanHariIni, $carbonDate) {
            $absensi = $absensiHariIni->get($p->id);
            // FIX: cari izin menggunakan pegawai.id (bukan pegawai.user_id)
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

        // Pakukan ulang item ke object pagination Paginator
        $paginatedPegawai->setCollection($rekapItems);

        return view('absensi::manage.index', [
            'title' => 'Monitoring Kehadiran Pegawai',
            'rekap' => $paginatedPegawai,
            'stats' => $stats,
            'selectedDate' => $date
        ]);
    }

    /**
     * Export attendance records to CSV.
     */
    public function export(Request $request)
    {
        $date = $request->date ?: Carbon::today()->toDateString();
        $carbonDate = Carbon::parse($date);
        
        $pegawaiQuery = Pegawai::with(['typePegawai']);

        if ($request->search) {
            $pegawaiQuery->where('nama', 'like', "%{$request->search}%");
        }

        $pegawais = $pegawaiQuery->get();
        $ids = $pegawais->pluck('id'); // pegawai.id

        $absensiHariIni = Absensi::whereDate('tanggal', $date)
            ->whereIn('pegawai_id', $ids)
            ->get()
            ->keyBy('pegawai_id');

        // FIX: gunakan pegawai.id karena Perizinan menyimpan pegawai.id di kolom user_id
        $perizinanHariIni = Perizinan::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->whereIn('user_id', $ids)
            ->get()
            ->keyBy('user_id');

        $filename = "Laporan_Absensi_{$date}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($pegawais, $absensiHariIni, $perizinanHariIni, $carbonDate) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM agar Excel membaca UTF-8 dengan benar
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['No', 'Nama Pegawai', 'Tipe Pegawai', 'Jam Masuk', 'Jam Pulang', 'Status Kehadiran'], ';');

            foreach ($pegawais as $index => $p) {
                $absensi = $absensiHariIni->get($p->id);
                $izin = $perizinanHariIni->get($p->id); // FIX: gunakan pegawai.id

                $status = 'ALPA';
                $jamMasuk = '-';
                $jamPulang = '-';

                if ($absensi) {
                    $status = $absensi->status;
                    $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i') : '-';
                    $jamPulang = $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i') : '-';
                } elseif ($izin) {
                    $status = strtoupper($izin->jenis_izin);
                } elseif ($carbonDate->isWeekend()) {
                    $status = 'LIBUR';
                }

                fputcsv($file, [
                    $index + 1,
                    $p->nama,
                    $p->typePegawai->nama_type ?? 'Pegawai',
                    $jamMasuk,
                    $jamPulang,
                    $status
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tampilkan Halaman TV Lobi (Generator QR Code)
     */
    public function qrGenerator(): View
    {
        $today = Carbon::today()->toDateString();
        // Token Harian (Bisa di-upgrade jadi token jam-jaman atau menitan nanti)
        $qrToken = md5(config('absensi.qr_secret') . $today);

        return view('absensi::manage.qr-generator', [
            'title' => 'Layar Pemindai Absensi',
            'qrToken' => $qrToken
        ]);
    }
}
