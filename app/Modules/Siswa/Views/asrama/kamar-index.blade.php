@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0" style="font-weight: 700;">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Kamar</li>
        </ol>
    </div>
</div>

<style>
    .table-compact td {
        padding: 0.6rem 0.75rem !important;
        font-size: 0.9rem;
    }
    .table-compact thead th {
        padding: 0.75rem !important;
        background-color: #f4f6f9;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-xs-custom {
        padding: 1px 6px !important;
        font-size: 0.7rem !important;
        border-radius: 4px !important;
        margin: 0 2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 24px;
        width: 24px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <x-card title="Daftar Kamar Asrama" icon="fas fa-door-open">
                
                {{-- Search Form --}}
                <form action="{{ route('siswa.asrama.kamar.index') }}" method="GET" class="mb-4">
                    <div class="input-group shadow-sm" style="max-width: 400px;">
                        <input type="text" name="search" class="form-control border-right-0" placeholder="Cari nama kamar atau deskripsi..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('siswa.asrama.kamar.index') }}" class="btn btn-secondary">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-compact mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="200">Nama Kamar</th>
                                <th width="150">Kapasitas</th>
                                <th width="180">Status Hunian</th>
                                <th>Deskripsi</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $myKamarId = auth()->user()->ref?->kamarPenghuni()->aktif()->first()?->kamar_id; 
                            @endphp
                            @forelse($kamar as $item)
                            @php $isMyKamar = $item->id == $myKamarId; @endphp
                            <tr style="{{ $isMyKamar ? 'background-color: rgba(67, 97, 238, 0.05); border-left: 4px solid #4361ee;' : '' }}">
                                <td class="text-center text-muted">{{ $loop->iteration + ($kamar->currentPage() - 1) * $kamar->perPage() }}</td>
                                <td>
                                    <strong>{{ $item->nama_kamar }}</strong>
                                    @if($isMyKamar) <span class="badge badge-primary ml-1" style="font-size: 0.6rem;">Kamar Saya</span> @endif
                                    <div class="mt-1">
                                        @forelse($item->penghuniAktif->take(3) as $p)
                                            <span class="badge badge-light border text-dark mb-1" style="font-weight: 400; font-size: 0.7rem;">
                                                <i class="fas fa-user-circle mr-1 text-primary"></i> {{ Str::words($p->siswa->nama ?? 'N/A', 1, '') }}
                                            </span>
                                        @empty
                                            <small class="text-muted italic">Kosong</small>
                                        @endforelse
                                        @if($item->penghuniAktif->count() > 3)
                                            <span class="badge badge-light border text-muted mb-1" style="font-size: 0.7rem;">+{{ $item->penghuniAktif->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="progress progress-xs" style="height: 6px;">
                                        @php $percent = $item->kapasitas > 0 ? ($item->terisi / $item->kapasitas) * 100 : 0; @endphp
                                        <div class="progress-bar {{ $percent >= 100 ? 'bg-danger' : ($percent >= 75 ? 'bg-warning' : 'bg-success') }}" 
                                             role="progressbar" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $item->terisi }} / {{ $item->kapasitas }} Orang</small>
                                </td>
                                <td>{!! $item->status_kapasitas_badge !!}</td>
                                <td><small class="text-muted">{{ Str::limit($item->deskripsi ?? '-', 80) }}</small></td>
                                <td class="text-center">
                                    <a href="{{ route('siswa.asrama.kamar.show', $item->id) }}" class="btn btn-xs-custom btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>Tidak ditemukan data kamar yang cocok
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kamar->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $kamar->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $kamar->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>
@endsection
