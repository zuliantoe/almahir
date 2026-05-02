@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="container-fluid">
    <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
        @csrf
        
        <div class="card border-0 shadow-lg mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header gradient-primary border-0 p-4">
                <h3 class="card-title text-white font-weight-bold mb-0">
                    <i class="fas fa-user-plus mr-2"></i> Tambah User Baru
                </h3>
            </div>
            
            <div class="card-body p-4 bg-light">
                <div class="glass-card p-4">
            <div class="row">
                <div class="col-md-6">
                    <x-input label="Nama Lengkap" name="name" placeholder="Masukkan nama lengkap" :value="old('name')" required />
                </div>
                <div class="col-md-6">
                    <x-input label="Email" name="email" type="email" placeholder="user@example.com" :value="old('email')" required />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input label="Password" name="password" type="password" placeholder="Minimal 8 karakter" required />
                </div>
                <div class="col-md-6">
                    <x-input label="Konfirmasi Password" name="password_confirmation" type="password" placeholder="Ulangi password" required />
                </div>
            </div>

            <div class="form-group">
                <label>Role <span class="text-danger">*</span></label>
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-4">
                            <div class="form-check p-3 bg-white rounded border shadow-sm mb-2 hover-elevate">
                                <input type="checkbox" class="form-check-input role-checkbox ml-1 mt-2" 
                                       name="roles[]" value="{{ $role->id }}" 
                                       id="role_{{ $role->id }}"
                                       data-role-name="{{ $role->name }}"
                                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                <label class="form-check-label ml-4 font-weight-bold" for="role_{{ $role->id }}">
                                    {{ $role->display_name }} <br><span class="badge badge-light text-muted">{{ $role->name }}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('roles')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Dynamic Data Linking Section --}}
            <div id="dataLinkingSection" class="form-group p-4 bg-info-light rounded border border-info mb-4" style="display: none; border-left-width: 5px !important; background: rgba(23, 162, 184, 0.05);">
                <label class="font-weight-bold text-info"><i class="fas fa-link mr-1"></i> Link ke Data <span class="text-muted font-weight-normal">(Opsional)</span></label>
                <select name="ref_id" id="refIdSelect" class="form-control rounded-pill px-3 shadow-sm" style="border: 1px solid #17a2b8;">
                    <option value="">-- Pilih data untuk di-link --</option>
                </select>
                <input type="hidden" name="ref_type" id="refTypeInput" value="">
                <small class="form-text mt-2 text-info">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Fungsi ini akan menghubungkan user ke data <b id="linkableType" class="text-uppercase"></b> yang belum memiliki akun login.
                </small>
            </div>

            <div class="form-group">
                <label>Status Akun <span class="text-danger">*</span></label>
                <select name="account_status" class="form-control @error('account_status') is-invalid @enderror" required>
                    <option value="active" {{ old('account_status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('account_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('account_status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr class="mt-4 mb-4">
            <div class="d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-secondary rounded-pill px-4 py-2 shadow-sm btn-animate font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 shadow-sm btn-animate gradient-primary border-0 font-weight-bold">
                    <i class="fas fa-save mr-2"></i> Simpan User
                </button>
            </div>
            
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    const dataLinkingSection = document.getElementById('dataLinkingSection');
    const refIdSelect = document.getElementById('refIdSelect');
    const refTypeInput = document.getElementById('refTypeInput');
    const linkableTypeSpan = document.getElementById('linkableType');
    
    const linkableRoles = ['SISWA', 'GURU', 'WALI_MURID'];
    const roleTypeMap = {
        'SISWA': 'Modules\\Siswa\\Models\\Siswa',
        'GURU': 'Modules\\Guru\\Models\\Guru',
        'WALI_MURID': 'Modules\\WaliMurid\\Models\\WaliMurid'
    };
    
    function updateDataLinking() {
        let selectedLinkableRole = null;
        
        roleCheckboxes.forEach(checkbox => {
            if (checkbox.checked && linkableRoles.includes(checkbox.dataset.roleName)) {
                selectedLinkableRole = checkbox.dataset.roleName;
            }
        });
        
        if (selectedLinkableRole) {
            dataLinkingSection.style.display = 'block';
            linkableTypeSpan.textContent = selectedLinkableRole.replace('_', ' ').toLowerCase();
            refTypeInput.value = roleTypeMap[selectedLinkableRole] || '';
            
            // Fetch available data
            fetchLinkableData(selectedLinkableRole);
        } else {
            dataLinkingSection.style.display = 'none';
            refIdSelect.innerHTML = '<option value="">-- Pilih data untuk di-link --</option>';
            refTypeInput.value = '';
        }
    }
    
    function fetchLinkableData(roleName) {
        refIdSelect.innerHTML = '<option value="">Memuat...</option>';
        
        fetch(`{{ route('users.linkable-data') }}?role=${roleName}`)
            .then(response => response.json())
            .then(data => {
                refIdSelect.innerHTML = '<option value="">-- Tidak di-link (buat baru) --</option>';
                data.forEach(item => {
                    const identifier = item.identifier ? ` (${item.identifier})` : '';
                    refIdSelect.innerHTML += `<option value="${item.id}">${item.nama}${identifier}</option>`;
                });
                
                if (data.length === 0) {
                    refIdSelect.innerHTML += '<option value="" disabled>-- Tidak ada data tersedia --</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching linkable data:', error);
                refIdSelect.innerHTML = '<option value="">-- Error memuat data --</option>';
            });
    }
    
    // Listen for role checkbox changes
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDataLinking);
    });
    
    // Initial check
    updateDataLinking();
});
</script>
@endpush
