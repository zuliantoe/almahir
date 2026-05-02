<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\Aset;

use Modules\ManajemenAsetDanAsrama\Traits\HasSoftDeleteWithUser;

class AsetController extends BaseController
{
    use HasSoftDeleteWithUser;

    /**
     * Display a listing of master aset.
     */
    public function index(Request $request): View
    {
        $aset = Aset::with('pengadaan:id,nomor_po,pengajuan_id', 'pengadaan.pengajuan:id,nomor_pengajuan')
                    ->whereNull('deleted_at')
                    ->latest()
                    ->paginate(15);
        
        return view('manajemenasetdanasrama::aset.index', [
            'title' => 'Master Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Display the specified aset.
     */
    public function show(string $id): View
    {
        $aset = Aset::with(['pengadaan.pengajuan', 'kerusakan', 'pemeliharaan'])
                    ->findOrFail($id);
        
        return view('manajemenasetdanasrama::aset.show', [
            'title' => 'Detail Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Show the form for editing the specified aset.
     */
    public function edit(string $id): View
    {
        $aset = Aset::findOrFail($id);
        
        return view('manajemenasetdanasrama::aset.edit', [
            'title' => 'Edit Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Update the specified aset in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $aset = Aset::findOrFail($id);
        
        $validated = $request->validate([
            'nama_aset'       => 'required|string|max:255',
            'status_kondisi'  => 'required|in:baik,rusak,dalam_perbaikan,sudah_diperbaiki',
            'kondisi'         => 'nullable|string',
            'deskripsi_aset'  => 'nullable|string',
        ]);

        $aset->update($validated);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Data aset berhasil diperbarui.');
    }

    /**
     * Remove the specified aset from storage (soft delete).
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $aset = Aset::findOrFail($id);
        
        $this->performSoftDelete($request, $aset);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Aset berhasil dipindahkan ke trash.');
    }

    /**
     * Duplicate the specified aset.
     */
    public function duplicate(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'jumlah_duplikat' => 'required|integer|min:1|max:50'
        ]);

        $originalAset = Aset::findOrFail($id);
        $jumlah = (int) $request->jumlah_duplikat;
        $baseCode = $originalAset->kode_aset;

        // Pisahkan bagian teks dan bagian angka (e.g. MEJA-001 -> 'MEJA-', '001')
        // Jika tidak ada angka di belakang, tambahkan '-1'
        if (preg_match('/^(.*?)(\d+)$/', $baseCode, $matches)) {
            $prefix = $matches[1];
            $numericFormatLength = strlen($matches[2]);
            $currentNumber = (int) $matches[2];
        } else {
            $prefix = $baseCode . '-';
            $numericFormatLength = 1;
            $currentNumber = 0;
        }

        $duplicatedCount = 0;

        for ($i = 1; $i <= $jumlah; $i++) {
            $currentNumber++;
            
            // Coba cari kode yang belum terpakai kalau-kalau sudah ada
            do {
                $newCode = $prefix . str_pad($currentNumber, $numericFormatLength, '0', STR_PAD_LEFT);
                $exists = Aset::withTrashed()->where('kode_aset', $newCode)->exists();
                if ($exists) {
                    $currentNumber++;
                }
            } while ($exists);

            $newAset = $originalAset->replicate();
            $newAset->kode_aset = $newCode;
            $newAset->status_kondisi = 'baik'; // Set status awal ke baik
            $newAset->save();

            $duplicatedCount++;
        }

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', "Berhasil menduplikat aset sebanyak {$duplicatedCount} kali dengan rentang kode berurutan.");
    }
}