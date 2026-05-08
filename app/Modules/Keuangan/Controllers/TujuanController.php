<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Keuangan\Models\Tujuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TujuanController extends Controller
{
    public function index(): View
    {
        $tujuans = Tujuan::all();
        return view('keuangan::tujuans.index', [
            'title' => 'Master Kategori Pengeluaran',
            'tujuans' => $tujuans
        ]);
    }

    public function create(): View
    {
        return view('keuangan::tujuans.create', [
            'title' => 'Tambah Kategori Pengeluaran'
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:tujuans,nama'
        ]);

        Tujuan::create($request->all());

        return redirect()->route('keuangan.tujuans.index')
            ->with('success', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    public function edit(Tujuan $tujuan): View
    {
        return view('keuangan::tujuans.edit', [
            'title' => 'Edit Kategori Pengeluaran',
            'tujuan' => $tujuan
        ]);
    }

    public function update(Request $request, Tujuan $tujuan): RedirectResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:tujuans,nama,' . $tujuan->id
        ]);

        $tujuan->update($request->all());

        return redirect()->route('keuangan.tujuans.index')
            ->with('success', 'Kategori pengeluaran berhasil diperbarui.');
    }

    public function destroy(Tujuan $tujuan): RedirectResponse
    {
        $tujuan->delete();

        return redirect()->route('keuangan.tujuans.index')
            ->with('success', 'Kategori pengeluaran berhasil dihapus.');
    }
}
