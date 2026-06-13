<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Keuangan\Models\Pengeluaran;
use Modules\Keuangan\Models\Tujuan;
use Modules\Keuangan\Models\UangSaku;
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
        $dateRules = 'required|date|before_or_equal:' . date('Y-m-d');
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            $dateRules .= '|after_or_equal:' . date('Y-m-01');
        }

        // Validasi input
        $request->validate([
            'tujuan_id'   => 'required|exists:tujuans,id',
            'jumlah'      => 'required|numeric|min:0|max:99999999999999999999999999',
            'tanggal'     => $dateRules,
            'deskripsi'   => 'nullable|string'
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh kurang dari awal bulan ini.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.'
        ]);

        $totalPemasukan = \Modules\Keuangan\Models\Pemasukan::where('is_draft', false)->sum('jumlah');
        $totalPengeluaran = \Modules\Keuangan\Models\Pengeluaran::where('is_draft', false)->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        if ($request->jumlah > $saldo) {
            return back()->withErrors(['jumlah' => 'Jumlah pengeluaran melebihi saldo yang tersedia (Rp ' . number_format($saldo, 0, ',', '.') . ').'])->withInput();
        }

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
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data pengeluaran.');
        }
        $pengeluaran = Pengeluaran::findOrFail($id);

        $tujuans = Tujuan::all();
        return view('keuangan::pengeluarans.edit', compact('pengeluaran', 'tujuans'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data pengeluaran.');
        }
        $pengeluaran = Pengeluaran::findOrFail($id);

        $dateRules = 'required|date|before_or_equal:' . date('Y-m-d');
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            $dateRules .= '|after_or_equal:' . date('Y-m-01');
        }

        $request->validate([
            'tujuan_id'   => 'required|exists:tujuans,id',
            'jumlah'      => 'required|numeric|min:0|max:99999999999999999999999999',
            'tanggal'     => $dateRules,
            'deskripsi'   => 'nullable|string'
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh kurang dari awal bulan ini.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.'
        ]);
        
        $totalPemasukan = \Modules\Keuangan\Models\Pemasukan::where('is_draft', false)->sum('jumlah');
        $totalPengeluaranLain = \Modules\Keuangan\Models\Pengeluaran::where('id', '!=', $id)->where('is_draft', false)->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaranLain;

        if ($request->jumlah > $saldo) {
            return back()->withErrors(['jumlah' => 'Jumlah pengeluaran melebihi saldo yang tersedia (Rp ' . number_format($saldo, 0, ',', '.') . ').'])->withInput();
        }

        $pengeluaran->tujuan_id   = $request->tujuan_id;
        $pengeluaran->jumlah      = $request->jumlah;
        $pengeluaran->tanggal     = $request->tanggal;
        $pengeluaran->deskripsi   = $request->deskripsi;
        $pengeluaran->save();

        // Uang Saku sync removed

        return redirect()->route('keuangan.pengeluarans.index')->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy(string $id): RedirectResponse
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data pengeluaran.');
        }
        $pengeluaran = Pengeluaran::findOrFail($id);

        // Uang Saku sync removed

        $pengeluaran->delete();

        return redirect()->route('keuangan.pengeluarans.index')->with('success', 'Pengeluaran berhasil dihapus!');
    }

    public function confirmDraft(Request $request, string $id): RedirectResponse
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        
        if (!$pengeluaran->is_draft) {
            return back()->with('error', 'Data ini sudah dikonfirmasi sebelumnya.');
        }

        // Validate balance
        $totalPemasukan = \Modules\Keuangan\Models\Pemasukan::where('is_draft', false)->sum('jumlah');
        $totalPengeluaranLain = \Modules\Keuangan\Models\Pengeluaran::where('is_draft', false)->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaranLain;

        if ($pengeluaran->jumlah > $saldo) {
            return back()->with('error', 'Saldo tidak mencukupi untuk menambahkan data pengeluaran yang dijadwalkan (Rp ' . number_format($saldo, 0, ',', '.') . ').');
        }

        $pengeluaran->is_draft = false;
        $pengeluaran->save();

        return back()->with('success', 'Draft pengeluaran berhasil dikonfirmasi menjadi transaksi.');
    }
}
