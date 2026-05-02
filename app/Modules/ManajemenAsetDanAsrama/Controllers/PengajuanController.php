<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use App\Modules\ManajemenAsetDanAsrama\Traits\HasNomorOtomatis;
use App\Modules\ManajemenAsetDanAsrama\Traits\HasSoftDeleteWithUser;

class PengajuanController extends BaseController
{
    use HasNomorOtomatis, HasSoftDeleteWithUser;

    /**
     * Display a listing of pengajuan aset.
     */
    public function index(Request $request): View
    {
        // Hanya tampilkan pengajuan yang masih di tahap pengajuan atau ditolak
        $pengajuan = PengajuanAset::with(['pengaju:id,nama', 'approver:id,nama'])
                        ->whereIn('status', ['diajukan', 'ditolak'])
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
    public function create()
    {
        return redirect()->route('manajemenasetdanasrama.pengajuan.index');
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

        $data = $validated;
        $data['nomor_pengajuan'] = $this->generateNomor(PengajuanAset::class, 'PJ');
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
        $pengajuan = PengajuanAset::findOrFail($id);
        
        $this->performSoftDelete($request, $pengajuan);

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