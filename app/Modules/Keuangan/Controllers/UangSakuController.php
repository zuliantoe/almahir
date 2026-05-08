<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use Modules\Keuangan\Models\UangSaku;
use Modules\Siswa\Models\Siswa;
use Carbon\Carbon;

class UangSakuController extends Controller
{
    public function index(Request $request): View
    {
        $uangsakus = UangSaku::with('siswa')->get();
        return view('keuangan::uangsakus.index', compact('uangsakus'));
    }

    public function create(): View
    {
        $siswas = Siswa::where('status', 'aktif')->get();
        return view('keuangan::uangsakus.create', compact('siswas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'siswa_id'  => 'required|exists:siswa,id',
            'jumlah'    => 'required|numeric|min:0',
            'tanggal'   => 'required|date',
            'status'    => 'required|string',
            'deskripsi' => 'nullable|string'
        ]);

        UangSaku::create($request->all());

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Uang Saku berhasil ditambahkan!');
    }

    public function show(string $id): View
    {
        $uangsaku = UangSaku::with('siswa')->findOrFail($id);
        return view('keuangan::uangsakus.show', compact('uangsaku'));
    }

    public function edit(string $id): View
    {
        $uangsaku = UangSaku::findOrFail($id);
        $siswas = Siswa::where('status', 'aktif')->get();
        return view('keuangan::uangsakus.edit', compact('uangsaku', 'siswas'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'siswa_id'  => 'required|exists:siswa,id',
            'jumlah'    => 'required|numeric|min:0',
            'tanggal'   => 'required|date',
            'status'    => 'required|string',
            'deskripsi' => 'nullable|string'
        ]);

        $uangsaku = UangSaku::findOrFail($id);
        $uangsaku->update($request->all());

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Uang Saku berhasil diperbarui!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $uangsaku = UangSaku::findOrFail($id);
        $uangsaku->delete();

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Uang Saku berhasil dihapus!');
    }
}
