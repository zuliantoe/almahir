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
        $pendaftaran = Pendaftaran::with(['seleksis' => function($q) {
            $q->with('guru');
        }])->findOrFail($id);
        
        $gurus = \Modules\Guru\Models\Guru::aktif()->get();

        return view('pendaftaran::admin.jadwal', compact('pendaftaran', 'gurus'));
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
        'guru_id' => 'nullable|exists:guru,id',
    ]);

    Seleksi::create([
        'pendaftaran_id' => $id,
        'nama_tes' => $request->nama_tes,
        'tanggal' => $request->tanggal,
        'jam' => $request->jam,
        'pengampu' => $request->pengampu,
        'guru_id' => $request->guru_id,
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
    public function updateNilai(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100'
        ]);

        $seleksi = \Modules\Pendaftaran\Models\Seleksi::findOrFail($id);

        $seleksi->update([
            'nilai' => $request->nilai
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Nilai berhasil disimpan']);
        }

        return back()->with('success', 'Nilai berhasil disimpan');
    }
    
    public function applyTemplate(Request $request, $id)
    {
        $request->validate([
            'template_id' => 'required|exists:template_seleksis,id',
            'tanggal' => 'required|date',
            'jam' => 'required'
        ]);

        $template = \Modules\Pendaftaran\Models\TemplateSeleksi::with('items')->findOrFail($request->template_id);
        
        $guruIdByPengampu = null;
        foreach ($template->items as $item) {
            $guruIdByPengampu = null;

            // Template masih menyimpan pengampu sebagai string (nama guru).
            // Mapping dilakukan saat apply template: cari guru_id berdasarkan nama.
            if (!empty($item->pengampu)) {
                $guruIdByPengampu = \Modules\Guru\Models\Guru::query()
                    ->where('nama', $item->pengampu)
                    ->value('id');
            }

            \Modules\Pendaftaran\Models\Seleksi::create([
                'pendaftaran_id' => $id,
                'nama_tes' => $item->nama_tes,
                'tanggal' => $request->tanggal,
                'jam' => $request->jam,
                'pengampu' => $item->pengampu,
                'guru_id' => $guruIdByPengampu,
                'metode' => $item->metode,
                'lokasi' => $item->lokasi,
                'link' => $item->link,
            ]);
        }

        $pendaftaran = Pendaftaran::findOrFail($id);
        if ($pendaftaran->status == 'pending') {
            $pendaftaran->update(['status' => 'diproses']);
        }

        return back()->with('success', 'Template tes berhasil diterapkan');
    }
}
