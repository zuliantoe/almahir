@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="container-fluid">
    <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
        @csrf
        
        <x-card title="Informasi User" icon="fas fa-user-plus">
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
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input role-checkbox" 
                                       name="roles[]" value="{{ $role->id }}" 
                                       id="role_{{ $role->id }}"
                                       data-role-name="{{ $role->name }}"
                                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->display_name }} <small class="text-muted">({{ $role->name }})</small>
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
            <div id="dataLinkingSection" class="form-group" style="display: none;">
                <label><i class="fas fa-link mr-1"></i> Link ke Data <span class="text-muted">(Opsional)</span></label>
                <select name="ref_id" id="refIdSelect" class="form-control">
                    <option value="">-- Pilih data untuk di-link --</option>
                </select>
                <input type="hidden" name="ref_type" id="refTypeInput" value="">
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Link user ke data <span id="linkableType"></span> yang belum memiliki akun login
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

            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <x-btn type="submit" variant="primary" icon="fas fa-save">Simpan</x-btn>
            </div>
        </x-card>
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
