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
        // Hanya ambil penghuni yang berstatus AKTIF saat ini
        $query = KamarPenghuni::with(['kamar', 'siswa'])->aktif();

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
    public function create(Request $request): View
    {
        // Hanya tampilkan kamar yang masih punya sisa slot
        $kamar = Kamar::all()->filter(function($k) {
            return $k->sisa > 0;
        });
        
        // Ambil santri yang AKTIF dan BELUM punya kamar (atau sudah keluar dari kamar lama)
        $siswa = Siswa::aktif()
            ->whereDoesntHave('kamarPenghuni', function($q) {
                $q->where(function($query) {
                    $query->whereNull('tanggal_keluar')
                          ->orWhere('tanggal_keluar', '>', now());
                });
            })
            ->orderBy('nama')
            ->get();
            
        $selectedKamarId = $request->query('kamar_id');
        
        // Cek apakah kamar ini sudah punya ketua
        $hasKetua = false;
        if ($selectedKamarId) {
            $hasKetua = KamarPenghuni::where('kamar_id', $selectedKamarId)
                ->where('jabatan', 'Ketua Kamar')
                ->where(function($q) {
                    $q->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>', now());
                })->exists();
        }
        
        return view('manajemenasetdanasrama::penghuni.create', [
            'title' => 'Tambah Penghuni Kamar',
            'kamar' => $kamar,
            'siswa' => $siswa,
            'selectedKamarId' => $selectedKamarId,
            'hasKetua' => $hasKetua,
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
            'jabatan'        => 'required|string',
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

        // Otomatisasi Keterangan berdasarkan Jabatan
        $jabatan = $request->jabatan;
        $keteranganManual = $request->keterangan;
        $validated['keterangan'] = $keteranganManual ? "{$jabatan} - {$keteranganManual}" : $jabatan;

        // CEK 2: Apakah kamar masih ada sisa kapasitas?
        $kamar = Kamar::findOrFail($request->kamar_id);
        if ($kamar->sisa <= 0) {
            return redirect()->back()->withInput()->with('error', 'Kapasitas kamar ini sudah penuh.');
        }

        // ABSOLUTE RULE: Jika jabatan baru adalah Ketua Kamar, turunkan ketua lama jadi Anggota
        if ($validated['jabatan'] == 'Ketua Kamar') {
            KamarPenghuni::where('kamar_id', $request->kamar_id)
                ->where('jabatan', 'Ketua Kamar')
                ->where(function($q) {
                    $q->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>', now());
                })
                ->update(['jabatan' => 'Anggota', 'keterangan' => 'Demoted from Ketua Kamar (Automatic)']);
        }

        KamarPenghuni::create($validated);

        // Regenerate jadwal piket kamar masa depan (Auto-Update)
        /** @var \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService $piketService */
        $piketService = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
        $piketService->regenerateFutureJadwal($request->kamar_id);

        return redirect()->route('manajemenasetdanasrama.kamar.show', $request->kamar_id)
            ->with('success', 'Penghuni kamar berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified penghuni.
     */
    public function edit(string $id): View
    {
        $penghuni = KamarPenghuni::findOrFail($id);
        
        // Ambil SEMUA kamar (biar bisa tukeran sama yang penuh sekalipun)
        $kamar = Kamar::with('penghuni.siswa')->get();

        // Ambil santri yang AKTIF dan BELUM punya kamar, ATAU santri yang sedang diedit ini
        $siswa = Siswa::aktif()
            ->where(function($query) use ($penghuni) {
                $query->whereDoesntHave('kamarPenghuni', function($q) {
                    $q->where(function($inner) {
                        $inner->whereNull('tanggal_keluar')
                              ->orWhere('tanggal_keluar', '>', now());
                    });
                })
                ->orWhere('id', $penghuni->siswa_id);
            })
            ->orderBy('nama')
            ->get();
        
        // Cek apakah kamar ini sudah punya ketua (selain penghuni ini sendiri)
        $hasKetua = KamarPenghuni::where('kamar_id', $penghuni->kamar_id)
            ->where('id', '!=', $id)
            ->where('jabatan', 'Ketua Kamar')
            ->where(function($q) {
                $q->whereNull('tanggal_keluar')
                  ->orWhere('tanggal_keluar', '>', now());
            })->exists();
        
        return view('manajemenasetdanasrama::penghuni.edit', [
            'title'    => 'Edit / Tukar Penghuni Kamar',
            'penghuni' => $penghuni,
            'kamar'    => $kamar,
            'siswa'    => $siswa,
            'hasKetua' => $hasKetua,
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
            'jabatan'        => 'required|string',
            'tanggal_masuk'  => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'keterangan'     => 'nullable|string',
        ]);

        // Logic Tukar (Swap)
        $swapPenghuniId = $request->swap_penghuni_id;
        $oldKamarId = $penghuni->kamar_id;
        $oldKamar = Kamar::find($oldKamarId);
        $oldJabatan = $penghuni->jabatan;
        $newKamarId = $request->kamar_id;
        $newKamar = Kamar::findOrFail($newKamarId);

        $autoKeterangan = "";

        if ($oldKamarId != $newKamarId) {
            // Catatan history pindah
            $autoKeterangan = " (Pindahan dari " . ($oldKamar->nama_kamar ?? 'Kamar Lama') . " tgl " . date('d/m/Y') . ")";
            
            // Jika kamar penuh, tapi admin pilih orang buat dituker
            if ($newKamar->sisa <= 0 && $swapPenghuniId) {
                $swapTarget = KamarPenghuni::findOrFail($swapPenghuniId);
                
                // Backup jabatan target
                $swapTargetJabatan = $swapTarget->jabatan;
                $swapTargetNama = $swapTarget->siswa->nama ?? 'Siswa';

                // Si Target pindah ke kamar lama Si Ahmad DAN ambil jabatan lama Si Ahmad
                // Serta update keterangannya otomatis
                $swapTarget->update([
                    'kamar_id'   => $oldKamarId,
                    'jabatan'    => $oldJabatan,
                    'keterangan' => $oldJabatan . " (Tukar dengan {$penghuni->siswa->nama} dari " . ($newKamar->nama_kamar) . ")"
                ]);

                // Si Ahmad ambil jabatan Si Target di kamar baru
                $validated['jabatan'] = $swapTargetJabatan;
                $autoKeterangan = " (Tukar dengan {$swapTargetNama} dari " . ($newKamar->nama_kamar) . ")";
            } 
            // Jika kamar penuh dan TIDAK ada orang buat dituker
            elseif ($newKamar->sisa <= 0) {
                return redirect()->back()->withInput()->with('error', 'Kapasitas kamar baru sudah penuh. Silakan pilih penghuni untuk ditukar.');
            }
        }

        // ABSOLUTE RULE: Jika jabatan baru adalah Ketua Kamar, turunkan ketua lama jadi Anggota (kecuali jika swap)
        if ($validated['jabatan'] == 'Ketua Kamar' && !$swapPenghuniId) {
            KamarPenghuni::where('kamar_id', $validated['kamar_id'])
                ->where('id', '!=', $id)
                ->where('jabatan', 'Ketua Kamar')
                ->where(function($q) {
                    $q->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>', now());
                })
                ->update(['jabatan' => 'Anggota', 'keterangan' => 'Demoted from Ketua Kamar (Automatic)']);
        }

        // Otomatisasi Keterangan berdasarkan Jabatan (untuk Update)
        $jabatan = $validated['jabatan']; // Pakai jabatan terbaru (bisa hasil swap)
        $keteranganManual = $request->keterangan;
        
        // Bersihkan prefix jabatan lama dan history lama jika ada
        $cleanKeterangan = $keteranganManual;
        $patterns = [
            '/^(Ketua Kamar|Wakil Ketua Kamar|Anggota) - /',
            '/\(Pindahan dari .*?\)/',
            '/\(Tukar dengan .*?\)/'
        ];
        $cleanKeterangan = preg_replace($patterns, '', $cleanKeterangan ?? '');
        $cleanKeterangan = trim($cleanKeterangan, " -");
        
        // Gabungkan Jabatan + Keterangan Manual + Auto History
        $finalKeterangan = $jabatan;
        if ($cleanKeterangan) $finalKeterangan .= " - " . $cleanKeterangan;
        if ($autoKeterangan) $finalKeterangan .= $autoKeterangan;

        $validated['keterangan'] = $finalKeterangan;

        $penghuni->update($validated);

        // Regenerate jadwal piket (Auto-Update untuk kedua kamar)
        /** @var \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService $piketService */
        $piketService = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
        $piketService->regenerateFutureJadwal($penghuni->kamar_id);
        if ($oldKamarId != $penghuni->kamar_id) {
            $piketService->regenerateFutureJadwal($oldKamarId);
        }

        return redirect()->route('manajemenasetdanasrama.kamar.show', $penghuni->kamar_id)
            ->with('success', 'Data penghuni dan riwayat perpindahan berhasil diperbarui.');
    }

    /**
     * Remove the specified penghuni from storage (Soft Checkout / Histori Tetap Ada).
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $penghuni = KamarPenghuni::findOrFail($id);
        $kamarId = $penghuni->kamar_id;
        $kamarNama = $penghuni->kamar->nama_kamar ?? 'Kamar';
        
        $alasan = $request->input('alasan_hapus');
        
        $newKet = "Keluaran dari {$kamarNama}";
        if ($alasan) {
            $newKet = "Keluar: {$alasan} ({$newKet})";
        }
        
        $penghuni->update([
            'tanggal_keluar' => now(),
            'keterangan'     => $newKet
        ]);

        // Regenerate jadwal piket
        /** @var \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService $piketService */
        $piketService = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
        $piketService->regenerateFutureJadwal($kamarId);

        return redirect()->back()
            ->with('success', 'Santri berhasil dikeluarkan dari kamar, riwayat tersimpan, dan jadwal piket disesuaikan.');
    }

    /**
     * Show form to assign multiple residents to a room.
     */
    public function assignMultiple(string $kamarId): View
    {
        $kamar = Kamar::findOrFail($kamarId);
        
        // Ambil santri yang AKTIF dan BELUM punya kamar sama sekali
        $siswa = Siswa::aktif()
            ->whereDoesntHave('kamarPenghuni', function($q) {
                $q->where(function($query) {
                    $query->whereNull('tanggal_keluar')
                          ->orWhere('tanggal_keluar', '>', now());
                });
            })
            ->orderBy('nama')
            ->get();

        return view('manajemenasetdanasrama::penghuni.assign_multiple', [
            'title' => 'Input Penghuni: ' . $kamar->nama_kamar,
            'kamar' => $kamar,
            'siswa' => $siswa,
        ]);
    }

    /**
     * Store multiple residents for a room.
     */
    public function storeMultiple(Request $request, string $kamarId): RedirectResponse
    {
        $kamar = Kamar::findOrFail($kamarId);
        
        $request->validate([
            'siswa_id'       => 'required|array',
            'siswa_id.*'     => 'nullable|exists:siswa,id',
            'jabatan'        => 'required|array',
            'tanggal_masuk'  => 'required|date',
            'keterangan'     => 'nullable|array',
        ]);

        $count = 0;
        foreach ($request->siswa_id as $index => $siswaId) {
            if (empty($siswaId)) continue;

            $jabatan = $request->jabatan[$index] ?? 'Anggota';
            $keteranganManual = $request->keterangan[$index] ?? null;
            $keteranganFinal = $keteranganManual ? "{$jabatan} - {$keteranganManual}" : $jabatan;

            // ABSOLUTE RULE: Jika input masal ada yang jadi Ketua, turunkan yang lama
            if ($jabatan == 'Ketua Kamar') {
                KamarPenghuni::where('kamar_id', $kamarId)
                    ->where('jabatan', 'Ketua Kamar')
                    ->where(function($q) {
                        $q->whereNull('tanggal_keluar')
                          ->orWhere('tanggal_keluar', '>', now());
                    })
                    ->update(['jabatan' => 'Anggota', 'keterangan' => 'Demoted from Ketua Kamar (Automatic Batch)']);
            }

            KamarPenghuni::create([
                'kamar_id'      => $kamarId,
                'siswa_id'      => $siswaId,
                'jabatan'       => $jabatan,
                'tanggal_masuk' => $request->tanggal_masuk,
                'keterangan'    => $keteranganFinal,
            ]);
            $count++;
        }

        // Regenerate jadwal piket
        if ($count > 0) {
            /** @var \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService $piketService */
            $piketService = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
            $piketService->regenerateFutureJadwal($kamarId);
        }

        return redirect()->route('manajemenasetdanasrama.kamar.show', $kamarId)
            ->with('success', "Berhasil menambahkan {$count} penghuni ke kamar {$kamar->nama_kamar}.");
    }
}