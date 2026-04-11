@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <x-card title="Detail Pengajuan" icon="fas fa-search">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Nama Pegawai</th>
                        <td>{{ $perizinan->pegawai->nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Izin</th>
                        <td><span class="badge badge-info">{{ strtoupper($perizinan->jenis_izin) }}</span></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $perizinan->tanggal_mulai->format('d F Y') }} s/d {{ $perizinan->tanggal_selesai->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Alasan</th>
                        <td>{{ $perizinan->alasan }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($perizinan->status == 'menunggu')
                                <span class="badge badge-warning p-2">MENUNGGU KONFIRMASI</span>
                            @elseif($perizinan->status == 'disetujui')
                                <span class="badge badge-success p-2">DISETUJUI</span>
                            @else
                                <span class="badge badge-danger p-2">DITOLAK</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if($perizinan->keterangan_admin)
                <div class="mt-3 p-3 bg-light rounded border border-info" style="border-left-width: 5px !important;">
                    <h6 class="font-weight-bold text-info"><i class="fas fa-comment-dots mr-1"></i> Catatan Admin:</h6>
                    <p class="mb-0 italic">{{ $perizinan->keterangan_admin }}</p>
                </div>
                @endif

                @if($perizinan->bukti)
                <div class="mt-4">
                    <h6 class="font-weight-bold">Lampiran Bukti:</h6>
                    <img src="{{ Storage::url($perizinan->bukti) }}" class="img-fluid border rounded" style="max-height: 400px;">
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('perizinan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </x-card>
        </div>

        @if($perizinan->status == 'menunggu' && (Auth::user()->hasRole('SUPER_ADMIN') || Auth::user()->hasRole('STAF_TU')))
        <div class="col-md-4">
            <x-card title="Proses Pengajuan" icon="fas fa-check-circle" type="primary" :outline="true">
                <p>Silakan berikan keputusan untuk pengajuan ini.</p>
                <form action="{{ route('perizinan.update-status', $perizinan->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Catatan Admin <small>(Opsional)</small></label>
                        <textarea name="keterangan_admin" class="form-control mb-3" rows="2" placeholder="Alasan penolakan atau catatan..."></textarea>
                    </div>

                    <div class="d-flex flex-column">
                        <button type="submit" name="status" value="disetujui" class="btn btn-success btn-block mb-2" onclick="return confirm('Setujui pengajuan ini?')">
                            <i class="fas fa-check mr-1"></i> SETUJUI
                        </button>
                        
                        <button type="submit" name="status" value="ditolak" class="btn btn-danger btn-block" onclick="return confirm('Tolak pengajuan ini?')">
                            <i class="fas fa-times mr-1"></i> TOLAK
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
        @endif
    </div>
</div>
@endsection
