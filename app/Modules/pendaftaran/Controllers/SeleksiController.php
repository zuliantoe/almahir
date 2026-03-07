<?php

namespace Modules\Pendaftaran\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pendaftaran\Models\Seleksi;
use Modules\Pendaftaran\Models\Pendaftaran;

class SeleksiController extends Controller

{

    public function index($id)
    {
        $pendaftaran = Pendaftaran::with('seleksis')->findOrFail($id);

        return view('pendaftaran::admin.jadwal', compact('pendaftaran'));
    }
    public function store(Request $request, $id)
{
    $request->validate([
        'nama_tes' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'jam' => 'required',
        'metode' => 'required|in:offline,online',
        'lokasi' => 'nullable|string|max:255',
        'link' => 'nullable|url',
    ]);

    Seleksi::create([
        'pendaftaran_id' => $id,
        'nama_tes' => $request->nama_tes,
        'tanggal' => $request->tanggal,
        'jam' => $request->jam,
        'pengampu' => $request->pengampu,
        'metode' => $request->metode,
        'lokasi' => $request->lokasi,
        'link' => $request->link,
    ]);

    // ubah status jadi diproses kalau masih pending
    $pendaftaran = Pendaftaran::findOrFail($id);
    if ($pendaftaran->status == 'pending') {
        $pendaftaran->update(['status' => 'diproses']);
    }

    return back()->with('success', 'Jadwal berhasil ditambahkan');
}

    public function update(Request $request, $id)
    {
        $seleksi = Seleksi::findOrFail($id);
        $seleksi->update($request->all());

        return back()->with('success', 'Seleksi diperbarui');
    }

    public function destroy($id)
    {
        Seleksi::findOrFail($id)->delete();

        return back()->with('success', 'Tes dihapus');
    }
}
