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
            'kamar_id'      => 'required|exists:kamar,id',
            'siswa_id'      => 'required|exists:siswa,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar'=> 'nullable|date|after:tanggal_masuk',
            'keterangan'    => 'nullable|string',
        ]);

        // Cek apakah siswa sudah aktif di kamar manapun
        $isAktif = KamarPenghuni::where('siswa_id', $validated['siswa_id'])
            ->where(function($query) {
                $query->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>', now());
            })->exists();

        if ($isAktif) {
            return back()->withInput()->with('error', 'Siswa ini masih terdaftar aktif di kamar. Silakan isi tanggal keluar pada data sebelumnya terlebih dahulu.');
        }

        // Cek kapasitas kamar
        $kamar = Kamar::findOrFail($validated['kamar_id']);
        if ($kamar->sisa <= 0) {
            return back()->withInput()->with('error', 'Kamar ini sudah penuh (Kapasitas: ' . $kamar->kapasitas . ').');
        }

        KamarPenghuni::create($validated);

        return redirect()->route('manajemenasetdanasrama.penghuni.index')
            ->with('success', 'Penghuni berhasil ditambahkan.');
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
            'kamar_id'      => 'required|exists:kamar,id',
            'siswa_id'      => 'required|exists:siswa,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar'=> 'nullable|date|after:tanggal_masuk',
            'keterangan'    => 'nullable|string',
        ]);

        // Jika mengubah siswa, pastikan siswa baru tidak aktif di tempat lain
        if ($penghuni->siswa_id !== $validated['siswa_id']) {
            $isAktif = KamarPenghuni::where('siswa_id', $validated['siswa_id'])
                ->where(function($query) {
                    $query->whereNull('tanggal_keluar')
                          ->orWhere('tanggal_keluar', '>', now());
                })->exists();

            if ($isAktif) {
                return back()->withInput()->with('error', 'Siswa yang dipilih masih terdaftar aktif di kamar lain.');
            }
        }

        // Jika mengubah kamar, pastikan kamar baru masih ada slot
        if ($penghuni->kamar_id !== $validated['kamar_id']) {
            $kamarBaru = Kamar::findOrFail($validated['kamar_id']);
            if ($kamarBaru->sisa <= 0) {
                return back()->withInput()->with('error', 'Kamar tujuan sudah penuh.');
            }
        }

        $penghuni->update($validated);

        return redirect()->route('manajemenasetdanasrama.penghuni.index')
            ->with('success', 'Penghuni berhasil diperbarui.');
    }

    /**
     * Remove the specified penghuni from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $penghuni = KamarPenghuni::findOrFail($id);
        $penghuni->delete();

        return redirect()->route('manajemenasetdanasrama.penghuni.index')
            ->with('success', 'Penghuni berhasil dihapus.');
    }
}