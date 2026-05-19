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
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 1. Validasi: Presensi baru dibuka jam 06:00 pagi
        if ($now->hour < 6) {
            return redirect()->back()->with('error', 'Presensi belum dibuka. Silakan kembali lagi pukul 06:00 pagi.');
        }

        // VALIDASI QR CODE
        $expectedToken = md5(config('absensi.qr_secret') . $today);
        if ($request->qr_token !== $expectedToken) {
            return redirect()->back()->with('error', 'QR Code tidak valid atau sudah kedaluwarsa. Silakan scan ulang di lobi.');
        }

        // VALIDASI GPS LOKASI
        if (!$request->lat || !$request->long) {
            return redirect()->back()->with('error', 'Koordinat GPS tidak ditemukan. Pastikan izin lokasi aktif di browser Anda.');
        }
        $officeLat = config('absensi.office_latitude');
        $officeLong = config('absensi.office_longitude');
        $officeRadius = config('absensi.office_radius');
        $distance = $this->calculateDistance($request->lat, $request->long, $officeLat, $officeLong);
        
        if ($distance > $officeRadius) {
            return redirect()->back()->with('error', 'Anda berada di luar radius kantor (Jarak: ' . round($distance) . 'm). Maksimal radius adalah ' . $officeRadius . 'm.');
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
            'lat_masuk' => $request->lat,
            'long_masuk' => $request->long,
            'keterangan' => $request->keterangan
        ]);

        $msg = $status == 'TERLAMBAT' ? 'Absen masuk berhasil (Terlambat).' : 'Absen masuk berhasil (Tepat Waktu).';
        
        return redirect()->route('absensi.index')->with('success', $msg);
    }

    /**
     * Update/Clock-out
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
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

        // VALIDASI QR CODE PULANG
        $expectedToken = md5(config('absensi.qr_secret') . $today);
        if ($request->qr_token !== $expectedToken) {
            return redirect()->back()->with('error', 'QR Code tidak valid. Silakan scan ulang di lobi untuk pulang.');
        }

        // VALIDASI GPS PULANG
        if (!$request->lat || !$request->long) {
            return redirect()->back()->with('error', 'Koordinat GPS tidak ditemukan.');
        }
        $officeLat = config('absensi.office_latitude');
        $officeLong = config('absensi.office_longitude');
        $officeRadius = config('absensi.office_radius');
        $distance = $this->calculateDistance($request->lat, $request->long, $officeLat, $officeLong);
        
        if ($distance > $officeRadius) {
            return redirect()->back()->with('error', 'Anda berada di luar radius kantor (Jarak: ' . round($distance) . 'm) untuk melakukan presensi pulang.');
        }

        $absensi->update([
            'jam_pulang' => $now->toTimeString(),
            'lat_pulang' => $request->lat,
            'long_pulang' => $request->long,
        ]);

        return redirect()->route('absensi.index')->with('success', 'Berhasil melakukan absen pulang. Selamat beristirahat!');
    }
}
