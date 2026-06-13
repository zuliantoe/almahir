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
        $dateRules = 'required|date|before_or_equal:' . date('Y-m-d');
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            $dateRules .= '|after_or_equal:' . date('Y-m-01');
        }

        // Validasi input
        $request->validate([
            'sumber_id'   => 'required|exists:sumbers,id',
            'jumlah'      => 'required|numeric|min:0|max:99999999999999999999999999',
            'tanggal'     => $dateRules,
            'deskripsi'   => 'nullable|string'
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh kurang dari awal bulan ini.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.'
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
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data pemasukan.');
        }
        $pemasukan = Pemasukan::findOrFail($id);

        $sumbers = Sumber::all();
        return view('keuangan::pemasukans.edit', compact('pemasukan', 'sumbers'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data pemasukan.');
        }
        $pemasukan = Pemasukan::findOrFail($id);

        $dateRules = 'required|date|before_or_equal:' . date('Y-m-d');
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            $dateRules .= '|after_or_equal:' . date('Y-m-01');
        }

        $request->validate([
            'sumber_id'   => 'required|exists:sumbers,id',
            'jumlah'      => 'required|numeric',
            'tanggal'     => $dateRules,
            'deskripsi'   => 'nullable|string'
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh kurang dari awal bulan ini.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.'
        ]);


        $pemasukan->sumber_id   = $request->sumber_id;
        $pemasukan->jumlah      = $request->jumlah;
        $pemasukan->tanggal     = $request->tanggal;
        $pemasukan->deskripsi   = $request->deskripsi;
        $pemasukan->save();

        // Uang Saku sync removed

        return redirect()->route('keuangan.pemasukans.index')->with('success', 'Pemasukan berhasil diperbarui!');
    }

    public function destroy(string $id): RedirectResponse
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data pemasukan.');
        }
        $pemasukan = Pemasukan::findOrFail($id);
        
        // Uang Saku sync removed

        $pemasukan->delete();

        return redirect()->route('keuangan.pemasukans.index')->with('success', 'Pemasukan berhasil dihapus!');
    }

    public function confirmDraft(Request $request, string $id): RedirectResponse
    {
        $pemasukan = Pemasukan::findOrFail($id);
        
        if (!$pemasukan->is_draft) {
            return back()->with('error', 'Data ini sudah dikonfirmasi sebelumnya.');
        }

        $pemasukan->is_draft = false;
        $pemasukan->save();

        return back()->with('success', 'Draft pemasukan berhasil dikonfirmasi menjadi transaksi.');
    }
}
