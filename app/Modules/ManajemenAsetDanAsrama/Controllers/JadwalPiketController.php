<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;
use Modules\Siswa\Models\Siswa;

class JadwalPiketController extends BaseController
{
    /**
     * Display a listing of jadwal piket.
     */
    public function index(Request $request): View
    {
        $jadwal = JadwalPiket::with('siswa')
                    ->orderBy('bulan')
                    ->orderBy('pekan')
                    ->orderBy('hari')
                    ->paginate(15);
        
        return view('manajemenasetdanasrama::jadwal-piket.index', [
            'title'  => 'Jadwal Piket',
            'jadwal' => $jadwal,
        ]);
    }

    /**
     * Show the form for creating a new jadwal piket.
     */
    public function create(): View
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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->getValidationRules());
        $validated['status'] = 'belum';

        JadwalPiket::create($validated);

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified jadwal piket.
     */
    public function edit(string $id): View
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $siswa = Siswa::all();
        
        return view('manajemenasetdanasrama::jadwal-piket.edit', [
            'title'  => 'Edit Jadwal Piket',
            'jadwal' => $jadwal,
            'siswa'  => $siswa,
        ]);
    }

    /**
     * Update the specified jadwal piket in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $validated = $request->validate($this->getValidationRules());

        $jadwal->update($validated);

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil diperbarui.');
    }

    /**
     * Get common validation rules.
     */
    private function getValidationRules(): array
    {
        return [
            'bulan'    => 'required|integer|between:1,12',
            'pekan'    => 'required|integer|between:1,5',
            'hari'     => 'required|string',
            'tempat'   => 'required|string|max:255',
            'siswa_id' => 'required|exists:siswa,id',
        ];
    }

    /**
     * Remove the specified jadwal piket from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil dihapus.');
    }

    /**
     * Mark jadwal piket as completed.
     */
    public function selesai(string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->status = 'sudah';
        $jadwal->save();

        return redirect()->back()
            ->with('success', 'Status piket diupdate menjadi selesai.');
    }
}