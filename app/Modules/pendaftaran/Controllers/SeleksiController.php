<?php
namespace Modules\Pendaftaran\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;    
use Modules\Seleksi\Models\Seleksi;

class SeleksiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pendaftaran_id' => 'required',
            'nama_tes' => 'required',
        ]);

        Seleksi::create($request->all());

        return back()->with('success', 'Tes berhasil ditambahkan');
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
