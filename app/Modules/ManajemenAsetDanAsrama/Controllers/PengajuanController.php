<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use App\Modules\ManajemenAsetDanAsrama\Traits\HasNomorOtomatis;
use App\Modules\ManajemenAsetDanAsrama\Traits\HasSoftDeleteWithUser;

use App\Modules\ManajemenAsetDanAsrama\Traits\HasAssetCode;

class PengajuanController extends BaseController
{
    use HasNomorOtomatis, HasSoftDeleteWithUser, HasAssetCode;

    /**
     * Display a listing of pengajuan aset.
     */
    public function index(Request $request): View
    {
        // Hanya tampilkan pengajuan yang masih di tahap pengajuan atau ditolak
        $pengajuan = PengajuanAset::with(['pengaju:id,name', 'approver:id,name'])
                        ->whereIn('status', ['diajukan', 'ditolak'])
                        ->latest()
                        ->paginate(15);
        
        $stats = [
            'total'     => PengajuanAset::count(),
            'diajukan'  => PengajuanAset::where('status', 'diajukan')->count(),
            'disetujui' => PengajuanAset::where('status', 'disetujui')->count(),
            'ditolak'   => PengajuanAset::where('status', 'ditolak')->count(),
        ];
        
        return view('manajemenasetdanasrama::pengajuan.index', [
            'title'     => 'Data Pengajuan Aset',
            'pengajuan' => $pengajuan,
            'stats'     => $stats,
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
        $request->validate([
            'nama_aset'            => 'required|string|max:255',
            'deskripsi_pengajuan'  => 'required|string',
            'estimasi_harga'       => 'required|numeric|min:0',
            'tanggal_pengajuan'    => 'required|date',
            'jumlah'               => 'required|integer|min:1|max:100',
        ]);

        $jumlah = (int) $request->jumlah;
        $namaAset = ucwords(strtolower($request->nama_aset));

        for ($i = 0; $i < $jumlah; $i++) {
            $data = [
                'nama_aset'           => $namaAset,
                'deskripsi_pengajuan' => $request->deskripsi_pengajuan,
                'estimasi_harga'      => $request->estimasi_harga,
                'tanggal_pengajuan'   => $request->tanggal_pengajuan,
                'pengaju_id'          => auth()->id(),
                'status'              => 'diajukan',
                // Pake generateAssetCode biar kodenya MJ, KRS, dll (Cek ke tabel pengajuan)
                'nomor_pengajuan'     => $this->generateAssetCode($namaAset, PengajuanAset::class, 'nomor_pengajuan')
            ];

            PengajuanAset::create($data);
        }

        $msg = $jumlah > 1 
            ? "Berhasil mengirim {$jumlah} pengajuan aset dengan kode berurutan." 
            : "Pengajuan aset berhasil diajukan.";

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', $msg);
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
     * Bulk destroy submissions based on pattern.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'pattern' => 'required|string|min:2',
        ]);

        $pattern = strtoupper($request->pattern);
        
        // Cari pengajuan berdasarkan nomor_pengajuan (yang sekarang isinya kode aset)
        $query = PengajuanAset::where('nomor_pengajuan', 'LIKE', "{$pattern}%");
        $count = $query->count();

        if ($count === 0) {
            return redirect()->back()->with('error', "Tidak ditemukan pengajuan dengan pola kode '{$pattern}'.");
        }

        $query->delete();

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', "Berhasil menghapus {$count} pengajuan dengan pola kode '{$pattern}'.");
    }

    /**
     * Resubmit a rejected submission.
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

    /**
     * Duplicate a pengajuan multiple times.
     */
    public function duplicate(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1|max:100'
        ]);

        $original = PengajuanAset::findOrFail($id);
        $jumlah = (int) $request->jumlah;

        for ($i = 0; $i < $jumlah; $i++) {
            $new = $original->replicate();
            
            // Ambil tanggal dari kode asli (parts[1]) biar kodenya tetep "sama"
            $parts = explode('-', $original->nomor_pengajuan);
            $originalDate = isset($parts[1]) ? $parts[1] : null;
            
            $new->nomor_pengajuan = $this->generateAssetCode($original->nama_aset, PengajuanAset::class, 'nomor_pengajuan', $originalDate);
            $new->status = 'diajukan';
            $new->save();
        }

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', "Berhasil menduplikat pengajuan sebanyak {$jumlah} kali.");
    }
}