@extends('layouts.app')

@section('title', 'Set Jadwal Tes')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Set Jadwal Tes</h1>
        </div>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                {{ $pendaftaran->nama_lengkap }}
            </h3>
        </div>

        <div class="card-body">

            {{-- FORM TAMBAH JADWAL --}}
            <form method="POST" action="{{ route('pendaftaran.admin.jadwal.store', $pendaftaran->id) }}">
                @csrf

                <div class="form-group">
                    <label>Nama Tes</label>
                    <input type="text" name="nama_tes" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Jam</label>
                    <input type="time" name="jam" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Metode</label>
                    <select name="metode" class="form-control">
                        <option value="offline">Offline</option>
                        <option value="online">Online</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Pengampu</label>
                    <input type="text" name="pengampu" class="form-control">
                </div>

                <div class="form-group">
                    <label>Lokasi (jika offline)</label>
                    <input type="text" name="lokasi" class="form-control">
                </div>

                <div class="form-group">
                    <label>Link (jika online)</label>
                    <input type="url" name="link" class="form-control">
                </div>

                <button type="submit" class="btn btn-success">
                    Simpan Jadwal
                </button>
            </form>

            <hr>

            {{-- DAFTAR JADWAL --}}
            <h5>Daftar Jadwal Tes</h5>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Tes</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Pengampu</th>
                        <th>Metode</th>
                        <th>Nilai</th>
                        <th style="width: 1%; white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($pendaftaran->seleksis as $seleksi)
                        <tr>

                            <td>{{ $seleksi->nama_tes }}</td>
                            <td>{{ $seleksi->tanggal }}</td>
                            <td>{{ $seleksi->jam }}</td>
                            <td>{{ $seleksi->pengampu }}</td>
                            <td>{{ ucfirst($seleksi->metode) }}</td>

                            {{-- INPUT NILAI --}}
                            <td>
                                <form id="form-nilai-{{ $seleksi->id }}" method="POST"
                                    action="{{ route('pendaftaran.admin.jadwal.updateNilai', $seleksi->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <input type="number" name="nilai" value="{{ $seleksi->nilai }}" min="0"
                                        max="100" style="width:80px" class="form-control">
                                </form>
                            </td>

                            <td style="width: 1%; white-space: nowrap;">
                                <div class="d-flex" style="gap: 5px;">
                                    <button type="submit" form="form-nilai-{{ $seleksi->id }}" class="btn btn-success btn-sm">
                                        Simpan
                                    </button>
                                    
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal{{ $seleksi->id }}">
                                        Edit
                                    </button>

                                    <form method="POST" action="/pendaftaran/seleksi/{{ $seleksi->id }}" onsubmit="return confirm('Yakin ingin menghapus tes ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>

                        </tr>

                        <!-- Modal Edit Jadwal -->
                        <div class="modal fade" id="editModal{{ $seleksi->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $seleksi->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form method="POST" action="/pendaftaran/seleksi/{{ $seleksi->id }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $seleksi->id }}">Edit Jadwal Tes</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-left">
                                            <div class="form-group">
                                                <label>Nama Tes</label>
                                                <input type="text" name="nama_tes" class="form-control" value="{{ $seleksi->nama_tes }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control" value="{{ $seleksi->tanggal }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Jam</label>
                                                <input type="time" name="jam" class="form-control" value="{{ date('H:i', strtotime($seleksi->jam)) }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Metode</label>
                                                <select name="metode" class="form-control">
                                                    <option value="offline" {{ $seleksi->metode == 'offline' ? 'selected' : '' }}>Offline</option>
                                                    <option value="online" {{ $seleksi->metode == 'online' ? 'selected' : '' }}>Online</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Pengampu</label>
                                                <input type="text" name="pengampu" class="form-control" value="{{ $seleksi->pengampu }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Lokasi (jika offline)</label>
                                                <input type="text" name="lokasi" class="form-control" value="{{ $seleksi->lokasi }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Link (jika online)</label>
                                                <input type="url" name="link" class="form-control" value="{{ $seleksi->link }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada jadwal
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="card-footer text-right">
            <a href="/pendaftaran/admin/pendaftaran/{{ $pendaftaran->id }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>

    </div>

@endsection
