<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Keuangan\Models\Pengeluaran;
use Modules\Keuangan\Models\Tujuan;
use Carbon\Carbon;

/**
 * PengeluaranController
 * 
 * CRUD operations for Pengeluaran module.
 */
class PengeluaranController extends Controller
{
    public function index(Request $request): View
    {
        // Ambil semua pengeluaran beserta tujuan
        $pengeluarans = Pengeluaran::with('tujuan')->get();
        return view('keuangan::pengeluarans.index', compact('pengeluarans'));
    }

    public function create(): View
    {
        $tujuans = Tujuan::all();
        return view('keuangan::pengeluarans.create', compact('tujuans'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'tujuan_id'   => 'required|exists:tujuans,id',
            'jumlah'      => 'required|numeric|min:0|max:99999999999999999999999999',
            'tanggal'     => 'required|date',
            'deskripsi'   => 'nullable|string'
        ]);

        $pengeluaran = new Pengeluaran;
        $pengeluaran->tujuan_id   = $request->tujuan_id;
        $pengeluaran->jumlah      = $request->jumlah;
        $pengeluaran->tanggal     = $request->tanggal;
        $pengeluaran->deskripsi   = $request->deskripsi;
        $pengeluaran->save();

        return redirect()->route('keuangan.pengeluarans.index')->with('success', 'Pengeluaran berhasil ditambahkan!');
    }

    public function show(string $id): View
    {
        $pengeluaran = Pengeluaran::with('tujuan')->findOrFail($id);
        return view('keuangan::pengeluarans.show', compact('pengeluaran'));
    }

    public function edit(string $id): View
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $tujuans = Tujuan::all();
        return view('keuangan::pengeluarans.edit', compact('pengeluaran', 'tujuans'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'tujuan_id'   => 'required|exists:tujuans,id',
            'jumlah'      => 'required|numeric',
            'tanggal'     => 'required|date',
            'deskripsi'   => 'nullable|string'
        ]);

        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->tujuan_id   = $request->tujuan_id;
        $pengeluaran->jumlah      = $request->jumlah;
        $pengeluaran->tanggal     = $request->tanggal;
        $pengeluaran->deskripsi   = $request->deskripsi;
        $pengeluaran->save();

        return redirect()->route('keuangan.pengeluarans.index')->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();

        return redirect()->route('keuangan.pengeluarans.index')->with('success', 'Pengeluaran berhasil dihapus!');
    }
}
