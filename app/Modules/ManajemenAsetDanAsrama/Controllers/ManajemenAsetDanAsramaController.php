<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;  // Gunakan Modules\... (bukan App\Modules\...)

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\Aset;           // Gunakan Modules\... untuk model
use Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use Modules\ManajemenAsetDanAsrama\Models\PengadaanAset;
use Modules\ManajemenAsetDanAsrama\Models\Kerusakan;
use Modules\ManajemenAsetDanAsrama\Models\Pemeliharaan;
use Modules\ManajemenAsetDanAsrama\Models\Kamar;
use Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni;
use Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;
use Modules\Siswa\Models\Siswa;  // Gunakan Modules\Siswa...
use App\Models\User;

/**
 * ManajemenAsetDanAsramaController
 * 
 * CRUD operations for ManajemenAsetDanAsrama module.
 */
class ManajemenAsetDanAsramaController extends Controller
{
    /**
     * Display dashboard / index page.
     */
    public function index(Request $request): View
    {
        $totalAset = Aset::count();
        $totalPengajuan = PengajuanAset::count();
        $totalPengadaan = PengadaanAset::count();
        $totalKerusakan = Kerusakan::count();
        $totalPemeliharaan = Pemeliharaan::count();
        $totalKamar = Kamar::count();
        $totalPenghuni = KamarPenghuni::count();
        
        $pengajuanTerbaru = PengajuanAset::with('pengaju')
                                ->latest()
                                ->take(5)
                                ->get();
        
        $asetTerbaru = Aset::with('pengadaan')
                            ->latest()
                            ->take(5)
                            ->get();
        
        $jadwalPiketHariIni = JadwalPiket::with('siswa')
                                ->where('hari', $this->getHariIndo(date('l')))
                                ->where('status', 'belum')
                                ->take(5)
                                ->get();

        $asetByStatus = [
            'baik' => Aset::where('status_kondisi', 'baik')->count(),
            'rusak' => Aset::where('status_kondisi', 'rusak')->count(),
            'dalam_perbaikan' => Aset::where('status_kondisi', 'dalam_perbaikan')->count(),
            'sudah_diperbaiki' => Aset::where('status_kondisi', 'sudah_diperbaiki')->count(),
        ];
        
        return view('manajemenasetdanasrama::index', [
            'title' => 'Dashboard Manajemen Aset & Asrama',
            'totalAset' => $totalAset,
            'totalPengajuan' => $totalPengajuan,
            'totalPengadaan' => $totalPengadaan,
            'totalKerusakan' => $totalKerusakan,
            'totalPemeliharaan' => $totalPemeliharaan,
            'totalKamar' => $totalKamar,
            'totalPenghuni' => $totalPenghuni,
            'pengajuanTerbaru' => $pengajuanTerbaru,
            'asetTerbaru' => $asetTerbaru,
            'jadwalPiketHariIni' => $jadwalPiketHariIni,
            'asetByStatus' => $asetByStatus,
        ]);
    }

    // =========================================================================
    // PENGAJUAN ASET
    // =========================================================================

    /**
     * Display a listing of pengajuan aset.
     */
    public function pengajuanIndex(Request $request): View
    {
        $pengajuan = PengajuanAset::with(['pengaju', 'approver'])
                        ->latest()
                        ->paginate(15);
        
        return view('manajemenasetdanasrama::pengajuan.index', [
            'title' => 'Data Pengajuan Aset',
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Show the form for creating a new pengajuan aset.
     */
    public function pengajuanCreate(): View
    {
        return view('manajemenasetdanasrama::pengajuan.create', [
            'title' => 'Tambah Pengajuan Aset',
        ]);
    }

    /**
     * Store a newly created pengajuan aset in storage.
     */
    public function pengajuanStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'deskripsi_pengajuan' => 'required|string',
            'estimasi_harga' => 'required|numeric|min:0',
            'tanggal_pengajuan' => 'required|date',
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
        $data['pengaju_id'] = auth()->id;
        $data['status'] = 'diajukan';

        PengajuanAset::create($data);

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', 'Pengajuan aset berhasil ditambahkan.');
    }

    /**
     * Display the specified pengajuan aset.
     */
    public function pengajuanShow(string $id): View
    {
        $pengajuan = PengajuanAset::with(['pengaju', 'approver', 'pengadaan'])
                        ->findOrFail($id);
        
        return view('manajemenasetdanasrama::pengajuan.show', [
            'title' => 'Detail Pengajuan Aset',
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Show the form for editing the specified pengajuan aset.
     */
    public function pengajuanEdit(string $id): View
    {
        $pengajuan = PengajuanAset::findOrFail($id);
        
        // Hanya bisa edit jika status diajukan atau ditolak
        abort_if(!in_array($pengajuan->status, ['diajukan', 'ditolak']), 403);
        
        return view('manajemenasetdanasrama::pengajuan.edit', [
            'title' => 'Edit Pengajuan Aset',
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Update the specified pengajuan aset in storage.
     */
    public function pengajuanUpdate(Request $request, string $id): RedirectResponse
    {
        $pengajuan = PengajuanAset::findOrFail($id);
        
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'deskripsi_pengajuan' => 'required|string',
            'estimasi_harga' => 'required|numeric|min:0',
        ]);

        $pengajuan->update($validated);

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', 'Pengajuan aset berhasil diperbarui.');
    }

    /**
     * Remove the specified pengajuan aset from storage (soft delete).
     */
    public function pengajuanDestroy(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'alasan_hapus' => 'required|string'
        ]);

        $pengajuan = PengajuanAset::findOrFail($id);
        $pengajuan->deleted_by = auth()->id;
        $pengajuan->alasan_hapus = $request->alasan_hapus;
        $pengajuan->save();
        $pengajuan->delete();

        return redirect()->route('manajemenasetdanasrama.pengajuan.index')
            ->with('success', 'Pengajuan aset berhasil dihapus.');
    }

    // =========================================================================
    // PERSETUJUAN ASET
    // =========================================================================

    /**
     * Display a listing of pengajuan aset waiting for approval.
     */
    public function persetujuanIndex(Request $request): View
    {
        $pengajuan = PengajuanAset::with('pengaju')
                        ->where('status', 'diajukan')
                        ->latest()
                        ->paginate(15);
        
        return view('manajemenasetdanasrama::persetujuan.index', [
            'title' => 'Persetujuan Pengajuan Aset',
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Approve the specified pengajuan aset.
     */
    public function persetujuanApprove(string $id): RedirectResponse
    {
        $pengajuan = PengajuanAset::findOrFail($id);
        
        $pengajuan->status = 'disetujui';
        $pengajuan->approved_by = auth()->id;
        $pengajuan->approved_at = now();
        $pengajuan->save();

        return redirect()->route('manajemenasetdanasrama.persetujuan.index')
            ->with('success', 'Pengajuan aset disetujui.');
    }

    /**
     * Reject the specified pengajuan aset.
     */
    public function persetujuanReject(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'catatan_tolak' => 'required|string'
        ]);

        $pengajuan = PengajuanAset::findOrFail($id);
        
        $pengajuan->status = 'ditolak';
        $pengajuan->catatan_tolak = $request->catatan_tolak;
        $pengajuan->approved_by = auth()->id;
        $pengajuan->approved_at = now();
        $pengajuan->save();

        return redirect()->route('manajemenasetdanasrama.persetujuan.index')
            ->with('success', 'Pengajuan aset ditolak.');
    }

    // =========================================================================
    // PENGADAAN ASET
    // =========================================================================

    /**
     * Display a listing of pengadaan aset.
     */
    public function pengadaanIndex(Request $request): View
    {
        $pengadaan = PengadaanAset::with('pengajuan')
                        ->latest()
                        ->paginate(15);
        
        return view('manajemenasetdanasrama::pengadaan.index', [
            'title' => 'Data Pengadaan Aset',
            'pengadaan' => $pengadaan,
        ]);
    }

    /**
     * Show the form for processing pengadaan aset.
     */
    public function pengadaanProses(string $id): View
    {
        $pengajuan = PengajuanAset::with('pengaju')->findOrFail($id);
        
        return view('manajemenasetdanasrama::pengadaan.proses', [
            'title' => 'Proses Pengadaan Aset',
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Store a newly created pengadaan aset in storage.
     */
    public function pengadaanStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan_aset,id',
            'vendor' => 'required|string|max:255',
            'tanggal_pesan' => 'required|date',
            'estimasi_datang' => 'required|date|after:tanggal_pesan',
            'biaya_riil' => 'required|numeric|min:0',
            'catatan_pengadaan' => 'nullable|string',
        ]);

        // Generate nomor PO: PO-YYYYMM-XXXX
        $yearMonth = date('Ym');
        $lastPengadaan = PengadaanAset::whereYear('created_at', date('Y'))
                            ->whereMonth('created_at', date('m'))
                            ->count();
        $nomorUrut = str_pad($lastPengadaan + 1, 4, '0', STR_PAD_LEFT);
        $nomorPO = "PO-{$yearMonth}-{$nomorUrut}";

        $data = $validated;
        $data['nomor_po'] = $nomorPO;
        $data['status'] = 'dipesan';

        PengadaanAset::create($data);

        // Update status pengajuan
        $pengajuan = PengajuanAset::find($request->pengajuan_id);
        $pengajuan->status = 'proses_pengadaan';
        $pengajuan->save();

        return redirect()->route('manajemenasetdanasrama.pengadaan.index')
            ->with('success', 'Data pengadaan berhasil disimpan.');
    }

    /**
     * Mark pengadaan as completed and create aset.
     */
    public function pengadaanSelesai(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_datang' => 'required|date',
            'kode_aset' => 'required|string|unique:aset,kode_aset',
            'nama_aset' => 'required|string|max:255',
            'kondisi' => 'nullable|string',
            'deskripsi_aset' => 'nullable|string',
        ]);

        $pengadaan = PengadaanAset::with('pengajuan')->findOrFail($id);
        
        // Update status pengadaan
        $pengadaan->tanggal_datang = $request->tanggal_datang;
        $pengadaan->status = 'datang';
        $pengadaan->save();

        // Buat aset baru
        Aset::create([
            'kode_aset' => $request->kode_aset,
            'nama_aset' => $request->nama_aset,
            'tanggal_pengajuan' => $pengadaan->pengajuan->tanggal_pengajuan,
            'harga' => $pengadaan->biaya_riil,
            'status_kondisi' => 'baik',
            'tanggal_pengadaan' => $request->tanggal_datang,
            'kondisi' => $request->kondisi,
            'deskripsi_aset' => $request->deskripsi_aset,
            'pengadaan_id' => $pengadaan->id,
        ]);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Aset berhasil ditambahkan ke master aset.');
    }

    // =========================================================================
    // MASTER ASET
    // =========================================================================

    /**
     * Display a listing of master aset.
     */
    public function asetIndex(Request $request): View
    {
        $aset = Aset::with('pengadaan.pengajuan')
                    ->whereNull('deleted_at')
                    ->latest()
                    ->paginate(15);
        
        return view('manajemenasetdanasrama::aset.index', [
            'title' => 'Master Aset',
            'aset' => $aset,
        ]);
    }

    /**
     * Display the specified aset.
     */
    public function asetShow(string $id): View
    {
        $aset = Aset::with(['pengadaan.pengajuan', 'kerusakan', 'pemeliharaan'])
                    ->findOrFail($id);
        
        return view('manajemenasetdanasrama::aset.show', [
            'title' => 'Detail Aset',
            'aset' => $aset,
        ]);
    }

    /**
     * Show the form for editing the specified aset.
     */
    public function asetEdit(string $id): View
    {
        $aset = Aset::findOrFail($id);
        
        return view('manajemenasetdanasrama::aset.edit', [
            'title' => 'Edit Aset',
            'aset' => $aset,
        ]);
    }

    /**
     * Update the specified aset in storage.
     */
    public function asetUpdate(Request $request, string $id): RedirectResponse
    {
        $aset = Aset::findOrFail($id);
        
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'status_kondisi' => 'required|in:baik,rusak,dalam_perbaikan,sudah_diperbaiki',
            'kondisi' => 'nullable|string',
            'deskripsi_aset' => 'nullable|string',
        ]);

        $aset->update($validated);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Data aset berhasil diperbarui.');
    }

    /**
     * Remove the specified aset from storage (soft delete).
     */
    public function asetDestroy(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'alasan_hapus' => 'required|string'
        ]);

        $aset = Aset::findOrFail($id);
        $aset->deleted_by = auth()->id;
        $aset->alasan_hapus = $request->alasan_hapus;
        $aset->save();
        $aset->delete();

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Aset berhasil dipindahkan ke trash.');
    }

    // =========================================================================
    // KAMAR
    // =========================================================================

    /**
     * Display a listing of kamar.
     */
    public function kamarIndex(Request $request): View
    {
        $kamar = Kamar::withCount('penghuni')->paginate(15);
        
        return view('manajemenasetdanasrama::kamar.index', [
            'title' => 'Data Kamar',
            'kamar' => $kamar,
        ]);
    }

    /**
     * Show the form for creating a new kamar.
     */
    public function kamarCreate(): View
    {
        return view('manajemenasetdanasrama::kamar.create', [
            'title' => 'Tambah Kamar',
        ]);
    }

    /**
     * Store a newly created kamar in storage.
     */
    public function kamarStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:255|unique:kamar,nama_kamar',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        Kamar::create($validated);

        return redirect()->route('manajemenasetdanasrama.kamar.index')
            ->with('success', 'Kamar berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified kamar.
     */
    public function kamarEdit(string $id): View
    {
        $kamar = Kamar::findOrFail($id);
        
        return view('manajemenasetdanasrama::kamar.edit', [
            'title' => 'Edit Kamar',
            'kamar' => $kamar,
        ]);
    }

    /**
     * Update the specified kamar in storage.
     */
    public function kamarUpdate(Request $request, string $id): RedirectResponse
    {
        $kamar = Kamar::findOrFail($id);
        
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:255|unique:kamar,nama_kamar,' . $id,
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        $kamar->update($validated);

        return redirect()->route('manajemenasetdanasrama.kamar.index')
            ->with('success', 'Kamar berhasil diperbarui.');
    }

    /**
     * Remove the specified kamar from storage.
     */
    public function kamarDestroy(string $id): RedirectResponse
    {
        $kamar = Kamar::findOrFail($id);
        
        // Cek apakah masih ada penghuni
        if ($kamar->penghuni()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kamar tidak bisa dihapus karena masih memiliki penghuni.');
        }

        $kamar->delete();

        return redirect()->route('manajemenasetdanasrama.kamar.index')
            ->with('success', 'Kamar berhasil dihapus.');
    }

    // =========================================================================
    // JADWAL PIKET
    // =========================================================================

    /**
     * Display a listing of jadwal piket.
     */
    public function jadwalPiketIndex(Request $request): View
    {
        $jadwal = JadwalPiket::with('siswa')
                    ->orderBy('bulan')
                    ->orderBy('pekan')
                    ->orderBy('hari')
                    ->paginate(15);
        
        return view('manajemenasetdanasrama::jadwal-piket.index', [
            'title' => 'Jadwal Piket',
            'jadwal' => $jadwal,
        ]);
    }

    /**
     * Show the form for creating a new jadwal piket.
     */
    public function jadwalPiketCreate(): View
    {
        $siswa = Siswa::all();
        
        return view('manajemenasetdanasrama::jadwal-piket.create', [
            'title' => 'Tambah Jadwal Piket',
            'siswa' => $siswa,
        ]);
    }

    /**
     * Store a newly created jadwal piket in storage.
     */
    public function jadwalPiketStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'pekan' => 'required|integer|between:1,5',
            'hari' => 'required|string',
            'tempat' => 'required|string|max:255',
            'siswa_id' => 'required|exists:siswa,id',
        ]);

        $validated['status'] = 'belum';

        JadwalPiket::create($validated);

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified jadwal piket.
     */
    public function jadwalPiketEdit(string $id): View
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $siswa = Siswa::all();
        
        return view('manajemenasetdanasrama::jadwal-piket.edit', [
            'title' => 'Edit Jadwal Piket',
            'jadwal' => $jadwal,
            'siswa' => $siswa,
        ]);
    }

    /**
     * Update the specified jadwal piket in storage.
     */
    public function jadwalPiketUpdate(Request $request, string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        
        $validated = $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'pekan' => 'required|integer|between:1,5',
            'hari' => 'required|string',
            'tempat' => 'required|string|max:255',
            'siswa_id' => 'required|exists:siswa,id',
        ]);

        $jadwal->update($validated);

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil diperbarui.');
    }

    /**
     * Remove the specified jadwal piket from storage.
     */
    public function jadwalPiketDestroy(string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil dihapus.');
    }

    /**
     * Mark jadwal piket as completed.
     */
    public function jadwalPiketSelesai(string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->status = 'sudah';
        $jadwal->save();

        return redirect()->back()
            ->with('success', 'Status piket diupdate menjadi selesai.');
    }

    // =========================================================================
    // TRASH (SOFT DELETE)
    // =========================================================================

    /**
     * Display a listing of trashed items.
     */
    public function trashIndex(Request $request): View
    {
        $pengajuanTrash = PengajuanAset::onlyTrashed()
                            ->with(['pengaju', 'deletedBy'])
                            ->latest('deleted_at')
                            ->get();
        
        $asetTrash = Aset::onlyTrashed()
                        ->with('deletedBy')
                        ->latest('deleted_at')
                        ->get();
        
        return view('manajemenasetdanasrama::trash.index', [
            'title' => 'Data Terhapus (Trash)',
            'pengajuanTrash' => $pengajuanTrash,
            'asetTrash' => $asetTrash,
        ]);
    }

    /**
     * Restore the specified trashed item.
     */
    public function trashRestore(string $type, string $id): RedirectResponse
    {
        if ($type === 'pengajuan') {
            $item = PengajuanAset::onlyTrashed()->findOrFail($id);
            $item->restore();
            $message = 'Pengajuan aset berhasil dipulihkan.';
        } elseif ($type === 'aset') {
            $item = Aset::onlyTrashed()->findOrFail($id);
            $item->restore();
            $message = 'Aset berhasil dipulihkan.';
        } else {
            abort(404);
        }

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', $message);
    }

    /**
     * Permanently delete the specified trashed item.
     */
    public function trashForceDelete(string $type, string $id): RedirectResponse
    {
        if ($type === 'pengajuan') {
            $item = PengajuanAset::onlyTrashed()->findOrFail($id);
            $item->forceDelete();
            $message = 'Pengajuan aset berhasil dihapus permanen.';
        } elseif ($type === 'aset') {
            $item = Aset::onlyTrashed()->findOrFail($id);
            $item->forceDelete();
            $message = 'Aset berhasil dihapus permanen.';
        } else {
            abort(404);
        }

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', $message);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Get Indonesian day name from English day.
     */
    private function getHariIndo(string $day): string
    {
        $hari = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        
        return $hari[$day] ?? $day;
    }
}