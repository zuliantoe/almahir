@extends('layouts.app')

@section('title', 'Detail Pendaftaran')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Detail Pendaftaran</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item">
                    <a href="/pendaftaran/admin/pendaftaran">Pendaftaran</a>
                </li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>
@endsection


@section('content')

@push('styles')
<style>
    /* Animasi modal muncul dari bawah ke atas */
    .modal-bottom-up.fade .modal-dialog {
        transform: translate(0, 50px);
        transition: transform 0.3s ease-out;
    }
    .modal-bottom-up.show .modal-dialog {
        transform: none;
    }
</style>
@endpush

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Data Lengkap Siswa
            </h3>
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- DATA SISWA --}}
            <h5 class="mb-3"><strong>Data Siswa</strong></h5>
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="30%">NISN</th>
                    <td>{{ $pendaftaran->nisn }}</td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $pendaftaran->nama_lengkap }}</td>
                </tr>
                <tr>
                    <th>Tempat Lahir</th>
                    <td>{{ $pendaftaran->tempat_lahir }}</td>
                </tr>
                <tr>
                    <th>Tanggal Lahir</th>
                    <td>{{ $pendaftaran->tanggal_lahir }}</td>
                </tr>
                <tr>
                    <th>Jenis Kelamin</th>
                    <td>
                        {{ $pendaftaran->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </td>
                </tr>
                <tr>
                    <th>Berat Badan</th>
                    <td>{{ $pendaftaran->berat_badan }} kg</td>
                </tr>
                <tr>
                    <th>Tinggi Badan</th>
                    <td>{{ $pendaftaran->tinggi_badan }} cm</td>
                </tr>
                <tr>
                    <th>Riwayat Sakit</th>
                    <td>{{ $pendaftaran->riwayat_sakit }}</td>
                </tr>
            </table>


            {{-- DATA ALAMAT --}}
            <h5 class="mb-3"><strong>Data Alamat</strong></h5>
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="30%">Kelurahan</th>
                    <td>{{ $pendaftaran->kelurahan }}</td>
                </tr>
                <tr>
                    <th>Kecamatan</th>
                    <td>{{ $pendaftaran->kecamatan }}</td>
                </tr>
                <tr>
                    <th>Kota</th>
                    <td>{{ $pendaftaran->kota }}</td>
                </tr>
                <tr>
                    <th>Provinsi</th>
                    <td>{{ $pendaftaran->provinsi }}</td>
                </tr>
                <tr>
                    <th>Alamat Lengkap</th>
                    <td>{{ $pendaftaran->alamat }}</td>
                </tr>
            </table>


            {{-- DATA ORANG TUA --}}
            <h5 class="mb-3"><strong>Data Orang Tua</strong></h5>
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="30%">Nama Ayah</th>
                    <td>{{ $pendaftaran->nama_ayah }}</td>
                </tr>
                <tr>
                    <th>Pekerjaan Ayah</th>
                    <td>{{ $pendaftaran->pekerjaan_ayah }}</td>
                </tr>
                <tr>
                    <th>No HP</th>
                    <td>{{ $pendaftaran->no_hp }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $pendaftaran->email }}</td>
                </tr>
            </table>


            {{-- STATUS & ADMIN --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0"><strong>Status Pendaftaran</strong></h5>
                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalCatatan">
                    <i class="fas fa-edit"></i> Edit Catatan
                </button>
            </div>
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Status</th>
                    <td>
                        @if ($pendaftaran->status == 'pending')
                            <span class="badge badge-warning">Ditunda</span>
                        @elseif($pendaftaran->status == 'diproses')
                            <span class="badge badge-info">Diproses</span>
                        @elseif($pendaftaran->status == 'diterima')
                            <span class="badge badge-success">Diterima</span>
                        @else
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Daftar</th>
                    <td>{{ date('d-m-Y H:i', strtotime($pendaftaran->tanggal_daftar)) }} </td>
                </tr>
                <tr>
                    <th>Tanggal Diterima</th>
                    <td>{{ $pendaftaran->tanggal_diterima ? date('d-m-Y H:i', strtotime($pendaftaran->tanggal_diterima)) : '-' }}</td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>{{ $pendaftaran->catatan ?? '-' }}</td>
                </tr>
            </table>

            <div class="mt-3">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTerima" {{ $pendaftaran->status == 'diterima' ? 'disabled' : '' }}>
                    <i class="fas fa-check"></i> Terima
                </button>

                <button type="button" class="btn btn-danger ml-2" data-toggle="modal" data-target="#modalTolak" {{ $pendaftaran->status == 'ditolak' ? 'disabled' : '' }}>
                    <i class="fas fa-times"></i> Tolak
                </button>
            </div>

            <!-- Modal Terima -->
            <div class="modal modal-bottom-up fade" id="modalTerima" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/status" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="diterima">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Konfirmasi Terima</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <i class="fas fa-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                                <h5 class="mb-0">Apakah Anda yakin ingin menerima pendaftar ini?</h5>
                                <p class="text-muted mt-2">Status pendaftaran akan diubah menjadi "Diterima".</p>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success px-4">Ya, Terima</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Tolak -->
            <div class="modal modal-bottom-up fade" id="modalTolak" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/status" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="ditolak">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Konfirmasi Tolak</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <i class="fas fa-times-circle text-danger mb-3" style="font-size: 4rem;"></i>
                                <h5 class="mb-0">Apakah Anda yakin ingin menolak pendaftar ini?</h5>
                                <p class="text-muted mt-2">Status pendaftaran akan diubah menjadi "Ditolak".</p>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger px-4">Ya, Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Catatan -->
            <div class="modal modal-bottom-up fade" id="modalCatatan" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/catatan" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">Edit Catatan Pendaftaran</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-left">
                                <div class="form-group">
                                    <label>Catatan Tambahan</label>
                                    <textarea name="catatan" rows="4" class="form-control" placeholder="Tulis catatan admin di sini...">{{ $pendaftaran->catatan }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-info">Simpan Catatan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- JADWAL TES --}}
            <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                <h5 class="m-0"><strong>Jadwal Tes</strong></h5>
                <div>
                    <button type="button" class="btn btn-sm btn-info mr-2" data-toggle="modal" data-target="#modalPilihTemplate">
                        <i class="fas fa-list-ol"></i> Pilih Template
                    </button>
                    <a href="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/jadwal" class="btn btn-sm btn-success">
                        <i class="fas fa-calendar-plus"></i> Set Jadwal Manual
                    </a>
                </div>
            </div>

            <!-- Modal Pilih Template -->
            <div class="modal modal-bottom-up fade" id="modalPilihTemplate" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}/apply-template" method="POST">
                            @csrf
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">Pilih Template Tes Seleksi</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-left">
                                <div class="form-group">
                                    <label>Pilih Template</label>
                                    <select name="template_id" class="form-control" required>
                                        <option value="">-- Pilih Template --</option>
                                        @foreach(\Modules\Pendaftaran\Models\TemplateSeleksi::latest()->get() as $tmpl)
                                            <option value="{{ $tmpl->id }}">{{ $tmpl->nama_template }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Template akan mendaftarkan beberapa tes sekaligus ke pendaftar.</small>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Tes <i>(Default untuk semua baris tes)</i></label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Jam <i>(Default, bisa diedit nanti)</i></label>
                                    <input type="time" name="jam" class="form-control" value="08:00" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-info">Terapkan Template</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if ($pendaftaran->seleksis->count() > 0)

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Tes</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Pengampu</th>
                            <th>Metode</th>
                            <th>Lokasi / Link</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftaran->seleksis as $jadwal)
                            <tr>
                                <td>{{ $jadwal->nama_tes }}</td>
                                <td>{{ $jadwal->tanggal }}</td>
                                <td>{{ $jadwal->jam }}</td>
                                <td>{{ $jadwal->pengampu }}</td>
                                <td>{{ $jadwal->metode }}</td>
                                <td>
                                    {{ $jadwal->lokasi ?? '-' }}
                                    @if ($jadwal->link)
                                        <br>
                                        <a href="{{ $jadwal->link }}" target="_blank">Link</a>
                                    @endif
                                </td>
                                <td>{{ $jadwal->nilai ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">Belum ada jadwal tes</p>
            @endif
        </div>


        <div class="card-footer text-right">
            <a href="/pendaftaran/admin/pendaftaran" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

    </div>

@endsection
