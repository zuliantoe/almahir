@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('pegawaimanager.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm btn-animate">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        {{-- Sisi Kiri: List Wawancara --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-user-check text-success mr-2"></i> Rekrut dari Daftar Wawancara
                    </h5>
                    <span class="badge badge-success px-3 py-2 rounded-pill">{{ $wawancara->count() }} Kandidat Siap</span>
                </div>
                
                <div class="card-body p-0">
                    @if($wawancara->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-top-0 border-bottom-0 pl-4">Identitas Kandidat</th>
                                        <th class="border-top-0 border-bottom-0">Posisi Dilamar</th>
                                        <th class="border-top-0 border-bottom-0 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wawancara as $calon)
                                    <tr>
                                        <td class="pl-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 font-weight-bold shadow-sm" style="width: 45px; height: 45px; font-size: 1.2rem;">
                                                    {{ strtoupper(substr($calon->nama, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold">{{ $calon->nama }}</h6>
                                                    <small class="text-muted"><i class="fas fa-envelope mr-1"></i>{{ $calon->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-primary px-3 py-1 rounded-pill" style="font-size: 0.85rem;">
                                                {{ $calon->typePegawai->nama_type ?? 'Tidak Diketahui' }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <form action="{{ route('pegawaimanager.calon-pegawai.update', $calon->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action_type" value="status_update">
                                                <input type="hidden" name="status_seleksi" value="diterima">
                                                <button type="button" class="btn btn-success btn-sm rounded-pill px-4 font-weight-bold shadow-sm btn-animate btn-confirm-accept" data-name="{{ $calon->nama }}">
                                                    <i class="fas fa-check-circle mr-1"></i> Terima Pegawai
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Kosong" style="width: 150px; opacity: 0.5" class="mb-3">
                            <h5 class="text-muted font-weight-bold">Tidak ada kandidat di tahap wawancara.</h5>
                            <p class="text-muted small">Semua kandidat sudah diproses atau belum ada pelamar baru yang mencapai tahap ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Input Manual --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 bg-primary text-white" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-4 position-relative">
                    {{-- Efek dekoratif --}}
                    <i class="fas fa-keyboard position-absolute" style="font-size: 8rem; opacity: 0.1; right: -20px; bottom: -20px;"></i>
                    
                    <h5 class="font-weight-bold mb-3"><i class="fas fa-edit mr-2"></i> Input Pegawai Manual</h5>
                    <p class="mb-4 text-white-50" style="font-size: 0.95rem;">
                        Gunakan opsi ini jika Anda ingin memasukkan data pegawai baru secara langsung tanpa melalui jalur rekrutmen / wawancara (Jalur Khusus/Internal).
                    </p>
                    
                    <a href="{{ route('pegawaimanager.create', ['manual' => 'true']) }}" class="btn btn-light btn-block btn-lg rounded-pill font-weight-bold shadow text-primary btn-animate">
                        Lewati & Input Manual <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>

            <div class="alert alert-warning mt-4 border-0 shadow-sm" style="border-radius: 12px;">
                <h6 class="font-weight-bold"><i class="fas fa-lightbulb mr-1"></i> Tips HRD:</h6>
                <p class="mb-0 small">Sangat disarankan untuk memasukkan data calon pegawai ke menu <strong>Calon Pegawai</strong> terlebih dahulu agar perusahaan memiliki riwayat rekrutmen yang rapi.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('click', '.btn-confirm-accept', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Terima Pegawai?',
                html: `Kandidat <strong>${name}</strong> akan resmi dijadikan pegawai.<br><br>
                       <div class="text-left">
                           <label class="font-weight-bold text-dark small text-uppercase mb-1">Role Sistem</label>
                           <select id="swalRole" class="form-control mb-3" style="border-radius: 10px; border: 2px solid #e2e8f0; height: 45px;">
                               <option value="PEGAWAI" selected>PEGAWAI</option>
                               <option value="GURU">GURU</option>
                               <option value="STAF_TU">STAF TU</option>
                           </select>
                           <label class="font-weight-bold text-dark small text-uppercase mb-1">Password Akun Baru</label>
                           <input type="text" id="swalPassword" class="form-control" placeholder="Biarkan kosong untuk password123" style="border-radius: 10px; border: 2px solid #e2e8f0; height: 45px;">
                       </div>`,
                icon: 'question',
                showCancelButton: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-success rounded-pill px-4 mx-2',
                    cancelButton: 'btn btn-light rounded-pill px-4 mx-2 border'
                },
                confirmButtonText: 'Ya, Terima Pegawai',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    return {
                        role_name: document.getElementById('swalRole').value,
                        password: document.getElementById('swalPassword').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let role = result.value.role_name;
                    let pass = result.value.password;
                    
                    form.append(`<input type="hidden" name="role_name" value="${role}">`);
                    if(pass.trim() !== '') {
                        form.append(`<input type="hidden" name="password" value="${pass.trim()}">`);
                    }
                    
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

