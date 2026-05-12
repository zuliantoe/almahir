<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;

class KamarController extends BaseController
{
    /**
     * Display a listing of kamar.
     */
    public function index(Request $request): View
    {
        $kamar = Kamar::with(['penghuniAktif.siswa'])->paginate(15);
        
        return view('manajemenasetdanasrama::kamar.index', [
            'title' => 'Data Kamar',
            'kamar' => $kamar,
        ]);
    }

    /**
     * Show the form for creating a new kamar.
     */
    public function create()
    {
        return redirect()->route('manajemenasetdanasrama.kamar.index');
    }

    /**
     * Show the details of the specified kamar.
     */
    public function show(string $id): View
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

        // Ambil riwayat penghuni sebelumnya (Isolasi Mutlak Berdasarkan Detik Eksekusi Terakhir)
        $latestHistory = $kamar->penghuni()
            ->whereNotNull('tanggal_keluar')
            ->where('tanggal_keluar', '<=', now())
            ->orderBy('updated_at', 'desc')
            ->first();

        $riwayatPenghuni = collect();
        if ($latestHistory) {
            // Tarik rombongan yang dieksekusi keluar pada JAM, MENIT, dan DETIK yang sama persis
            $riwayatPenghuni = $kamar->penghuni()
                ->with('siswa')
                ->where('updated_at', $latestHistory->updated_at)
                ->get();
        }

        return view('manajemenasetdanasrama::kamar.show', [
            'title'           => 'Detail Kamar: ' . $kamar->nama_kamar,
            'kamar'           => $kamar,
            'penghuniAktif'   => $penghuniAktif,
            'riwayatPenghuni' => $riwayatPenghuni,
        ]);
    }

    /**
     * Store a newly created kamar in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:255|unique:kamar,nama_kamar',
            'kapasitas'  => 'required|integer|min:1',
            'deskripsi'  => 'nullable|string',
        ]);

        $kamar = Kamar::create($validated);
        return redirect()->route('manajemenasetdanasrama.penghuni.assign-multiple', $kamar->id)
            ->with('success', 'Kamar berhasil dibuat. Silakan pilih penghuni untuk kamar ini.');
    }

    /**
     * Show the form for editing the specified kamar.
     */
    public function edit(string $id)
    {
        return redirect()->route('manajemenasetdanasrama.kamar.index');
    }

    /**
     * Update the specified kamar in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $kamar = Kamar::findOrFail($id);
        
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:255|unique:kamar,nama_kamar,' . $id,
            'kapasitas'  => 'required|integer|min:1',
            'deskripsi'  => 'nullable|string',
        ]);

        $kamar->update($validated);

        return redirect()->route('manajemenasetdanasrama.kamar.index')
            ->with('success', 'Kamar berhasil diperbarui.');
    }

    /**
     * Remove the specified kamar from storage.
     */
    public function destroy(string $id): RedirectResponse
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

    /**
     * Print the list of residents in the specified kamar.
     */
    public function print(Request $request, string $id): View
    {
        $kamar = Kamar::findOrFail($id);
        
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

        return view('manajemenasetdanasrama::kamar.print', [
            'title'         => 'Cetak Daftar Penghuni Kamar',
            'kamar'         => $kamar,
            'penghuniAktif' => $penghuniAktif,
            'musyrif'       => $request->query('musyrif'),
            'kepsek'        => $request->query('kepsek'),
        ]);
    }

    /**
     * Empty all residents in the specified kamar.
     */
    public function emptyAll(string $id): RedirectResponse
    {
        $kamar = Kamar::findOrFail($id);
        
        // Dapatkan semua penghuni yang masih aktif
        $activePenghuni = $kamar->penghuni()
            ->where(function($query) {
                $query->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>', now());
            })->get();

        $now = now();
        foreach ($activePenghuni as $penghuni) {
            $penghuni->update([
                'tanggal_keluar' => $now,
                'keterangan'     => "Keluaran dari {$kamar->nama_kamar}",
                'updated_at'     => $now
            ]);
        }

        if ($activePenghuni->count() > 0) {
            // Regenerate jadwal piket karena kamar kosong
            /** @var \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService $piketService */
            $piketService = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
            $piketService->regenerateFutureJadwal($id);
            
            return redirect()->route('manajemenasetdanasrama.kamar.show', $id)
                ->with('success', "Berhasil mengosongkan {$activePenghuni->count()} santri dari kamar {$kamar->nama_kamar}.");
        }

        return redirect()->route('manajemenasetdanasrama.kamar.show', $id)
            ->with('info', "Kamar sudah dalam keadaan kosong.");
    }
}