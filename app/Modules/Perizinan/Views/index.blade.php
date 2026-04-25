@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <x-card title="{{ $title }}" icon="fas fa-envelope-open-text">
        <x-slot name="tools">
            @if(!$isAdmin)
            <a href="{{ route('perizinan.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Ajukan Izin
            </a>
            @endif
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        @if($isAdmin)
                        <th>Nama Pegawai</th>
                        @endif
                        <th>Jenis</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perizinan as $item)
                    <tr>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        @if($isAdmin)
                        <td>{{ $item->pegawai->nama ?? 'N/A' }}</td>
                        @endif
                        <td>
                            <span class="badge badge-info">{{ strtoupper($item->jenis_izin) }}</span>
                        </td>
                        <td>
                            {{ $item->tanggal_mulai->format('d M') }} - {{ $item->tanggal_selesai->format('d M Y') }}
                        </td>
                        <td>
                            @if($item->status == 'menunggu')
                                <span class="badge badge-warning">Menunggu</span>
                            @elseif($item->status == 'disetujui')
                                <span class="badge badge-success">Disetujui</span>
                            @else
                                <span class="badge badge-danger">Ditolak</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('perizinan.show', $item->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye shadow-xs"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 6 : 5 }}" class="text-center py-4 text-muted">
                            Belum ada data perizinan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $perizinan->links() }}
        </div>
    </x-card>
</div>
@endsection
