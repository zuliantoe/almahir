<?php

namespace Modules\Pendaftaran\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pendaftaran\Models\TemplateSeleksi;
use Modules\Pendaftaran\Models\TemplateSeleksiItem;

class TemplateSeleksiController extends Controller
{
    public function index()
    {
        $templates = TemplateSeleksi::with('items')->latest()->get();
        return view('pendaftaran::admin.template.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        TemplateSeleksi::create($request->all());

        return back()->with('success', 'Template berhasil dibuat');
    }

    public function destroy($id)
    {
        TemplateSeleksi::findOrFail($id)->delete();
        return back()->with('success', 'Template dihapus');
    }

    public function storeItem(Request $request, $id)
    {
        $request->validate([
            'nama_tes' => 'required|string|max:255',
            'metode' => 'required|in:offline,online',
            'pengampu' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'link' => 'nullable|url'
        ]);

        $template = TemplateSeleksi::findOrFail($id);
        $template->items()->create($request->all());

        return back()->with('success', 'Item tes ditambahkan ke template');
    }

    public function destroyItem($id)
    {
        TemplateSeleksiItem::findOrFail($id)->delete();
        return back()->with('success', 'Item dihapus dari template');
    }
}
