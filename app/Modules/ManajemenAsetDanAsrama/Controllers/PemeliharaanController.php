<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\Pemeliharaan;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;
use App\Modules\ManajemenAsetDanAsrama\Models\Kerusakan;
use App\Modules\ManajemenAsetDanAsrama\Traits\HasSoftDeleteWithUser;

class PemeliharaanController extends BaseController
{
    use HasSoftDeleteWithUser;
    /**
     * Display a listing of pemeliharaan (hanya yang masih proses).
     */
    public function index(Request $request): View
    {
        // Hanya tampilkan yang masih dalam proses pemeliharaan dan asetnya belum dihapus
        $pemeliharaan = Pemeliharaan::with('aset')
                            ->whereHas('aset')
                            ->where('status', 'proses')
                            ->latest()
                            ->paginate(15);
        
        return view('manajemenasetdanasrama::pemeliharaan.index', [
            'title'        => 'Data Pemeliharaan Aset',
            'pemeliharaan' => $pemeliharaan,
        ]);
    }

    /**
     * Show the form for creating a new pemeliharaan.
     */
    public function create(): View
    {
        $aset = Aset::all();
        
        return view('manajemenasetdanasrama::pemeliharaan.create', [
            'title' => 'Tambah Pemeliharaan Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Store a newly created pemeliharaan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $aset = Aset::find($request->aset_id);
        $data = $this->getValidationRulesAndMessages($request, $aset);
        
        $validated = $request->validate($data['rules'], $data['messages']);

        // Sync kolom asli yang NOT NULL
        $validated['tanggal_mulai_pemeliharaan'] = $validated['tanggal_pemeliharaan'];
        $validated['biaya_pemeliharaan'] = $validated['biaya'];
        $validated['status'] = 'proses';

        Pemeliharaan::create($validated);

        // Update status aset menjadi "dalam_perbaikan"
        Aset::where('id', $validated['aset_id'])->update(['status_kondisi' => 'dalam_perbaikan']);

        return redirect()->route('manajemenasetdanasrama.pemeliharaan.index')
            ->with('success', 'Data pemeliharaan berhasil disimpan. Status aset diubah menjadi "Dalam Perbaikan".');
    }

    /**
     * Show the form for editing the specified pemeliharaan.
     */
    public function edit(string $id): View
    {
        $pemeliharaan = Pemeliharaan::findOrFail($id);
        $aset = Aset::all();
        
        return view('manajemenasetdanasrama::pemeliharaan.edit', [
            'title'        => 'Edit Pemeliharaan Aset',
            'pemeliharaan' => $pemeliharaan,
            'aset'         => $aset,
        ]);
    }

    /**
     * Update the specified pemeliharaan in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $pemeliharaan = Pemeliharaan::findOrFail($id);
        $aset = Aset::find($request->aset_id);
        $data = $this->getValidationRulesAndMessages($request, $aset);

        $validated = $request->validate($data['rules'], $data['messages']);

        // Sync kolom asli yang NOT NULL
        $validated['tanggal_mulai_pemeliharaan'] = $validated['tanggal_pemeliharaan'];
        $validated['biaya_pemeliharaan'] = $validated['biaya'];
        $pemeliharaan->update($validated);

        return redirect()->route('manajemenasetdanasrama.pemeliharaan.index')
            ->with('success', 'Data pemeliharaan berhasil diperbarui.');
    }

    /**
     * Get common validation rules and messages.
     */
    private function getValidationRulesAndMessages(Request $request, $aset): array
    {
        $minDate = $aset && $aset->tanggal_pengadaan ? $aset->tanggal_pengadaan->format('Y-m-d') : null;

        $rules = [
            'aset_id'           => 'required|exists:aset,id',
            'tanggal_pemeliharaan' => 'required|date' . ($minDate ? '|after_or_equal:' . $minDate : ''),
            'deskripsi_pemeliharaan' => 'required|string',
            'biaya'             => 'required|numeric|min:0',
            'catatan'           => 'nullable|string',
        ];

        $messages = [
            'tanggal_pemeliharaan.after_or_equal' => 'Tanggal pemeliharaan tidak boleh sebelum tanggal pengadaan aset (' . ($minDate ? date('d/m/Y', strtotime($minDate)) : '') . ').'
        ];

        return compact('rules', 'messages');
    }

    /**
     * Tandai pemeliharaan selesai dan update status aset.
     */
    public function selesai(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_selesai'  => 'required|date',
            'catatan_selesai'  => 'nullable|string',
        ]);

        $pemeliharaan = Pemeliharaan::findOrFail($id);

        // Update pemeliharaan: status selesai
        $pemeliharaan->status = 'selesai';
        $pemeliharaan->tanggal_selesai_pemeliharaan = $validated['tanggal_selesai'];
        $pemeliharaan->catatan_selesai = $validated['catatan_selesai'];
        $pemeliharaan->save();

        // Update status aset menjadi "sudah_diperbaiki"
        Aset::where('id', $pemeliharaan->aset_id)->update([
            'status_kondisi' => 'sudah_diperbaiki',
        ]);

        // Apabila ada data Kerusakan untuk aset ini yang belum selesai, otomatis sinkronkan ke selesai
        Kerusakan::where('aset_id', $pemeliharaan->aset_id)
            ->whereIn('status_penanganan', ['belum_ditangani', 'sedang_ditangani'])
            ->update([
                'status_penanganan' => 'selesai',
                'catatan' => \DB::raw("CONCAT(COALESCE(catatan, ''), '\\n[Sistem]: Otomatis diselesaikan melalui modul Pemeliharaan pada " . now()->format('d-m-Y H:i') . "')")
            ]);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Pemeliharaan selesai. Aset "' . $pemeliharaan->aset->nama_aset . '" dan laporannya telah disinkronkan ke "Sudah Diperbaiki".');
    }

    /**
     * Remove the specified pemeliharaan from storage (soft delete).
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $pemeliharaan = Pemeliharaan::findOrFail($id);
        $this->performSoftDelete($request, $pemeliharaan);

        return redirect()->route('manajemenasetdanasrama.pemeliharaan.index')
            ->with('success', 'Pemeliharaan berhasil dihapus.');
    }
}