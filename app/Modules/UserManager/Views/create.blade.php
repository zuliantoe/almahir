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

                    {{-- 1. ROLE SELECTION FIRST --}}
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-primary"><i class="fas fa-user-tag mr-1"></i> Pilih Role Akun <span class="text-danger">*</span></label>
                        <p class="text-muted small">Pilih role terlebih dahulu untuk menampilkan formulir pengisian.</p>
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-4">
                                    <div class="form-check p-3 bg-white rounded border shadow-sm mb-2 hover-elevate role-box">
                                        <input type="radio" class="form-check-input role-checkbox ml-1 mt-2" 
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

                    {{-- 2. DYNAMIC FORM SECTION (HIDDEN UNTIL ROLE SELECTED) --}}
                    <div id="mainFormFields" style="display: none;">
                        
                        {{-- Dynamic Data Linking Section --}}
                        <div id="dataLinkingSection" class="form-group p-4 bg-info-light rounded border border-info mb-4" style="display: none; border-left-width: 5px !important; background: rgba(23, 162, 184, 0.05);">
                            <label class="font-weight-bold text-info"><i class="fas fa-link mr-1"></i> Hubungkan dengan Data <span id="linkableTypeName" class="text-uppercase"></span></label>
                            <select name="ref_id" id="refIdSelect" class="form-control rounded-pill px-3 shadow-sm select2" style="border: 1px solid #17a2b8;">
                                <option value="">-- Pilih data untuk di-link --</option>
                            </select>
                            <input type="hidden" name="ref_type" id="refTypeInput" value="">
                            <small class="form-text mt-2 text-info">
                                <i class="fas fa-info-circle mr-1"></i> 
                                Memilih data di atas akan otomatis mengisi nama dan email di bawah.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6" id="nameInputContainer">
                                <x-input label="Nama Lengkap" name="name" id="inputName" placeholder="Masukkan nama lengkap" :value="old('name')" required />
                            </div>
                            <div class="col-md-6" id="emailInputContainer">
                                <x-input label="Email" name="email" id="inputEmail" type="email" placeholder="user@example.com" :value="old('email')" required />
                            </div>
                        </div>

                        <div id="linkedInfoDisplay" class="alert alert-light border mb-4" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-user-check fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Akun Terhubung: <span id="displayLinkedName" class="text-primary"></span></h6>
                                    <small class="text-muted">Nama diambil otomatis dari data profil.</small>
                                </div>
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
                                <i class="fas fa-arrow-left mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 shadow-sm btn-animate gradient-primary border-0 font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Simpan User
                            </button>
                        </div>
                    </div>

                    {{-- PLACEHOLDER WHEN NO ROLE SELECTED --}}
                    <div id="noRolePlaceholder" class="text-center py-5">
                        <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Silakan pilih role di atas untuk memulai.</h5>
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
        const $roleCheckboxes = $('.role-checkbox');
    const $mainFormFields = $('#mainFormFields');
    const $noRolePlaceholder = $('#noRolePlaceholder');
    const $dataLinkingSection = $('#dataLinkingSection');
    const $refIdSelect = $('#refIdSelect');
    const $refTypeInput = $('#refTypeInput');
    const $linkableTypeSpan = $('#linkableTypeName');
    
    // Target inputs more specifically
    const $inputName = $('input[name="name"]');
    const $inputEmail = $('input[name="email"]');
    
    const linkableRoles = ['SISWA', 'GURU', 'WALI_MURID', 'PEGAWAI'];
    const roleTypeMap = {
        'SISWA': 'Modules\\Siswa\\Models\\Siswa',
        'GURU': 'Modules\\Guru\\Models\\Guru',
        'WALI_MURID': 'Modules\\WaliMurid\\Models\\WaliMurid',
        'PEGAWAI': 'Modules\\PegawaiManager\\Models\\Pegawai'
    };
    
    let linkableDataCache = {};

    function updateDataLinking() {
        let selectedLinkableRole = null;
        let anyRoleSelected = false;
        
        $roleCheckboxes.each(function() {
            if (this.checked) {
                anyRoleSelected = true;
                const roleName = $(this).data('role-name');
                if (linkableRoles.includes(roleName)) {
                    selectedLinkableRole = roleName;
                }
            }
        });
        
        if (anyRoleSelected) {
            $mainFormFields.show();
            $noRolePlaceholder.hide();
        } else {
            $mainFormFields.hide();
            $noRolePlaceholder.show();
        }

        if (selectedLinkableRole) {
            $dataLinkingSection.show();
            $linkableTypeSpan.text(selectedLinkableRole.replace('_', ' '));
            $refTypeInput.val(roleTypeMap[selectedLinkableRole] || '');
            
            fetchLinkableData(selectedLinkableRole);
        } else {
            $dataLinkingSection.hide();
            $refIdSelect.html('<option value="">-- Pilih data untuk di-link --</option>').trigger('change');
            $refTypeInput.val('');
        }
    }
    
    function fetchLinkableData(roleName) {
        $refIdSelect.html('<option value="">Memuat...</option>').trigger('change');
        
        $.ajax({
            url: `{{ route('users.linkable-data') }}`,
            data: { role: roleName },
            success: function(data) {
                linkableDataCache = {}; // Clear cache
                let options = '<option value="">-- Pilih Data --</option>';
                
                data.forEach(item => {
                    linkableDataCache[item.id] = item;
                    const identifier = item.identifier ? ` (${item.identifier})` : '';
                    options += `<option value="${item.id}">${item.nama}${identifier}</option>`;
                });
                
                $refIdSelect.html(options);
                
                if (data.length === 0) {
                    $refIdSelect.append('<option value="" disabled>-- Tidak ada data tersedia --</option>');
                }
                
                $refIdSelect.trigger('change');
            },
            error: function() {
                $refIdSelect.html('<option value="">-- Error memuat data --</option>').trigger('change');
            }
        });
    }

    // Auto-fill when linkable data is selected
    $refIdSelect.on('change', function() {
        const selectedId = $(this).val();
        const $nameInputContainer = $('#nameInputContainer');
        const $linkedInfoDisplay = $('#linkedInfoDisplay');
        const $displayLinkedName = $('#displayLinkedName');

        if (selectedId && linkableDataCache[selectedId]) {
            const data = linkableDataCache[selectedId];
            $inputName.val(data.nama || '');
            $inputEmail.val(data.email || '');
            
            // Hide name input and show info display
            $nameInputContainer.hide();
            $linkedInfoDisplay.show();
            $displayLinkedName.text(data.nama);
            
            // Visual feedback
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Data Terhubung',
                    text: 'Email terisi otomatis. Silakan isi password.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        } else {
            // Show name input again if manual
            $nameInputContainer.show();
            $linkedInfoDisplay.hide();
            // Don't clear if it was already manually filled, but usually we want to clear if deselected
            // However, the logic here is if deselected, clear it.
            if (!selectedId) {
                // Keep values if they were manually entered? 
                // Actually, if they chose "Input Manual", we keep it.
            }
        }
    });
    
    // Listen for role checkbox changes
    $roleCheckboxes.on('change', function() {
        updateDataLinking();
    });
    
    // Initial check
    updateDataLinking();
});
</script>
@endpush
