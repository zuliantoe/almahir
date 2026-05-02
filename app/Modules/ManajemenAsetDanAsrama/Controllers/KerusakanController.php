<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\Kerusakan;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;
use App\Modules\ManajemenAsetDanAsrama\Traits\HasSoftDeleteWithUser;

class KerusakanController extends BaseController
{
    use HasSoftDeleteWithUser;
    /**
     * Display a listing of kerusakan.
     */
    public function index(Request $request): View
    {
        $kerusakan = Kerusakan::with('aset')
                        ->whereHas('aset')
                        ->where('status_penanganan', 'belum_ditangani')
                        ->latest()
                        ->paginate(15);
        
        return view('manajemenasetdanasrama::kerusakan.index', [
            'title'     => 'Data Kerusakan Aset Tertunda',
            'kerusakan' => $kerusakan,
        ]);
    }

    /**
     * Show the form for creating a new kerusakan.
     */
    public function create(): View
    {
        $aset = Aset::all();
        
        return view('manajemenasetdanasrama::kerusakan.create', [
            'title' => 'Lapor Kerusakan Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Store a newly created kerusakan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $aset = Aset::find($request->aset_id);
        $data = $this->getValidationRulesAndMessages($request, $aset);
        
        $validated = $request->validate($data['rules'], $data['messages']);
        $validated['tanggal_rusak'] = $validated['tanggal_kerusakan'];
        
        Kerusakan::create($validated);
        $this->syncStatusAset($validated);

        return redirect()->route('manajemenasetdanasrama.kerusakan.index')
            ->with('success', 'Laporan kerusakan berhasil disimpan dan status aset disinkronkan.');
    }

    /**
     * Show the form for editing the specified kerusakan.
     */
    public function edit(string $id): View
    {
        $kerusakan = Kerusakan::findOrFail($id);
        $aset = Aset::all();
        
        return view('manajemenasetdanasrama::kerusakan.edit', [
            'title'     => 'Edit Kerusakan Aset',
            'kerusakan' => $kerusakan,
            'aset'      => $aset,
        ]);
    }

    /**
     * Update the specified kerusakan in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $kerusakan = Kerusakan::findOrFail($id);
        $aset = Aset::find($request->aset_id);
        $data = $this->getValidationRulesAndMessages($request, $aset);

        $validated = $request->validate($data['rules'], $data['messages']);
        $validated['tanggal_rusak'] = $validated['tanggal_kerusakan'];
        
        $kerusakan->update($validated);
        $this->syncStatusAset($validated);

        return redirect()->route('manajemenasetdanasrama.kerusakan.index')
            ->with('success', 'Data kerusakan berhasil diperbarui dan status aset disinkronkan.');
    }

    /**
     * Get common validation rules and messages.
     */
    private function getValidationRulesAndMessages(Request $request, $aset): array
    {
        $minDate = $aset && $aset->tanggal_pengadaan ? $aset->tanggal_pengadaan->format('Y-m-d') : null;

        $rules = [
            'aset_id'          => 'required|exists:aset,id',
            'tanggal_kerusakan'=> 'required|date' . ($minDate ? '|after_or_equal:' . $minDate : ''),
            'deskripsi_kerusakan' => 'required|string',
            'tingkat_kerusakan' => 'required|in:ringan,sedang,berat',
            'status_penanganan' => 'required|in:belum_ditangani,sedang_ditangani,selesai',
            'catatan'          => 'nullable|string',
        ];

        $messages = [
            'tanggal_kerusakan.after_or_equal' => 'Tanggal kerusakan tidak boleh sebelum tanggal pengadaan aset (' . ($minDate ? date('d/m/Y', strtotime($minDate)) : '') . ').'
        ];

        return compact('rules', 'messages');
    }

    /**
     * Sync status aset based on penanganan.
     */
    private function syncStatusAset(array $validated): void
    {
        if ($validated['status_penanganan'] == 'sedang_ditangani') {
            Aset::where('id', $validated['aset_id'])->update(['status_kondisi' => 'dalam_perbaikan']);
        } elseif ($validated['status_penanganan'] == 'selesai') {
            Aset::where('id', $validated['aset_id'])->update(['status_kondisi' => 'sudah_diperbaiki']);
        } elseif ($validated['tingkat_kerusakan'] == 'berat' && $validated['status_penanganan'] == 'belum_ditangani') {
            Aset::where('id', $validated['aset_id'])->update(['status_kondisi' => 'rusak']);
        }
    }

    /**
     * Proses kerusakan ke meja pemeliharaan.
     */
    public function prosesPemeliharaan(string $id): RedirectResponse
    {
        $kerusakan = Kerusakan::findOrFail($id);

        // Update status kerusakan
        $kerusakan->update(['status_penanganan' => 'sedang_ditangani']);

        // Update status aset
        Aset::where('id', $kerusakan->aset_id)->update(['status_kondisi' => 'dalam_perbaikan']);

        // Redirect ke form tambah pemeliharaan sambil membawa aset_id dan deskripsi (optional) prepopulated
        return redirect()->route('manajemenasetdanasrama.pemeliharaan.create', [
            'aset_id' => $kerusakan->aset_id,
            'deskripsi_kerusakan' => 'Perbaikan untuk: ' . $kerusakan->deskripsi_kerusakan
        ])->with('info', 'Kerusakan sedang diproses. Silakan catat rincian awal tagihan pemeliharaan.');
    }

    /**
     * Remove the specified kerusakan from storage (soft delete).
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $kerusakan = Kerusakan::findOrFail($id);
        $this->performSoftDelete($request, $kerusakan);

        return redirect()->route('manajemenasetdanasrama.kerusakan.index')
            ->with('success', 'Kerusakan berhasil dihapus.');
    }
}