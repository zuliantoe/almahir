@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-chalkboard-teacher mr-1 text-primary"></i> {{ $title }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('akademik.beban-mengajar.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="tahunajaran_id" class="form-control shadow-sm" onchange="this.form.submit()">
                                    <option value="">-- Semua Tahun Ajaran --</option>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta->id }}" {{ request('tahunajaran_id', $tahunAjaranAktif?->id) == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->tahunajaran }} {{ $ta->semester ? '- ' . $ta->semester : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        @forelse($gurus as $guru)
                            @php
                                // Asumsi: 24 jam adalah beban standar. 
                                // Bisa disesuaikan dengan aturan sekolah sebenarnya.
                                $progress = ($guru->total_jam / 24) * 100;
                                $progressClass = 'bg-info';
                                $statusText = 'Belum Maksimal';
                                
                                if($guru->total_jam >= 24) {
                                    $progressClass = 'bg-success';
                                    $statusText = 'Full (Standar)';
                                    $progress = 100;
                                } elseif($guru->total_jam == 0) {
                                    $progressClass = 'bg-secondary';
                                    $statusText = 'Belum Ada Jadwal';
                                } elseif($guru->total_jam > 30) {
                                    $progressClass = 'bg-danger';
                                    $statusText = 'Overload';
                                    $progress = 100;
                                }
                            @endphp
                            <div class="col-md-4 col-sm-6 col-12">
                                <div class="info-box bg-light shadow-sm border">
                                    <span class="info-box-icon {{ str_replace('bg-', 'text-', $progressClass) }}"><i class="fas fa-user-tie"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text font-weight-bold text-truncate" title="{{ $guru->nama }}">{{ $guru->nama }}</span>
                                        <span class="info-box-number text-lg mb-1">
                                            {{ $guru->total_jam }} <small class="font-weight-normal text-muted">Jam / Minggu</small>
                                        </span>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar {{ $progressClass }}" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="progress-description text-sm mt-1">
                                            <span class="badge {{ $progressClass }}">{{ $statusText }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4">
                                <p class="text-muted">Data guru tidak ditemukan.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-3">
                        {{ $gurus->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
