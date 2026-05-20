<?php

namespace Modules\Siswa\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;
use App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni;
use App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;
use Modules\Siswa\Models\Siswa;
use Illuminate\Pagination\LengthAwarePaginator;

class SiswaAsramaController extends Controller
{
    /**
     * Display a listing of kamar (rooms) for students.
     */
    public function kamarIndex(Request $request): View
    {
        $query = Kamar::with(['penghuniAktif.siswa']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kamar', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $kamar = $query->paginate(15)->withQueryString();

        return view('siswa::asrama.kamar-index', [
            'title' => 'Data Kamar Asrama',
            'kamar' => $kamar,
        ]);
    }

    /**
     * Display details of a specific kamar for students.
     */
    public function kamarShow(string $id): View
    {
        $kamar = Kamar::findOrFail($id);
        
        // Ambil daftar penghuni yang sedang aktif di kamar ini (Urut: Ketua > Wakil > Anggota)
        $penghuniAktif = $kamar->penghuni()
            ->with('siswa')
            ->where(function($query) {
                $query->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>', now());
            })
            ->orderByRaw("CASE 
                WHEN jabatan = 'Ketua Kamar' THEN 1 
                WHEN jabatan = 'Wakil Ketua Kamar' THEN 2 
                ELSE 3 
            END ASC")
            ->orderBy('id', 'asc')
            ->get();

        // Ambil riwayat penghuni sebelumnya
        $latestHistory = $kamar->penghuni()
            ->whereNotNull('tanggal_keluar')
            ->where('tanggal_keluar', '<=', now())
            ->orderBy('updated_at', 'desc')
            ->first();

        $riwayatPenghuni = collect();
        if ($latestHistory) {
            $riwayatPenghuni = $kamar->penghuni()
                ->with('siswa')
                ->where('updated_at', $latestHistory->updated_at)
                ->get();
        }

        return view('siswa::asrama.kamar-show', [
            'title'           => 'Detail Kamar: ' . $kamar->nama_kamar,
            'kamar'           => $kamar,
            'penghuniAktif'   => $penghuniAktif,
            'riwayatPenghuni' => $riwayatPenghuni,
        ]);
    }

    /**
     * Display listing of penghuni (residents) for students.
     */
    public function penghuniIndex(Request $request): View
    {
        $query = KamarPenghuni::with(['kamar', 'siswa'])->aktif();

        if ($request->filled('kamar_id')) {
            $query->where('kamar_id', $request->kamar_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $penghuni = $query->latest()
                         ->paginate(15)
                         ->withQueryString();
        
        return view('siswa::asrama.penghuni-index', [
            'title'    => 'Data Penghuni Kamar',
            'penghuni' => $penghuni,
            'kamar'    => Kamar::all(),
        ]);
    }

    /**
     * Display listing of jadwal piket (shifts) for students.
     */
    public function jadwalPiket(Request $request): View
    {
        // 1. Ambil list tanggal unik yang punya jadwal
        $dateQuery = JadwalPiket::select('tanggal')->distinct();

        if ($request->filled('tanggal_mulai')) {
            $dateQuery->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $dateQuery->where('tanggal', '<=', $request->tanggal_selesai);
        }
        if ($request->filled('lokasi_piket')) {
            $dateQuery->where('lokasi_piket', $request->lokasi_piket);
        }
        if ($request->filled('q')) {
            $dateQuery->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%');
            });
        }

        $allDates = $dateQuery->orderBy('tanggal', 'desc')->pluck('tanggal')->toArray();

        $today = now()->format('Y-m-d');
        $allDatesFormatted = array_map(function ($d) {
            return \Carbon\Carbon::parse($d)->format('Y-m-d');
        }, $allDates);

        $todayIndex = array_search($today, $allDatesFormatted);

        $perPage = 1;
        $currentPage = $request->input('page');

        if (!$currentPage && $todayIndex !== false) {
            $currentPage = $todayIndex + 1;
        } else {
            $currentPage = $currentPage ?: 1;
        }

        $currentDateSlice = array_slice($allDates, ($currentPage - 1) * $perPage, $perPage);

        $paginatedDates = new LengthAwarePaginator(
            $currentDateSlice,
            count($allDates),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $activeDate = count($currentDateSlice) > 0 ? $currentDateSlice[0] : null;

        $jadwalQuery = JadwalPiket::with(['siswa', 'kamar']);
        if ($activeDate) {
            $jadwalQuery->where('tanggal', $activeDate);
        } else {
            $jadwalQuery->where('id', 0);
        }

        if ($request->filled('q')) {
            $jadwalQuery->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('lokasi_piket')) {
            $jadwalQuery->where('lokasi_piket', $request->lokasi_piket);
        }

        $jadwalData = $jadwalQuery->orderBy('shift', 'asc')
            ->orderBy('lokasi_piket', 'asc')
            ->get();

        return view('siswa::asrama.jadwal-piket', [
            'title'             => 'Jadwal Piket Asrama',
            'paginatedDates'    => $paginatedDates,
            'activeDate'        => $activeDate,
            'jadwalData'        => $jadwalData,
            'lokasiList'        => JadwalPiket::select('lokasi_piket')->distinct()->pluck('lokasi_piket')->toArray(),
        ]);
    }
}
