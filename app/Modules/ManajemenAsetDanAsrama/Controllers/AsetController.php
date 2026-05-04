<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;

use App\Modules\ManajemenAsetDanAsrama\Traits\HasSoftDeleteWithUser;

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
        $kamar = \App\Modules\ManajemenAsetDanAsrama\Models\Kamar::all();
        
        return view('manajemenasetdanasrama::aset.edit', [
            'title' => 'Edit Aset',
            'aset'  => $aset,
            'kamar' => $kamar,
        ]);
    }

    /**
     * Update the specified aset in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $aset = Aset::findOrFail($id);
        $oldStatus = $aset->status_kondisi;
        
        $validated = $request->validate([
            'nama_aset'       => 'required|string|max:255',
            'kamar_id'        => 'required|exists:kamar,id',
            'status_kondisi'  => 'required|in:baik,rusak,dalam_perbaikan,sudah_diperbaiki',
            'kondisi'         => 'nullable|string',
            'deskripsi_aset'  => 'nullable|string',
        ]);

        $aset->update($validated);

        // Fase 3: Auto-Maintenance Engine
        // Jika status diubah dari selain 'rusak' menjadi 'rusak'
        if ($oldStatus !== 'rusak' && $validated['status_kondisi'] === 'rusak') {
            // Cek apakah sudah ada kerusakan belum ditangani untuk aset ini
            $existingKerusakan = \App\Modules\ManajemenAsetDanAsrama\Models\Kerusakan::where('aset_id', $aset->id)
                ->where('status_penanganan', 'belum_ditangani')
                ->exists();

            if (!$existingKerusakan) {
                \App\Modules\ManajemenAsetDanAsrama\Models\Kerusakan::create([
                    'aset_id'           => $aset->id,
                    'tanggal_lapor'     => now(),
                    'pelapor'           => auth()->user()->name ?? 'Sistem Otomatis',
                    'deskripsi_kerusakan'=> 'Otomatis dicatat oleh sistem karena status aset diubah menjadi rusak.',
                    'tingkat_kerusakan' => 'sedang', // Default level
                    'status_penanganan' => 'belum_ditangani',
                    'catatan'           => null,
                ]);
            }
        }

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
     * Restore the specified asset from trash.
     */
    public function restore(string $id): RedirectResponse
    {
        $aset = Aset::onlyTrashed()->findOrFail($id);
        $aset->restore();

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', 'Aset berhasil dikembalikan.');
    }

    /**
     * Print asset labels with QR Code.
     */
    public function printLabel(Request $request)
    {
        $ids = $request->input('ids');
        if ($ids) {
            $idsArray = explode(',', $ids);
            $aset = Aset::with('kamar')->whereIn('id', $idsArray)->get();
        } else {
            // Jika tidak ada ID, mungkin dari halaman index tanpa seleksi (print all filtered?)
            // Untuk sekarang, kita support yang ada ID-nya saja atau satu ID
            $id = $request->input('id');
            if ($id) {
                $aset = Aset::with('kamar')->where('id', $id)->get();
            } else {
                return redirect()->back()->with('error', 'Pilih aset yang ingin dicetak labelnya.');
            }
        }

        return view('manajemenasetdanasrama::aset.print-label', [
            'title' => 'Cetak Label Aset',
            'aset'  => $aset
        ]);
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