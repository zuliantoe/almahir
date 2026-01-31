<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIAKAD') - {{ config('app.name', 'SIAKAD') }}</title>

    {{-- Google Fonts --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    {{-- Font Awesome 5 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    {{-- AdminLTE 3 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    {{-- Custom Styles --}}
    @stack('styles')
    
    <style>
        /* Custom styles for SIAKAD */
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #007bff;
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
</body>
</html>
