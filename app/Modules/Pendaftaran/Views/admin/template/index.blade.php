@extends('layouts.app')

@section('title', 'Master Template Tes')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Master Template Tes Seleksi</h1>
        </div>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Template</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahTemplate">
                    <i class="fas fa-plus"></i> Buat Template Baru
                </button>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="30%">Nama Template</th>
                        <th>Deskripsi</th>
                        <th width="200">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $tmpl)
                        <tr>
                            <td>
                                <strong>{{ $tmpl->nama_template }}</strong><br>
                                <small class="text-info">{{ $tmpl->items->count() }} item tes terdaftar</small>
                            </td>
                            <td>{{ $tmpl->deskripsi ?? '-' }}</td>
                            <td>
                                <!-- Manage Button -->
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalKelola{{ $tmpl->id }}">
                                    <i class="fas fa-cog"></i> Kelola Item
                                </button>

                                <!-- Delete Template -->
                                <form action="/pendaftaran/admin/template-seleksi/{{ $tmpl->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus template ini? Semua setelan item di dalamnya juga ikut terhapus!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <div class="text-muted"><i class="fas fa-folder-open mb-2" style="font-size: 2rem;"></i><br>Belum ada template tes yang dibuat</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-right">
            <a href="/pendaftaran/admin/pendaftaran" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pendaftar
            </a>
        </div>
    </div>

    @foreach($templates as $tmpl)
        {{-- MODAL KELOLA ITEM TEMPLATE --}}
        <div class="modal fade" id="modalKelola{{ $tmpl->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Kelola Isi Template: {{ $tmpl->nama_template }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-3">
                        
                        <table class="table table-striped m-0">
                            <thead>
                                <tr>
                                    <th>Nama Tes</th>
                                    <th>Metode</th>
                                    <th>Pengampu</th>
                                    <th>Lokasi</th>
                                    <th>Link</th>
                                    <th width="100">Hapus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tmpl->items as $item)
                                    <tr>
                                        <td>{{ $item->nama_tes }}</td>
                                        <td>{{ ucfirst($item->metode) }}</td>
                                        <td>{{ $item->pengampu ?? '-' }}</td>
                                        <td>{{ $item->lokasi ?? '-' }}</td>
                                        <td>{{ $item->link ?? '-' }}</td>
                                        <td>
                                            <form action="/pendaftaran/admin/template-seleksi/items/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus item tes ini dari template?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($tmpl->items->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada item tes. Silakan tambah di bawah.</td>
                                    </tr>
                                @endif
                            </tbody>
                            
                            <!-- FORM TAMBAH ITEM -->
                            <tfoot>
                                <tr class="bg-light">
                                    <form action="/pendaftaran/admin/template-seleksi/{{ $tmpl->id }}/items" method="POST">
                                        @csrf
                                        <td class="p-2 border-0">
                                            <input type="text" name="nama_tes" class="form-control form-control-sm" placeholder="Contoh: Tes Baca Tulis" required>
                                        </td>
                                        <td class="p-2 border-0">
                                            <select name="metode" class="form-control form-control-sm" required>
                                                <option value="offline">Offline</option>
                                                <option value="online">Online</option>
                                            </select>
                                        </td>
                                        <td class="p-2 border-0">
                                            <select name="pengampu" class="form-control form-control-sm" required>
                                                <option value="" selected disabled>-- Pilih Guru --</option>
                                                @foreach(\Modules\Guru\Models\Guru::aktif()->get() as $guru)
                                                    <option value="{{ $guru->nama }}">{{ $guru->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2 border-0">
                                            <input type="text" name="lokasi" class="form-control form-control-sm" placeholder="R. Kelas A">
                                        </td>
                                        <td class="p-2 border-0">
                                            <input type="url" name="link" class="form-control form-control-sm" placeholder="https://zoom.us...">
                                        </td>
                                        <td class="p-2 border-0">
                                            <button type="submit" class="btn btn-sm btn-success btn-block" title="Tambah ke Template">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Tambah Template Baru -->
    <div class="modal fade" id="modalTambahTemplate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="/pendaftaran/admin/template-seleksi" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Buat Template Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Template</label>
                            <input type="text" name="nama_template" class="form-control" placeholder="Contoh: Seleksi SD Jalur Prestasi" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Khusus</label>
                            <textarea name="deskripsi" rows="2" class="form-control" placeholder="Opsional..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
