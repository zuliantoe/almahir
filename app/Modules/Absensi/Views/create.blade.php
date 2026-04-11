@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <x-card title="Presensi Mandiri" icon="fas fa-fingerprint" class="shadow-lg border-0">
                
                <div class="text-center py-4">
                    <h5 class="text-muted mb-1">{{ now()->translatedFormat('l, d F Y') }}</h5>
                    <div id="realtime-clock" class="display-4 font-weight-bold text-primary mb-4">00:00:00</div>
                    
                    <div class="p-3 mb-4 bg-light rounded-lg border">
                        <div class="d-flex align-items-center justify-content-center">
                            <img src="{{ Auth::user()->avatar_url }}" class="img-circle elevation-2 mr-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <div class="text-left">
                                <h6 class="mb-0 font-weight-bold">{{ $pegawai->nama }}</h6>
                                <p class="text-muted small mb-0">{{ $pegawai->typePegawai->nama_type ?? 'Pegawai' }}</p>
                            </div>
                        </div>
                    </div>

                    @if(!$currentAbsen)
                        {{-- BELUM ABSEN MASUK --}}
                        <div class="alert alert-info border-0 shadow-sm mb-4">
                            <i class="fas fa-info-circle mr-2"></i> Silakan tekan tombol <strong>Masuk</strong> untuk memulai hari kerja Anda.
                        </div>

                        <form action="{{ route('absensi.store') }}" method="POST" id="form-absen">
                            @csrf
                            <input type="hidden" name="lat" id="lat">
                            <input type="hidden" name="long" id="long">
                            
                            <div class="form-group text-left">
                                <label class="small font-weight-bold">Catatan (Opsional)</label>
                                <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Bekerja dari rumah, atau keterangan lainnya..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block rounded-pill py-3 shadow">
                                <i class="fas fa-sign-in-alt mr-2"></i> KLIK UNTUK ABSEN MASUK
                            </button>
                        </form>
                    @elseif(!$currentAbsen->jam_pulang)
                        {{-- SUDAH MASUK, BELUM PULANG --}}
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x mr-3"></i>
                                <div class="text-left">
                                    <div class="font-weight-bold">Anda Sudah Absen Masuk</div>
                                    <small>Pukul: {{ $currentAbsen->jam_masuk }} (Status: {{ $currentAbsen->status }})</small>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('absensi.update') }}" method="POST" id="form-absen">
                            @csrf
                            <input type="hidden" name="lat" id="lat">
                            <input type="hidden" name="long" id="long">

                            <button type="submit" class="btn btn-danger btn-lg btn-block rounded-pill py-3 shadow">
                                <i class="fas fa-sign-out-alt mr-2"></i> KLIK UNTUK ABSEN PULANG
                            </button>
                        </form>
                    @else
                        {{-- SUDAH SELESAI HARI INI --}}
                        <div class="py-5">
                            <i class="fas fa-mug-hot fa-4x text-muted mb-3"></i>
                            <h4 class="font-weight-bold">Kerja Bagus!</h4>
                            <p class="text-muted">Anda telah menyelesaikan presensi hari ini.</p>
                            <div class="badge badge-light border p-2">
                                Masuk: {{ $currentAbsen->jam_masuk }} | Pulang: {{ $currentAbsen->jam_pulang }}
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('absensi.index') }}" class="btn btn-outline-primary rounded-pill">
                                    <i class="fas fa-history mr-1"></i> Lihat Riwayat
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Realtime Clock
    function updateClock() {
        const now = new Date();
        const display = now.toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('realtime-clock').textContent = display;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Geolocation
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('long').value = position.coords.longitude;
            console.log("Location captured");
        }, function(error) {
            console.warn("Geolocation error: " + error.message);
        });
    }

    // Confirmation logic
    const form = document.getElementById('form-absen');
    if(form) {
        form.addEventListener('submit', function(e) {
            const isPulang = this.action.includes('update');
            const title = isPulang ? 'Yakin ingin pulang?' : 'Konfirmasi Absen Masuk';
            const text = isPulang ? 'Pastikan pekerjaan Anda hari ini sudah selesai.' : 'Waktu akan dicatat sekarang.';
            
            e.preventDefault();
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    }
</script>
@endpush
@endsection
