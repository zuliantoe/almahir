<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;

class PersetujuanController extends BaseController
{
    /**
     * Display a listing of pengajuan aset waiting for approval.
     */
    public function index(Request $request): View
    {
        $pengajuan = PengajuanAset::with('pengaju')
                        ->where('status', 'diajukan')
                        ->latest()
                        ->paginate(15);
        
        $stats = [
            'total_pending' => PengajuanAset::where('status', 'diajukan')->count(),
            'total_approved' => PengajuanAset::where('status', 'disetujui')->count(),
            'total_rejected' => PengajuanAset::where('status', 'ditolak')->count(),
            'estimasi_biaya' => PengajuanAset::where('status', 'diajukan')->sum('estimasi_harga'),
        ];
        
        return view('manajemenasetdanasrama::persetujuan.index', [
            'title'     => 'Persetujuan Pengajuan Aset',
            'pengajuan' => $pengajuan,
            'stats'     => $stats,
        ]);
    }

    /**
     * Approve the specified pengajuan aset.
     */
    public function approve(string $id): RedirectResponse
    {
        $pengajuan = PengajuanAset::findOrFail($id);

        // Pastikan status masih 'diajukan'
        if ($pengajuan->status !== 'diajukan') {
            return redirect()->route('manajemenasetdanasrama.persetujuan.index')
                ->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }
        
        $pengajuan->status = 'disetujui';
        $pengajuan->approved_by = auth()->id();
        $pengajuan->approved_at = now();
        $pengajuan->save();

        return redirect()->route('manajemenasetdanasrama.pengadaan.proses', $pengajuan->id)
            ->with('success', 'Pengajuan aset disetujui. Silakan proses pengadaan.');
    }

    /**
     * Reject the specified pengajuan aset.
     */
    public function reject(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'catatan_tolak' => 'required|string|max:500',
        ]);

        $pengajuan = PengajuanAset::findOrFail($id);

        // Pastikan status masih 'diajukan'
        if ($pengajuan->status !== 'diajukan') {
            return redirect()->route('manajemenasetdanasrama.persetujuan.index')
                ->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }
        
        $pengajuan->status = 'ditolak';
        $pengajuan->catatan_tolak = $request->catatan_tolak;
        $pengajuan->approved_by = auth()->id();
        $pengajuan->approved_at = now();
        $pengajuan->save();

        return redirect()->route('manajemenasetdanasrama.persetujuan.index')
            ->with('success', 'Pengajuan aset ditolak.');
    }

    /**
     * Bulk approve requests by name prefix.
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $request->validate([
            'prefix' => 'required|string|min:1',
        ]);

        $prefix = strtoupper($request->prefix);
        $pengajuan = PengajuanAset::where('status', 'diajukan')
                        ->where(function($q) use ($prefix) {
                            $q->where('nama_aset', 'LIKE', $prefix . '%')
                              ->orWhere('nomor_pengajuan', 'LIKE', $prefix . '%');
                        })
                        ->get();

        if ($pengajuan->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pengajuan yang ditemukan dengan inisial tersebut.');
        }

        $count = 0;
        foreach ($pengajuan as $item) {
            /** @var PengajuanAset $item */
            $item->status = 'disetujui';
            $item->approved_by = auth()->id();
            $item->approved_at = now();
            $item->save();
            $count++;
        }

        return redirect()->route('manajemenasetdanasrama.persetujuan.index')
            ->with('success', "$count pengajuan aset berhasil disetujui secara masal.");
    }
}