<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIAKAD') - {{ config('app.name', 'SIAKAD') }}</title>

    {{-- Google Fonts: Outfit --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Font Awesome 5 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    {{-- AdminLTE 3 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    {{-- Premium UI Styles --}}
    <link rel="stylesheet" href="{{ asset('css/premium-ui.css') }}">

    {{-- Animate.css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    
    {{-- Custom Styles --}}
    <style>
        .btn-action-xs {
            padding: 0.1rem 0.4rem !important;
            font-size: 0.75rem !important;
            line-height: 1.5 !important;
            border-radius: 4px !important;
        }
    </style>
    @stack('styles')
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --info-color: #4895ef;
            --card-radius: 16px;
            --btn-radius: 10px;
        }

        body {
            font-family: 'Outfit', sans-serif !important;
            background-color: #f8f9fc;
        }
        
        .main-sidebar {
            background-color: #1e1e2d !important;
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
        }

        .nav-sidebar .nav-link {
            border-radius: 12px;
            margin-bottom: 8px !important; /* Menaikkan margin antar menu */
            padding: 12px 15px !important; /* Menaikkan padding agar lebih lega */
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .nav-sidebar .nav-link .nav-icon {
            margin-left: 0 !important;
            margin-right: 12px !important;
            width: 20px !important;
            text-align: center;
            font-size: 1.1rem;
        }

        .nav-header {
            padding: 1.8rem 15px 0.8rem !important; /* Memberi ruang lebih pada header */
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            opacity: 0.8;
            color: #8a8a8e !important;
        }

        .nav-sidebar .nav-link.active {
            background-color: var(--primary-color) !important;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .content-wrapper {
            background-color: #f8f9fc;
            padding-top: 20px;
            min-height: calc(100vh - 120px) !important; /* Memastikan footer tetap di bawah */
            padding-bottom: 20px;
        }

        .card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }

        /* Hanya berikan efek angkat pada kartu di halaman dashboard utama, bukan form/admin detail */
        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f1f3f9;
            padding: 1.25rem;
        }

        .btn {
            border-radius: var(--btn-radius);
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-sm {
            padding: 5px 12px !important;
            font-size: 0.85rem;
        }

        .form-control, select.form-control {
            border-radius: 10px;
            padding: 12px 15px !important;
            height: auto !important;
            border: 1px solid #e1e5ef;
            color: #4e5e7a;
            font-weight: 500;
        }

        select.form-control {
            padding-right: 45px !important; 
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234e5e7a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            background-size: 1.2em;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }

        .badge {
            border-radius: 8px;
            padding: 6px 12px;
            font-weight: 600;
        }

        /* Table Styling */
        .table thead th {
            background-color: #f1f3f9;
            border-top: none;
            color: #4e5e7a;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
            color: #4e5e7a;
            font-weight: 500;
        }

        .brand-link {
            border-bottom: 1px solid rgba(255,255,255,0.05) !important;
            padding: 1.5rem 1rem !important;
        }

        .brand-text {
            font-weight: 700 !important;
            letter-spacing: 0.5px;
            font-size: 0.95rem !important;
            display: inline-block;
            vertical-align: middle;
            max-width: 170px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .content-header h1 {
            font-weight: 700;
            color: #1e1e2d;
        }

        /* SweetAlert Custom Styling */
        .swal2-popup {
            border-radius: 20px !important;
            font-family: 'Outfit', sans-serif !important;
            padding: 2rem !important;
        }
        .swal2-title {
            font-weight: 700 !important;
            color: #1e1e2d !important;
        }
        .swal2-styled.swal2-confirm {
            border-radius: 10px !important;
            padding: 10px 30px !important;
            font-weight: 600 !important;
        }
        .swal2-icon {
            border-width: 3px !important;
        }

        /* Global Image Preview Modal */
        #imagePreviewModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #imagePreviewModal.active {
            display: flex;
            opacity: 1;
        }

        .preview-container {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .preview-image-wrapper {
            overflow: auto;
            max-width: 100%;
            max-height: 80vh;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            background: #fff;
            cursor: grab;
        }

        .preview-image-wrapper:active {
            cursor: grabbing;
        }

        #previewImage {
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            max-width: 100%;
            display: block;
            margin: auto;
        }

        .preview-controls {
            margin-top: 20px;
            display: flex;
            gap: 15px;
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 25px;
            border-radius: 50px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .preview-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .preview-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .preview-close {
            position: absolute;
            top: -50px;
            right: 0;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .preview-close:hover {
            transform: rotate(90deg);
        }

        /* HR Alert Bell Ring Animation */
        @keyframes bellRing {
            0%, 100% { transform: rotate(0deg); }
            10%       { transform: rotate(15deg); }
            20%       { transform: rotate(-12deg); }
            30%       { transform: rotate(10deg); }
            40%       { transform: rotate(-8deg); }
            50%       { transform: rotate(0deg); }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        {{-- Navbar --}}
        @include('layouts.partials.navbar')

        {{-- Main Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Content Wrapper --}}
        <div class="content-wrapper">
            {{-- Content Header (Page header) --}}
            <section class="content-header">
                <div class="container-fluid">
                    @yield('content-header')
                </div>
            </section>

            {{-- Main content --}}
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        {{-- Footer --}}
        @include('layouts.partials.footer')

        {{-- Control Sidebar (Right sidebar) --}}
        <aside class="control-sidebar control-sidebar-dark">
            {{-- Control sidebar content goes here --}}
        </aside>
    </div>

    {{-- Global Image Preview Modal --}}
    <div id="imagePreviewModal">
        <div class="preview-container animate__animated animate__zoomIn animate__faster">
            <span class="preview-close" onclick="closeImagePreview()">&times;</span>
            <div class="preview-image-wrapper">
                <img id="previewImage" src="" alt="Preview">
            </div>
            <div class="preview-controls">
                <button class="preview-btn" onclick="zoomImage(0.2)" title="Zoom In"><i class="fas fa-search-plus"></i></button>
                <button class="preview-btn" onclick="zoomImage(-0.2)" title="Zoom Out"><i class="fas fa-search-minus"></i></button>
                <button class="preview-btn" onclick="rotateImage()" title="Rotate"><i class="fas fa-sync-alt"></i></button>
                <button class="preview-btn" id="downloadPreview" title="Download"><i class="fas fa-download"></i></button>
            </div>
        </div>
    </div>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    {{-- Bootstrap 4 --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- AdminLTE 3 --}}
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    {{-- Custom Scripts --}}
    <script>
        // CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    @stack('scripts')

    {{-- SweetAlert Global Handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: 'var(--primary-color)',
                    timer: 2500,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef233c',
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Data Tidak Valid',
                    html: `
                        <ul style="text-align: left;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Perbaiki Data'
                });
            @endif

            // Global Delete Confirmation
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Global Logout Confirmation
            $(document).on('click', '.btn-logout', function(e) {
                e.preventDefault();
                let element = $(this);
                let form = element.closest('form');
                
                // If it's a link (like in sidebar) that isn't inside a form
                if (form.length === 0) {
                    form = $('#logout-form-sidebar');
                }

                Swal.fire({
                    title: 'Yakin ingin keluar?',
                    text: "Sesi Anda akan berakhir dan Anda harus login kembali untuk masuk.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Keluar!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Status Cuti/Izin Notification
            @auth
                @if(auth()->user()->pegawai && $leave = auth()->user()->pegawai->isOnLeave())
                    Swal.fire({
                        title: 'Status: Sedang Cuti/Izin',
                        html: `Halo <strong>{{ auth()->user()->name }}</strong>, Anda sedang dalam masa <strong>{{ strtoupper($leave->jenis_izin) }}</strong> hingga tanggal <strong>{{ \Carbon\Carbon::parse($leave->tanggal_selesai)->format('d/m/Y') }}</strong>. <br><br> <small class="text-muted">Selamat beristirahat/bertugas!</small>`,
                        icon: 'info',
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: 'var(--primary-color)',
                        backdrop: `rgba(0,0,123,0.1)`
                    });
                @endif
            @endauth

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                allowClear: true
            });
        });

        // Global Image Preview Logic
        let currentScale = 1;
        let currentRotation = 0;

        function openImagePreview(src) {
            const modal = document.getElementById('imagePreviewModal');
            const img = document.getElementById('previewImage');
            const downloadBtn = document.getElementById('downloadPreview');
            
            img.src = src;
            img.style.transform = `scale(1) rotate(0deg)`;
            currentScale = 1;
            currentRotation = 0;
            
            downloadBtn.onclick = function() {
                const link = document.createElement('a');
                link.href = src;
                link.download = 'lampiran-bukti-' + new Date().getTime();
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            };

            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeImagePreview() {
            const modal = document.getElementById('imagePreviewModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function zoomImage(delta) {
            currentScale = Math.max(0.5, Math.min(3, currentScale + delta));
            updateImageTransform();
        }

        function rotateImage() {
            currentRotation += 90;
            updateImageTransform();
        }

        function updateImageTransform() {
            const img = document.getElementById('previewImage');
            img.style.transform = `scale(${currentScale}) rotate(${currentRotation}deg)`;
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeImagePreview();
        });

        // Close on click outside
        document.getElementById('imagePreviewModal').addEventListener('click', function(e) {
            if (e.target === this) closeImagePreview();
        });
    </script>
</body>
</html>
