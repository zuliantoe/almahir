<?php

namespace Modules\Perizinan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Perizinan\Models\Perizinan;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\PengajuanIzinBaru;
use App\Notifications\StatusIzinDiperbarui;
use Carbon\Carbon;

class PerizinanController extends Controller
{
    /**
     * Hitung sisa cuti pegawai untuk tahun ini
     */
    private function getSisaCuti($pegawai): int
    {
        $currentYear = date('Y');
        
        // Hitung cuti/izin yang masih menunggu (pending) untuk tahun ini
        $pendingDays = Perizinan::where('user_id', $pegawai->id)
            ->where('potong_kuota', true)
            ->where('status', 'menunggu')
            ->whereYear('tanggal_mulai', $currentYear)
            ->sum('total_hari');

        // Sisa cuti tersedia = Jatah di database - yang sedang diajukan
        return max(0, $pegawai->sisa_cuti - $pendingDays);
    }

    /**
     * Display a listing of personal permissions or all permissions for admin
     */
    public function index(): View
    {
        $user = Auth::user();
        $search = request('search');
        $pegawaiFilterId = request('pegawai_id');
        
        $query = Perizinan::with('pegawai');
        
        if ($user->hasRole('SUPER_ADMIN') || $user->hasRole('STAF_TU')) {
            $isAdmin = true;
            $sisaCuti = null;
            
            if ($pegawaiFilterId) {
                $query->where('user_id', $pegawaiFilterId);
            }
        } else {
            $pegawai = $user->pegawai;
            if (!$pegawai) {
                 return view('perizinan::error', ['message' => 'Data pegawai tidak ditemukan.']);
            }
            $query->where('user_id', $pegawai->id);
            $isAdmin = false;
            $sisaCuti = $this->getSisaCuti($pegawai);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jenis_izin', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function($qPegawai) use ($search) {
                      $qPegawai->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($bulan = request('bulan')) {
            $year  = substr($bulan, 0, 4);
            $month = substr($bulan, 5, 2);
            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $perizinan = $query->latest()->paginate(10)->withQueryString();

        return view('perizinan::index', [
            'title' => 'Daftar Perizinan',
            'perizinan' => $perizinan,
            'isAdmin' => $isAdmin,
            'sisaCuti' => $sisaCuti
        ]);
    }

    /**
     * Show the form for creating a new request
     */
    public function create(): View
    {
        if (Auth::user()->hasRole(['SUPER_ADMIN', 'STAF_TU'])) {
            return view('perizinan::error', [
                'title' => 'Akses Terbatas',
                'message' => 'Administrator tidak diperbolehkan mengajukan izin/cuti pribadi.'
            ]);
        }

        $pegawai = Auth::user()->pegawai;
        
        if (!$pegawai) {
            return view('perizinan::error', [
                'title' => 'Akses Terbatas',
                'message' => 'Akun Anda tidak terhubung dengan data Pegawai. Silakan gunakan akun Pegawai Anda untuk mengajukan izin atau cuti.'
            ]);
        }

        $sisaCuti = $this->getSisaCuti($pegawai);

        return view('perizinan::create', [
            'title' => 'Ajukan Perizinan',
            'sisaCuti' => $sisaCuti
        ]);
    }

    /**
     * Store a newly created request in storage
     */
    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->hasRole(['SUPER_ADMIN', 'STAF_TU'])) {
            return redirect()->back()->with('error', 'Administrator tidak diperbolehkan mengajukan izin/cuti.');
        }

        $pegawai = Auth::user()->pegawai;

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Akun Anda tidak terhubung dengan data Pegawai.');
        }

        $request->validate([
            'jenis_izin' => 'required|in:izin,sakit,cuti,dinas luar',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $durasiHari = Carbon::parse($request->tanggal_mulai)->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;
        $impact = Perizinan::getImpactSettings($request->jenis_izin);

        if ($impact['potong_kuota']) {
            $sisaCuti = $this->getSisaCuti($pegawai);
            if ($durasiHari > $sisaCuti) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Pengajuan gagal. Sisa jatah cuti Anda tahun ini adalah {$sisaCuti} hari, namun Anda mengajukan {$durasiHari} hari.");
            }
        }

        // CEK OVERLAP: Apakah sudah ada izin yang DISETUJUI atau MENUNGGU di tanggal yang sama?
        $overlap = Perizinan::where('user_id', $pegawai->id)
            ->whereIn('status', ['disetujui', 'menunggu'])
            ->where(function($q) use ($request) {
                $q->whereDate('tanggal_mulai', '<=', $request->tanggal_selesai)
                  ->whereDate('tanggal_selesai', '>=', $request->tanggal_mulai);
            })
            ->first();

        if ($overlap) {
            $tglAwal = Carbon::parse($overlap->tanggal_mulai)->format('d/m/Y');
            $tglAkhir = Carbon::parse($overlap->tanggal_selesai)->format('d/m/Y');
            $statusLabel = $overlap->status === 'disetujui' ? 'DISETUJUI' : 'MENUNGGU PERSETUJUAN';
            return redirect()->back()
                ->withInput()
                ->with('error', "Pengajuan gagal. Anda sudah memiliki izin/cuti berstatus {$statusLabel} pada periode {$tglAwal} s/d {$tglAkhir}. Tidak dapat mengajukan izin baru di tanggal yang sama.");
        }

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('perizinan', 'public');
        }

        $perizinan = Perizinan::create([
            'user_id' => $pegawai->id,
            'jenis_izin' => $request->jenis_izin,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'bukti' => $buktiPath,
            'status' => 'menunggu',
            'potong_gaji' => $impact['potong_gaji'],
            'potong_kuota' => $impact['potong_kuota'],
            'total_hari' => $durasiHari,
        ]);

        // Notify Admins
        $admins = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['SUPER_ADMIN', 'STAF_TU']);
        })->get();
        
        Notification::send($admins, new PengajuanIzinBaru($perizinan));

        return redirect()->route('perizinan.index')->with('success', 'Pengajuan perizinan berhasil dikirim.');
    }

    /**
     * Show detail of a request
     */
    public function show($id): View
    {
        $perizinan = Perizinan::with('pegawai')->findOrFail($id);
        
        return view('perizinan::show', [
            'title' => 'Detail Perizinan',
            'perizinan' => $perizinan,
        ]);
    }

    /**
     * Approve/Reject request (Admin only)
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        if (!Auth::user()->hasRole(['SUPER_ADMIN', 'STAF_TU'])) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'keterangan_admin' => 'nullable|string|max:255',
        ]);

        $perizinan = Perizinan::findOrFail($id);
        $oldStatus = $perizinan->status;
        $newStatus = $request->status;

        $perizinan->update([
            'status' => $newStatus,
            'approved_by' => Auth::id(),
            'keterangan_admin' => $request->keterangan_admin,
        ]);

        // Jika disetujui dan memotong kuota, kurangi sisa_cuti pegawai
        // via method deductLeave() agar validasi & Audit Trail berjalan dengan benar
        if ($oldStatus === 'menunggu' && $newStatus === 'disetujui' && $perizinan->potong_kuota) {
            $pegawai = $perizinan->pegawai;
            if ($pegawai) {
                $pegawai->deductLeave($perizinan->total_hari);
            }
        }

        // Notify Pegawai
        if ($perizinan->pegawai && $perizinan->pegawai->user) {
            $perizinan->pegawai->user->notify(new StatusIzinDiperbarui($perizinan));
        }

        $msg = $request->status == 'disetujui' ? 'Pengajuan disetujui.' : 'Pengajuan ditolak.';
        
        return redirect()->back()->with('success', $msg);
    }
}

