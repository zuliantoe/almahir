<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Keuangan\Models\Pemasukan;
use Modules\Keuangan\Models\Sumber;
use Modules\Keuangan\Models\UangSaku;
use Carbon\Carbon;

/**
 * PemasukanController
 * 
 * CRUD operations for Pemasukan module.
 */
class PemasukanController extends Controller
{
    public function index(Request $request): View
    {
        // Ambil semua pemasukan beserta sumber
        $pemasukans = Pemasukan::with('sumber')->get();
        return view('keuangan::pemasukans.index', compact('pemasukans'));
    }

    public function create(): View
    {
        $sumbers = Sumber::all();
        return view('keuangan::pemasukans.create', compact('sumbers'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'sumber_id'   => 'required|exists:sumbers,id',
            'jumlah'      => 'required|numeric|min:0|max:99999999999999999999999999',
            'tanggal'     => 'required|date',
            'deskripsi'   => 'nullable|string'
        ]);

        $pemasukan = new Pemasukan;
        $pemasukan->sumber_id   = $request->sumber_id;
        $pemasukan->jumlah      = $request->jumlah;
        $pemasukan->tanggal     = $request->tanggal;
        $pemasukan->deskripsi   = $request->deskripsi;
        $pemasukan->save();

        return redirect()->route('keuangan.pemasukans.index')->with('success', 'Pemasukan berhasil ditambahkan!');
    }

    public function show(string $id): View
    {
        $pemasukan = Pemasukan::with('sumber')->findOrFail($id);
        return view('keuangan::pemasukans.show', compact('pemasukan'));
    }

    public function edit(string $id): View
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $sumbers = Sumber::all();
        return view('keuangan::pemasukans.edit', compact('pemasukan', 'sumbers'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'sumber_id'   => 'required|exists:sumbers,id',
            'jumlah'      => 'required|numeric',
            'tanggal'     => 'required|date',
            'deskripsi'   => 'nullable|string'
        ]);

        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->sumber_id   = $request->sumber_id;
        $pemasukan->jumlah      = $request->jumlah;
        $pemasukan->tanggal     = $request->tanggal;
        $pemasukan->deskripsi   = $request->deskripsi;
        $pemasukan->save();

        // Sync back to Uang Saku if linked and status is "Belum Diterima Santri"
        if ($pemasukan->uang_saku_id) {
            $uangsaku = UangSaku::find($pemasukan->uang_saku_id);
            if ($uangsaku && $uangsaku->status === 'Belum Diterima Santri') {
                $uangsaku->update([
                    'jumlah' => $pemasukan->jumlah,
                    'tanggal' => $pemasukan->tanggal
                ]);
            }
        }

        return redirect()->route('keuangan.pemasukans.index')->with('success', 'Pemasukan berhasil diperbarui!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $pemasukan = Pemasukan::findOrFail($id);
        
        // If this pemasukan is linked to a Uang Saku, delete the Uang Saku as well
        if ($pemasukan->uang_saku_id) {
            UangSaku::where('id', $pemasukan->uang_saku_id)->delete();
        }

        $pemasukan->delete();

        return redirect()->route('keuangan.pemasukans.index')->with('success', 'Pemasukan berhasil dihapus!');
    }
}
