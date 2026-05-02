<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni;
use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;
use Modules\Siswa\Models\Siswa;

class PenghuniController extends BaseController
{
    /**
     * Display a listing of penghuni.
     */
    public function index(Request $request): View
    {
        $query = KamarPenghuni::with(['kamar', 'siswa']);

        if ($request->filled('kamar_id')) {
            $query->where('kamar_id', $request->kamar_id);
        }

        $penghuni = $query->latest()
                        ->paginate(15)
                        ->withQueryString();
        
        return view('manajemenasetdanasrama::penghuni.index', [
            'title'    => 'Data Penghuni Kamar',
            'penghuni' => $penghuni,
            'kamar'    => Kamar::all(),
        ]);
    }

    /**
     * Show the form for creating a new penghuni.
     */
    public function create(): View
    {
        $kamar = Kamar::all();
        $siswa = Siswa::all();
        
        return view('manajemenasetdanasrama::penghuni.create', [
            'title' => 'Tambah Penghuni Kamar',
            'kamar' => $kamar,
            'siswa' => $siswa,
        ]);
    }

    /**
     * Store a newly created penghuni in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kamar_id'       => 'required|exists:kamar,id',
            'siswa_id'       => 'required|exists:siswa,id',
            'tanggal_masuk'  => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'keterangan'     => 'nullable|string',
        ]);

        // CEK 1: Apakah santri ini sedang aktif di kamar lain?
        $isAktifDiKamarLain = KamarPenghuni::where('siswa_id', $request->siswa_id)
            ->where(function ($query) {
                $query->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>', now());
            })
            ->exists();

        if ($isAktifDiKamarLain) {
            return redirect()->back()->withInput()->with('error', 'Siswa ini masih terdaftar aktif di kamar lain. Silakan checkout (isi tanggal keluar) dari kamar sebelumnya terlebih dahulu.');
        }

        // CEK 2: Apakah kamar masih ada sisa kapasitas?
        $kamar = Kamar::findOrFail($request->kamar_id);
        if ($kamar->sisa_kapasitas <= 0) {
            return redirect()->back()->withInput()->with('error', 'Kapasitas kamar ini sudah penuh.');
        }

        KamarPenghuni::create($validated);

        // Regenerate jadwal piket kamar masa depan (Auto-Update)
        $piketService = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
        $piketService->regenerateFutureJadwal($request->kamar_id);

        return redirect()->route('manajemenasetdanasrama.penghuni.index')
            ->with('success', 'Penghuni kamar berhasil ditambahkan dan jadwal piket diperbarui otomatis.');
    }

    /**
     * Show the form for editing the specified penghuni.
     */
    public function edit(string $id): View
    {
        $penghuni = KamarPenghuni::findOrFail($id);
        $kamar = Kamar::all();
        $siswa = Siswa::all();
        
        return view('manajemenasetdanasrama::penghuni.edit', [
            'title'    => 'Edit Penghuni Kamar',
            'penghuni' => $penghuni,
            'kamar'    => $kamar,
            'siswa'    => $siswa,
        ]);
    }

    /**
     * Update the specified penghuni in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $penghuni = KamarPenghuni::findOrFail($id);
        
        $validated = $request->validate([
            'kamar_id'       => 'required|exists:kamar,id',
            'siswa_id'       => 'required|exists:siswa,id',
            'tanggal_masuk'  => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'keterangan'     => 'nullable|string',
        ]);

        // Jika kamar berubah, lakukan validasi ganda
        if ($penghuni->kamar_id != $request->kamar_id || $penghuni->siswa_id != $request->siswa_id) {
             // CEK 1: Apakah santri aktif di kamar lain (selain record ini)?
             $isAktifDiKamarLain = KamarPenghuni::where('siswa_id', $request->siswa_id)
             ->where('id', '!=', $id)
             ->where(function ($query) {
                 $query->whereNull('tanggal_keluar')
                       ->orWhere('tanggal_keluar', '>', now());
             })
             ->exists();

            if ($isAktifDiKamarLain) {
                return redirect()->back()->withInput()->with('error', 'Siswa ini masih terdaftar aktif di kamar lain.');
            }

            // CEK 2: Kapasitas kamar baru
            $kamarBaru = Kamar::findOrFail($request->kamar_id);
            if ($kamarBaru->sisa_kapasitas <= 0) {
                return redirect()->back()->withInput()->with('error', 'Kapasitas kamar baru sudah penuh.');
            }
        }

        $oldKamarId = $penghuni->kamar_id;
        $penghuni->update($validated);

        // Regenerate jadwal piket (Auto-Update)
        $piketService = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
        $piketService->regenerateFutureJadwal($penghuni->kamar_id);
        if ($oldKamarId != $penghuni->kamar_id) {
            $piketService->regenerateFutureJadwal($oldKamarId);
        }

        return redirect()->route('manajemenasetdanasrama.penghuni.index')
            ->with('success', 'Data penghuni kamar berhasil diperbarui dan jadwal piket disesuaikan.');
    }

    /**
     * Remove the specified penghuni from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $penghuni = KamarPenghuni::findOrFail($id);
        $kamarId = $penghuni->kamar_id;
        $penghuni->delete();

        // Regenerate jadwal piket
        $piketService = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
        $piketService->regenerateFutureJadwal($kamarId);

        return redirect()->route('manajemenasetdanasrama.penghuni.index')
            ->with('success', 'Penghuni kamar berhasil dihapus dan jadwal piket disesuaikan.');
    }
}