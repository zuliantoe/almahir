<?php

namespace Modules\Perizinan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Perizinan\Models\Perizinan;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PerizinanController extends Controller
{
    /**
     * Display a listing of personal permissions or all permissions for admin
     */
    public function index(): View
    {
        $user = Auth::user();
        
        if ($user->hasRole('SUPER_ADMIN') || $user->hasRole('STAF_TU')) {
            $perizinan = Perizinan::with('pegawai')->latest()->paginate(10);
            $isAdmin = true;
        } else {
            $pegawai = $user->pegawai;
            if (!$pegawai) {
                 return view('perizinan::error', ['message' => 'Data pegawai tidak ditemukan.']);
            }
            $perizinan = Perizinan::where('user_id', $pegawai->id)->latest()->paginate(10);
            $isAdmin = false;
        }

        return view('perizinan::index', [
            'title' => 'Daftar Perizinan',
            'perizinan' => $perizinan,
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * Show the form for creating a new request
     */
    public function create(): View
    {
        return view('perizinan::create', [
            'title' => 'Ajukan Perizinan',
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

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('perizinan', 'public');
        }

        Perizinan::create([
            'user_id' => $pegawai->id,
            'jenis_izin' => $request->jenis_izin,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'bukti' => $buktiPath,
            'status' => 'menunggu',
        ]);

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

        $msg = $request->status == 'disetujui' ? 'Pengajuan disetujui.' : 'Pengajuan ditolak.';
        
        return redirect()->back()->with('success', $msg);
    }
}
