@extends('layouts.app')

@section('title', 'Manajemen Kurikulum')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h1 class="h3 mb-2 mb-md-0 text-gray-800">Manajemen Kurikulum</h1>
            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            <x-btn :href="route('akademik.kurikulum.create')" icon="fas fa-plus">
                Tambah Kurikulum
            </x-btn>
            @endif
        </div>
    </div>

    {{-- Form Filter --}}
    <x-card title="Filter Kurikulum" icon="fas fa-filter" outline collapsible>
        <form action="{{ route('akademik.kurikulum.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-4 mb-3">
                    <label>Master Kurikulum</label>
                    <select name="master_kurikulum_id" class="form-control">
                        <option value="">Semua Kurikulum</option>
                        @foreach($masterKurikulums as $mk)
                            <option value="{{ $mk->id }}" {{ request('master_kurikulum_id') == $mk->id ? 'selected' : '' }}>{{ $mk->nama_kurikulum }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Tingkat</label>
                    <select name="tingkat_id" class="form-control">
                        <option value="">Semua Tingkat</option>
                        @foreach($tingkats as $tingkat)
                            <option value="{{ $tingkat->id }}" {{ request('tingkat_id') == $tingkat->id ? 'selected' : '' }}>{{ $tingkat->nama_tingkat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3 d-flex" style="gap: 8px;">
                    <x-btn type="submit" class="btn-info flex-fill" icon="fas fa-search">Filter</x-btn>
                    @if(request()->anyFilled(['master_kurikulum_id', 'tingkat_id']))
                    <a href="{{ route('akademik.kurikulum.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    {{-- Tabel Data --}}
    <x-card title="Daftar Struktur Kurikulum" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Kurikulum</th>
                        <th>Tingkat</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th class="text-center">Jam</th>
                        <th class="text-center">KKM</th>
                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <th width="150px" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurikulum as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($kurikulum->currentPage() - 1) * $kurikulum->perPage() }}</td>
                        <td><span class="badge badge-light border">{{ $item->masterKurikulum->nama_kurikulum }}</span></td>
                        <td>{{ $item->tingkat->nama_tingkat }}</td>
                        <td>{{ $item->kelas->nama_kelas }}</td>
                        <td><strong>{{ $item->mataPelajaran->nama }}</strong></td>
                        <td class="text-center">{{ $item->totaljam }} Jam</td>
                        <td class="text-center"><span class="text-primary font-weight-bold">{{ $item->kkm }}</span></td>
                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">
                                <x-btn :href="route('akademik.kurikulum.show', $item->id)" size="sm" class="btn-info" title="Detail" style="margin: 0;">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.kurikulum.edit', $item->id)" size="sm" class="btn-warning" title="Edit" style="margin: 0;">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <form action="{{ route('akademik.kurikulum.destroy', $item->id) }}" method="POST" class="d-inline" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <x-btn type="submit" size="sm" class="btn-danger btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </x-btn>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-book-open fa-2x mb-3"></i><br>
                            Tidak ada data kurikulum
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kurikulum->hasPages())
        <x-slot name="footer">
            {{ $kurikulum->withQueryString()->links() }}
        </x-slot>
        @endif
    </x-card>
</div>
@endsection

