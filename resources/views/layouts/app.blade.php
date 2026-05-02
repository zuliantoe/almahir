<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIAKAD') - {{ config('app.name', 'SIAKAD') }}</title>

    {{-- Google Fonts: Inter untuk Typografi Modern & Premium --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Font Awesome 5 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    {{-- AdminLTE 3 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    {{-- Premium UI Styles --}}
    <link rel="stylesheet" href="{{ asset('css/premium-ui.css') }}">

    {{-- Custom Styles --}}
    @stack('styles')
    
    <style>
        /* Typography System */
        body, h1, h2, h3, h4, h5, h6, p, a, span, button, input, select, textarea, .nav-link {
            font-family: 'Inter', sans-serif !important;
        }

        /* Custom styles for SIAKAD */
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #007bff;
            box-shadow: 0 4px 6px -1px rgba(0, 123, 255, 0.2), 0 2px 4px -1px rgba(0, 123, 255, 0.1);
        }
        
        .content-wrapper {
            min-height: calc(100vh - 57px - 57px);
        }
        
        .card-title {
            font-weight: 600;
        }
        
        /* Code snippet styling for UI Guide */
        .code-snippet {
            background: #2d3748;
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 0.375rem;
            font-family: 'Fira Code', 'Consolas', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
        }
        
        .code-snippet .tag { color: #63b3ed; }
        .code-snippet .attr { color: #fbd38d; }
        .code-snippet .value { color: #68d391; }
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
                    timer: 3000,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    position: 'center',
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#d33',
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
