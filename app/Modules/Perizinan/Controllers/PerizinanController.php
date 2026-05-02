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
    private function getSisaCuti($pegawaiId): int
    {
        $currentYear = date('Y');
        
        $cutiTerpakai = Perizinan::where('user_id', $pegawaiId)
            ->where('jenis_izin', 'cuti')
            ->whereIn('status', ['disetujui', 'menunggu'])
            ->whereYear('tanggal_mulai', $currentYear)
            ->get()
            ->sum(function($izin) {
                return Carbon::parse($izin->tanggal_mulai)->diffInDays(Carbon::parse($izin->tanggal_selesai)) + 1;
            });

        return max(0, 12 - $cutiTerpakai);
    }

    /**
     * Display a listing of personal permissions or all permissions for admin
     */
    public function index(): View
    {
        $user = Auth::user();
        $search = request('search');
        
        $query = Perizinan::with('pegawai');
        
        if ($user->hasRole('SUPER_ADMIN') || $user->hasRole('STAF_TU')) {
            $isAdmin = true;
            $sisaCuti = null;
        } else {
            $pegawai = $user->pegawai;
            if (!$pegawai) {
                 return view('perizinan::error', ['message' => 'Data pegawai tidak ditemukan.']);
            }
            $query->where('user_id', $pegawai->id);
            $isAdmin = false;
            $sisaCuti = $this->getSisaCuti($pegawai->id);
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
        $pegawai = Auth::user()->pegawai;
        $sisaCuti = $pegawai ? $this->getSisaCuti($pegawai->id) : 0;

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

        if ($request->jenis_izin == 'cuti') {
            $sisaCuti = $this->getSisaCuti($pegawai->id);
            $durasiCuti = Carbon::parse($request->tanggal_mulai)->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;
            
            if ($durasiCuti > $sisaCuti) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Pengajuan gagal. Sisa jatah cuti Anda tahun ini adalah {$sisaCuti} hari, namun Anda mengajukan {$durasiCuti} hari.");
            }
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
        $perizinan->update([
            'status' => $request->status,
            'approved_by' => Auth::id(),
            'keterangan_admin' => $request->keterangan_admin,
        ]);

        // Notify Pegawai
        if ($perizinan->pegawai && $perizinan->pegawai->user) {
            $perizinan->pegawai->user->notify(new StatusIzinDiperbarui($perizinan));
        }

        $msg = $request->status == 'disetujui' ? 'Pengajuan disetujui.' : 'Pengajuan ditolak.';
        
        return redirect()->back()->with('success', $msg);
    }
}
