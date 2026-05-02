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
    
    {{-- Custom Styles --}}
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
            margin-bottom: 5px;
            padding: 10px 15px;
            font-weight: 500;
        }

        .nav-sidebar .nav-link.active {
            background-color: var(--primary-color) !important;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .content-wrapper {
            background-color: #f8f9fc;
            padding-top: 20px;
        }

        .card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f1f3f9;
            padding: 1.25rem;
        }

        .btn {
            border-radius: var(--btn-radius);
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
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
            letter-spacing: 1px;
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

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    {{-- Bootstrap 4 --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- AdminLTE 3 --}}
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
        });
    </script>
</body>
</html>
