<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;

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
        
        return view('manajemenasetdanasrama::persetujuan.index', [
            'title'     => 'Persetujuan Pengajuan Aset',
            'pengajuan' => $pengajuan,
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

        return redirect()->route('manajemenasetdanasrama.persetujuan.index')
            ->with('success', 'Pengajuan aset disetujui.');
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
}