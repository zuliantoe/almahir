<?php

namespace Modules\Absensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Absensi\Models\Absensi;
use Modules\PegawaiManager\Models\Pegawai;
use Modules\Perizinan\Models\Perizinan;
use Modules\Absensi\Models\HariLibur;
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

        $isHariLibur = HariLibur::whereDate('tanggal', $date)->exists();

        $alpaCount = 0;
        if (!$carbonDate->isWeekend() && !$isHariLibur) {
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
        $rekapItems = $paginatedPegawai->getCollection()->map(function($p) use ($absensiHariIni, $perizinanHariIni, $carbonDate, $isHariLibur) {
            $absensi = $absensiHariIni->get($p->id);
            // FIX: cari izin menggunakan pegawai.id (bukan pegawai.user_id)
            $izin = $perizinanHariIni->get($p->id);

            $status = 'ALPA';
            $color = 'danger';
            $jamMasuk = '-';
            $jamPulang = '-';

            if ($absensi) {
                $status = $absensi->status;
                if ($status == 'TEPAT WAKTU') {
                    $color = 'success';
                } elseif ($status == 'TERLAMBAT') {
                    $color = 'warning';
                } elseif (in_array($status, ['SAKIT', 'IZIN'])) {
                    $color = 'info';
                } else {
                    $color = 'danger';
                }
                $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i') : '-';
                $jamPulang = $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i') : '-';
            } elseif ($izin) {
                $status = strtoupper($izin->jenis_izin);
                $color = 'info';
            } elseif ($carbonDate->isWeekend() || $isHariLibur) {
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

        $perizinanHariIni = Perizinan::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->whereIn('user_id', $ids)
            ->get()
            ->keyBy('user_id');

        $isHariLibur = HariLibur::whereDate('tanggal', $date)->exists();

        $filename = "Laporan_Absensi_{$date}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($pegawais, $absensiHariIni, $perizinanHariIni, $carbonDate, $isHariLibur) {
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
                } elseif ($carbonDate->isWeekend() || $isHariLibur) {
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

        // Ambil 5 scan terakhir hari ini
        $lastScans = Absensi::with(['pegawai.typePegawai', 'pegawai.user'])
            ->whereDate('tanggal', $today)
            ->latest('updated_at') // Urutkan berdasarkan update terakhir (biar masuk/pulang naik ke atas)
            ->take(5)
            ->get()
            ->map(function($abs) {
                return (object) [
                    'nama' => $abs->pegawai->nama,
                    'jabatan' => $abs->pegawai->typePegawai->nama_type ?? 'Pegawai',
                    'avatar' => $abs->pegawai->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($abs->pegawai->nama).'&background=0D8ABC&color=fff&size=80',
                    'jam' => $abs->jam_pulang ? Carbon::parse($abs->jam_pulang)->format('H:i:s') : Carbon::parse($abs->jam_masuk)->format('H:i:s'),
                    'tipe' => $abs->jam_pulang ? 'PULANG' : 'MASUK',
                    'status' => $abs->status
                ];
            });

        return view('absensi::manage.qr-generator', [
            'title' => 'Layar Pemindai Absensi',
            'qrToken' => $qrToken,
            'lastScans' => $lastScans
        ]);
    }

    /**
     * Store manual attendance input by Admin.
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:TEPAT WAKTU,TERLAMBAT,ALPA,SAKIT,IZIN',
            'jam_masuk' => 'nullable|string',
            'jam_pulang' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $tanggal = $request->tanggal;
        $pegawaiId = $request->pegawai_id;
        $status = $request->status;

        $absensi = Absensi::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        $data = [
            'status' => $status,
            'keterangan' => $request->keterangan,
        ];

        if (in_array($status, ['TEPAT WAKTU', 'TERLAMBAT'])) {
            $data['jam_masuk'] = $request->jam_masuk ? $tanggal . ' ' . $request->jam_masuk : null;
            $data['jam_pulang'] = $request->jam_pulang ? $tanggal . ' ' . $request->jam_pulang : null;
        } else {
            $data['jam_masuk'] = null;
            $data['jam_pulang'] = null;
        }

        if ($absensi) {
            $absensi->update($data);
        } else {
            Absensi::create(array_merge([
                'pegawai_id' => $pegawaiId,
                'tanggal' => $tanggal,
            ], $data));
        }

        return redirect()->back()->with('success', 'Data absensi manual berhasil disimpan.');
    }

    /**
     * Handle scan card AJAX request from Lobby Webcam.
     */
    public function scanCard(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $pegawai = Pegawai::where('qr_token', $request->qr_token)->first();

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak dikenali atau QR Code tidak terdaftar.'
            ], 404);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Cek apakah sudah absen hari ini
        $absensi = Absensi::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absensi) {
            // Logika Check-in: batas toleransi jam 07:15
            $limitTime = Carbon::createFromTimeString('07:15:00');
            $status = $now->gt($limitTime) ? 'TERLAMBAT' : 'TEPAT WAKTU';

            $newAbsen = Absensi::create([
                'pegawai_id' => $pegawai->id,
                'tanggal' => $today,
                'jam_masuk' => $now->toTimeString(),
                'status' => $status,
                'keterangan' => 'Scan Kartu Lobi'
            ]);

            return response()->json([
                'success' => true,
                'type' => 'MASUK',
                'employee' => [
                    'nama' => $pegawai->nama,
                    'jabatan' => $pegawai->typePegawai->nama_type ?? 'Pegawai',
                    'avatar' => $pegawai->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($pegawai->nama).'&background=0D8ABC&color=fff&size=80',
                ],
                'time' => $now->format('H:i:s'),
                'status' => $status,
                'message' => 'Presensi MASUK berhasil (' . ($status == 'TERLAMBAT' ? 'Terlambat' : 'Tepat Waktu') . ')'
            ]);
        } else {
            // Logika Check-out
            if ($absensi->jam_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai ' . $pegawai->nama . ' sudah melakukan presensi masuk & pulang hari ini.'
                ], 400);
            }

            // Validasi Jam Pulang (Minimal Jam 16:00)
            if ($now->hour < 16) {
                $sisaJam = 16 - $now->hour - 1;
                $sisaMenit = 60 - $now->minute;
                return response()->json([
                    'success' => false,
                    'message' => 'Belum waktunya pulang! Minimal jam 16:00 (Sisa ' . $sisaJam . ' jam ' . $sisaMenit . ' menit).'
                ], 400);
            }

            $absensi->update([
                'jam_pulang' => $now->toTimeString()
            ]);

            return response()->json([
                'success' => true,
                'type' => 'PULANG',
                'employee' => [
                    'nama' => $pegawai->nama,
                    'jabatan' => $pegawai->typePegawai->nama_type ?? 'Pegawai',
                    'avatar' => $pegawai->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($pegawai->nama).'&background=0D8ABC&color=fff&size=80',
                ],
                'time' => $now->format('H:i:s'),
                'status' => $absensi->status,
                'message' => 'Presensi PULANG berhasil.'
            ]);
        }
    }
}
