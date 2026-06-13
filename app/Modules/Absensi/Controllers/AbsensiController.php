<?php

namespace Modules\Absensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Absensi\Models\Absensi;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AbsensiController extends Controller
{
    /**
     * Menghitung jarak antara dua titik koordinat (Haversine Formula) dalam meter.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad((float)$lat1);
        $lonFrom = deg2rad((float)$lon1);
        $latTo = deg2rad((float)$lat2);
        $lonTo = deg2rad((float)$lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * View Personal Attendance History
     */
    public function index(): View
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return view('absensi::error', ['message' => 'Data pegawai tidak ditemukan untuk akun ini.']);
        }

        // Ambil data absensi
        $absensi = Absensi::where('pegawai_id', $pegawai->id)
            ->latest('tanggal')
            ->get();

        // Ambil data perizinan yang disetujui (sebagai virtual absensi)
        $perizinan = \Modules\Perizinan\Models\Perizinan::where('user_id', $pegawai->id)
            ->where('status', 'disetujui')
            ->get();

        // Gabungkan dan petakan ke format yang sama untuk view
        $history = $absensi->map(function($item) {
            $item->is_izin = false;
            return $item;
        });

        foreach ($perizinan as $izin) {
            $current = Carbon::parse($izin->tanggal_mulai);
            $end = Carbon::parse($izin->tanggal_selesai);
            
            while ($current->lte($end)) {
                // Jangan tambahkan jika sudah ada absen fisik di tanggal tersebut
                if (!$history->contains('tanggal', $current->toDateString())) {
                    $history->push((object)[
                        'tanggal' => $current->copy(),
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'status' => strtoupper($izin->jenis_izin),
                        'keterangan' => $izin->alasan,
                        'is_izin' => true,
                        'late_minutes' => 0,
                        'work_duration' => '-',
                        'lat_masuk' => null,
                        'long_masuk' => null,
                        'lat_pulang' => null,
                        'long_pulang' => null,
                    ]);
                }
                $current->addDay();
            }
        }

        // Urutkan berdasarkan tanggal terbaru
        $history = $history->sortByDesc('tanggal');

        // Statistik
        $stats = [
            'hadir' => $history->whereIn('status', ['TEPAT WAKTU', 'TERLAMBAT'])->count(),
            'terlambat' => $history->where('status', 'TERLAMBAT')->count(),
            'izin' => $history->whereIn('status', ['IZIN', 'SAKIT', 'CUTI', 'DINAS LUAR'])->count(),
        ];

        // Pagination Manual
        $perPage = 10;
        $page = request()->get('page', 1);
        $paginatedHistory = new \Illuminate\Pagination\LengthAwarePaginator(
            $history->forPage($page, $perPage),
            $history->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('absensi::index', [
            'title' => 'Riwayat Absensi Saya',
            'absensi' => $paginatedHistory,
            'pegawai' => $pegawai,
            'stats' => $stats,
        ]);
    }

    /**
     * Show Attendance Recording Interface (Scan/Button)
     */
    public function create(): View
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
             return view('absensi::error', [
                'title' => 'Akses Ditolak',
                'message' => 'Akun Anda belum terhubung dengan data Pegawai. Silakan hubungi Admin.'
            ]);
        }

        $today = Carbon::today()->toDateString();
        
        $currentAbsen = Absensi::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        return view('absensi::create', [
            'title' => 'Presensi Harian',
            'pegawai' => $pegawai,
            'currentAbsen' => $currentAbsen
        ]);
    }

    /**
     * Store Check-in
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return $this->returnResponse($request, false, 'Akun Anda belum terhubung dengan data Pegawai.', 'absensi.index');
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Validasi Hari Libur (Minggu atau Tanggal Merah)
        if ($now->isSunday()) {
            return $this->returnResponse($request, false, 'Hari ini adalah hari Minggu (Libur). Anda tidak perlu melakukan presensi.', 'absensi.index');
        }

        $hariLibur = \Modules\Absensi\Models\HariLibur::where('tanggal', $today)->first();
        if ($hariLibur) {
            return $this->returnResponse($request, false, 'Hari ini adalah hari libur (' . $hariLibur->keterangan . '). Anda tidak perlu melakukan presensi.', 'absensi.index');
        }

        // 1. Validasi: Presensi baru dibuka jam 06:00 pagi
        if ($now->hour < 6) {
            return $this->returnResponse($request, false, 'Presensi belum dibuka. Silakan kembali lagi pukul 06:00 pagi.', 'absensi.index');
        }

        // VALIDASI QR CODE
        $expectedToken = md5(config('absensi.qr_secret') . $today);
        if ($request->qr_token !== $expectedToken) {
            return $this->returnResponse($request, false, 'QR Code tidak valid atau sudah kedaluwarsa. Silakan scan ulang di lobi.', 'absensi.index');
        }

        // VALIDASI GPS LOKASI
        if (!$request->lat || !$request->long) {
            return $this->returnResponse($request, false, 'Koordinat GPS tidak ditemukan. Pastikan izin lokasi aktif di browser Anda.', 'absensi.index');
        }
        $officeLat = config('absensi.office_latitude');
        $officeLong = config('absensi.office_longitude');
        $officeRadius = config('absensi.office_radius');
        $distance = $this->calculateDistance($request->lat, $request->long, $officeLat, $officeLong);
        
        if ($distance > $officeRadius) {
            return $this->returnResponse($request, false, 'Anda berada di luar radius kantor (Jarak: ' . round($distance) . 'm). Maksimal radius adalah ' . $officeRadius . 'm.', 'absensi.index');
        }

        // 2. Cek apakah sudah absen hari ini
        $existing = Absensi::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        if ($existing) {
            return $this->returnResponse($request, false, 'Anda sudah melakukan presensi masuk hari ini.', 'absensi.index');
        }

        // 3. Logika Tepat Waktu: Batas jam 07:00 + Toleransi 15 menit = 07:15
        $limitTime = Carbon::createFromTimeString('07:15:00');
        $status = $now->gt($limitTime) ? 'TERLAMBAT' : 'TEPAT WAKTU';

        Absensi::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => $today,
            'jam_masuk' => $now->toTimeString(),
            'status' => $status,
            'lat_masuk' => $request->lat,
            'long_masuk' => $request->long,
            'keterangan' => $request->keterangan
        ]);

        $msg = $status == 'TERLAMBAT' ? 'Absen masuk berhasil (Terlambat).' : 'Absen masuk berhasil (Tepat Waktu).';
        
        return $this->returnResponse($request, true, $msg, 'absensi.index');
    }

    /**
     * Update/Clock-out
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return $this->returnResponse($request, false, 'Akun Anda belum terhubung dengan data Pegawai.', 'absensi.index');
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $absensi = Absensi::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        if (!$absensi) {
            return $this->returnResponse($request, false, 'Anda belum melakukan absen masuk hari ini.', 'absensi.index');
        }

        if ($absensi->jam_pulang) {
            return $this->returnResponse($request, false, 'Anda sudah melakukan absen pulang hari ini.', 'absensi.index');
        }

        // Validasi Jam Pulang (Minimal Jam 16:00)
        if ($now->hour < 16) {
            $sisaJam = 16 - $now->hour - 1;
            $sisaMenit = 60 - $now->minute;
            return $this->returnResponse($request, false, "Belum waktunya pulang! Jam pulang minimal pukul 16:00 (Sisa waktu: $sisaJam jam $sisaMenit menit lagi).", 'absensi.index');
        }

        // VALIDASI QR CODE PULANG
        $expectedToken = md5(config('absensi.qr_secret') . $today);
        if ($request->qr_token !== $expectedToken) {
            return $this->returnResponse($request, false, 'QR Code tidak valid. Silakan scan ulang di lobi untuk pulang.', 'absensi.index');
        }

        // VALIDASI GPS PULANG
        if (!$request->lat || !$request->long) {
            return $this->returnResponse($request, false, 'Koordinat GPS tidak ditemukan.', 'absensi.index');
        }
        $officeLat = config('absensi.office_latitude');
        $officeLong = config('absensi.office_longitude');
        $officeRadius = config('absensi.office_radius');
        $distance = $this->calculateDistance($request->lat, $request->long, $officeLat, $officeLong);
        
        if ($distance > $officeRadius) {
            return $this->returnResponse($request, false, 'Anda berada di luar radius kantor (Jarak: ' . round($distance) . 'm) untuk melakukan presensi pulang.', 'absensi.index');
        }

        $absensi->update([
            'jam_pulang' => $now->toTimeString(),
            'lat_pulang' => $request->lat,
            'long_pulang' => $request->long,
        ]);

        return $this->returnResponse($request, true, 'Berhasil melakukan absen pulang. Selamat beristirahat!', 'absensi.index');
    }

    /**
     * Store Self-Service Manual Check-in (Absen Masuk Darurat)
     */
    public function storeSelfManual(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Akun Anda belum terhubung dengan data Pegawai.');
        }

        $request->validate([
            'keterangan' => 'required|string|min:5|max:255',
        ]);

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Validasi Hari Libur (Minggu atau Tanggal Merah)
        if ($now->isSunday()) {
            return redirect()->back()->with('error', 'Hari ini adalah hari Minggu (Libur). Anda tidak perlu melakukan presensi.');
        }

        $hariLibur = \Modules\Absensi\Models\HariLibur::where('tanggal', $today)->first();
        if ($hariLibur) {
            return redirect()->back()->with('error', 'Hari ini adalah hari libur (' . $hariLibur->keterangan . '). Anda tidak perlu melakukan presensi.');
        }

        // 1. Validasi: Presensi baru dibuka jam 06:00 pagi
        if ($now->hour < 6) {
            return redirect()->back()->with('error', 'Presensi belum dibuka. Silakan kembali lagi pukul 06:00 pagi.');
        }

        // 2. Cek apakah sudah absen hari ini
        $existing = Absensi::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah melakukan presensi masuk hari ini.');
        }

        // 3. Logika Tepat Waktu: Batas jam 07:00 + Toleransi 15 menit = 07:15
        $limitTime = Carbon::createFromTimeString('07:15:00');
        $status = $now->gt($limitTime) ? 'TERLAMBAT' : 'TEPAT WAKTU';

        Absensi::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => $today,
            'jam_masuk' => $now->toTimeString(),
            'status' => $status,
            'lat_masuk' => null,
            'long_masuk' => null,
            'keterangan' => '[Manual Darurat] - ' . $request->keterangan
        ]);

        $msg = $status == 'TERLAMBAT' ? 'Absen masuk darurat berhasil (Terlambat).' : 'Absen masuk darurat berhasil (Tepat Waktu).';
        
        return redirect()->route('absensi.index')->with('success', $msg);
    }

    /**
     * Update Self-Service Manual Clock-out (Absen Pulang Darurat)
     */
    public function updateSelfManual(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Akun Anda belum terhubung dengan data Pegawai.');
        }

        $request->validate([
            'keterangan' => 'required|string|min:5|max:255',
        ]);

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $absensi = Absensi::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        if (!$absensi) {
            return redirect()->back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        if ($absensi->jam_pulang) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        // Validasi Jam Pulang (Minimal Jam 16:00)
        if ($now->hour < 16) {
            $sisaJam = 16 - $now->hour - 1;
            $sisaMenit = 60 - $now->minute;
            return redirect()->back()->with('error', "Belum waktunya pulang! Jam pulang minimal pukul 16:00 (Sisa waktu: $sisaJam jam $sisaMenit menit lagi).");
        }

        // Append checkout reason to existing checkin reason
        $newKeterangan = $absensi->keterangan;
        if (empty($newKeterangan)) {
            $newKeterangan = '[Manual Darurat Pulang] - ' . $request->keterangan;
        } else {
            $newKeterangan .= ' | [Pulang Darurat] - ' . $request->keterangan;
        }

        $absensi->update([
            'jam_pulang' => $now->toTimeString(),
            'lat_pulang' => null,
            'long_pulang' => null,
            'keterangan' => $newKeterangan
        ]);

        return redirect()->route('absensi.index')->with('success', 'Berhasil melakukan absen pulang darurat. Selamat beristirahat!');
    }

    /**
     * Helper to return standard redirect or JSON response
     */
    private function returnResponse(Request $request, bool $success, string $message, string $redirectRoute)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message
            ], $success ? 200 : 400);
        }

        return $success 
            ? redirect()->route($redirectRoute)->with('success', $message)
            : redirect()->back()->withInput()->with('error', $message);
    }
}
