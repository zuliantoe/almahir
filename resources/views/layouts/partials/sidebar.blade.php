{{-- Main Sidebar Container --}}
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    {{-- Brand Logo --}}
    <a href="{{ url('/') }}" class="brand-link">
        <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" 
             alt="SIAKAD Logo" 
             class="brand-image img-circle elevation-3" 
             style="opacity: .8">
        <span class="brand-text font-weight-light"><strong>SI</strong>AKAD</span>
    </a>

    {{-- Sidebar --}}
    <div class="sidebar">
        {{-- Sidebar user panel --}}
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Auth::check() && Auth::user()->avatar_url ? Auth::user()->avatar_url : 'https://ui-avatars.com/api/?name=G&color=fff&background=6c757d' }}" 
                     class="img-circle elevation-2" 
                     alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name ?? 'Guest' }}</a>
            </div>
        </div>

        {{-- Sidebar Menu --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                {{-- Dashboard - Available to all --}}
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- 
                |--------------------------------------------------------------------------
                | DATA MASTER (SUPER_ADMIN, STAF_TU)
                |--------------------------------------------------------------------------
                --}}
                @if(Auth::check() && (Auth::user()->hasRole(['SUPER_ADMIN', 'STAF_TU'])))
                <li class="nav-header">DATA MASTER</li>
                
                {{-- Data Siswa --}}
                <li class="nav-item">
                    <a href="{{ route('siswa.index') }}" class="nav-link {{ request()->is('siswa*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Data Siswa</p>
                    </a>
                </li>
                
                {{-- Data Guru --}}
                <li class="nav-item">
                    <a href="{{ route('guru.index') }}" class="nav-link {{ request()->is('guru*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>Data Guru</p>
                    </a>
                </li>

                {{-- Data Wali Murid --}}
                <li class="nav-item">
                    <a href="{{ route('walimurid.index') }}" class="nav-link {{ request()->is('walimurid*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Wali Murid</p>
                    </a>
                </li>

                {{-- Data Kelas --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-door-open"></i>
                        <p>
                            Data Kelas
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Kelas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Wali Kelas</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- 
                |--------------------------------------------------------------------------
                | AKADEMIK (SUPER_ADMIN, GURU)
                |--------------------------------------------------------------------------
                --}}
                @if(Auth::check() && (Auth::user()->hasRole(['SUPER_ADMIN', 'GURU'])))
                <li class="nav-header">AKADEMIK</li>
                
                {{-- Jadwal Pelajaran --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Jadwal Pelajaran</p>
                    </a>
                </li>
                
                {{-- Nilai --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            Nilai
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Input Nilai</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rekap Nilai</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                {{-- Absensi --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-check"></i>
                        <p>Absensi</p>
                    </a>
                </li>
                @endif

                {{-- 
                |--------------------------------------------------------------------------
                | KEUANGAN (SUPER_ADMIN, KEUANGAN)
                |--------------------------------------------------------------------------
                --}}
                @if(Auth::check() && (Auth::user()->hasRole(['SUPER_ADMIN', 'KEUANGAN'])))
                <li class="nav-header">KEUANGAN</li>
                
                {{-- Pembayaran --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>
                            Pembayaran
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>SPP</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Biaya Lain</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                {{-- Laporan Keuangan --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Laporan Keuangan</p>
                    </a>
                </li>
                @endif

                {{-- 
                |--------------------------------------------------------------------------
                | PENGATURAN (SUPER_ADMIN only)
                |--------------------------------------------------------------------------
                --}}
                @if(Auth::check() && Auth::user()->hasRole('SUPER_ADMIN'))
                <li class="nav-header">PENGATURAN</li>
                
                {{-- Manajemen User --}}
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>
                
                {{-- Roles & Permissions --}}
                <li class="nav-item">
                    <a href="{{ route('rolepermission.index') }}" class="nav-link {{ request()->is('rolepermission*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Roles & Permissions</p>
                    </a>
                </li>
                
                {{-- Konfigurasi Sistem --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Konfigurasi Sistem</p>
                    </a>
                </li>
                @endif

                {{-- 
                |--------------------------------------------------------------------------
                | DEVELOPER (Development only)
                |--------------------------------------------------------------------------
                --}}
                @if(config('app.debug'))
                <li class="nav-header">DEVELOPER</li>
                
                {{-- UI Guide --}}
                <li class="nav-item">
                    <a href="{{ url('/dev/ui-guide') }}" class="nav-link {{ request()->is('dev/ui-guide') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-palette"></i>
                        <p>UI Style Guide</p>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
