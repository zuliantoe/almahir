<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;

class PengajuanController extends BaseController
{
    /**
     * Display a listing of pengajuan aset.
     */
    public function index(Request $request): View
    {
        $pengajuan = PengajuanAset::with(['pengaju', 'approver'])
                        ->latest()
                        ->paginate(15);
        
        return view('manajemenasetdanasrama::pengajuan.index', [
            'title'     => 'Data Pengajuan Aset',
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Show the form for creating a new pengajuan aset.
     */
    public function create(): View
    {
        return view('manajemenasetdanasrama::pengajuan.create', [
            'title' => 'Tambah Pengajuan Aset',
        ]);
    }

    /**
     * Store a newly created pengajuan aset in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_aset'            => 'required|string|max:255',
            'deskripsi_pengajuan'  => 'required|string',
            'estimasi_harga'       => 'required|numeric|min:0',
            'tanggal_pengajuan'    => 'required|date',
        ]);

        // Generate nomor pengajuan: PJ-YYYYMM-XXXX
        $yearMonth = date('Ym');
        $lastPengajuan = PengajuanAset::whereYear('created_at', date('Y'))
                            ->whereMonth('created_at', date('m'))
                            ->count();
        $nomorUrut = str_pad($lastPengajuan + 1, 4, '0', STR_PAD_LEFT);
        $nomorPengajuan = "PJ-{$yearMonth}-{$nomorUrut}";

        $data = $validated;
        $data['nomor_pengajuan'] = $nomorPengajuan;
        $data['pengaju_id'] = auth()->id();
        $data['status'] = 'diajukan';

        PengajuanAset::create($data);

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', 'Pengajuan aset berhasil ditambahkan.');
    }

    /**
     * Display the specified pengajuan aset.
     */
    public function show(Request $request, string $id)
    {
        $pengajuan = PengajuanAset::with(['pengaju', 'approver', 'pengadaan'])
                        ->findOrFail($id);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($pengajuan);
        }
        
        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('error', 'Halaman detail tidak tersedia. Gunakan tombol lihat di tabel.');
    }

    /**
     * Show the form for editing the specified pengajuan aset.
     */
    public function edit(string $id): View
    {
        $pengajuan = PengajuanAset::findOrFail($id);
        
        // Hanya bisa edit jika status diajukan atau ditolak
        abort_if(!in_array($pengajuan->status, ['diajukan', 'ditolak']), 403);
        
        return view('manajemenasetdanasrama::pengajuan.edit', [
            'title'     => 'Edit Pengajuan Aset',
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Update the specified pengajuan aset in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $pengajuan = PengajuanAset::findOrFail($id);
        
        // Hanya bisa update jika status diajukan atau ditolak
        abort_if(!in_array($pengajuan->status, ['diajukan', 'ditolak']), 403);
        
        $validated = $request->validate([
            'nama_aset'            => 'required|string|max:255',
            'deskripsi_pengajuan'  => 'required|string',
            'estimasi_harga'       => 'required|numeric|min:0',
        ]);

        $pengajuan->update($validated);

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', 'Pengajuan aset berhasil diperbarui.');
    }

    /**
     * Remove the specified pengajuan aset from storage (soft delete).
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'alasan_hapus' => 'required|string'
        ]);

        $pengajuan = PengajuanAset::findOrFail($id);
        $pengajuan->deleted_by = auth()->id();
        $pengajuan->alasan_hapus = $request->alasan_hapus;
        $pengajuan->save();
        $pengajuan->delete();

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', 'Pengajuan aset berhasil dihapus.');
    }

    /**
     * Resubmit a rejected pengajuan aset.
     */
    public function ajukanUlang(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'deskripsi_pengajuan'   => 'required|string',
            'alasan_pengajuan_ulang' => 'required|string',
        ]);

        $pengajuan = PengajuanAset::findOrFail($id);

        // Pastikan statusnya ditolak
        abort_if($pengajuan->status !== 'ditolak', 403, 'Hanya pengajuan dengan status ditolak yang dapat diajukan ulang.');

        // Update data
        $pengajuan->status = 'diajukan';
        $pengajuan->deskripsi_pengajuan = $validated['deskripsi_pengajuan'];
        $pengajuan->alasan_pengajuan_ulang = $validated['alasan_pengajuan_ulang'];
        $pengajuan->catatan_tolak = null; // kosongkan catatan tolak
        $pengajuan->approved_by = null;
        $pengajuan->approved_at = null;
        $pengajuan->save();

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', 'Pengajuan berhasil diajukan ulang.');
    }
}