{{-- Main Sidebar Container --}}
@php
    $isAcademicRole = Auth::check() && (Auth::user()->hasRole('GURU') || Auth::user()->hasRole('SISWA'));
    $sidebarClass = $isAcademicRole ? 'sidebar-dark-info' : 'sidebar-dark-primary';
    $homeUrl = url('/');
    if (Auth::check()) {
        if (Auth::user()->hasRole('SISWA')) {
            $homeUrl = route('penilaiandanpresensi.index');
        } elseif (Auth::user()->hasRole('GURU')) {
            $homeUrl = route('penilaiandanpresensi.index');
        } elseif (Auth::user()->hasRole('WALI_MURID')) {
            $homeUrl = route('walimurid.portal.dashboard');
        }
    }
@endphp
<aside class="main-sidebar {{ $sidebarClass }} elevation-4 no-print">
    {{-- Brand Logo --}}
    <a href="{{ $homeUrl }}" class="brand-link">
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
                @if($isAcademicRole)
                    <small class="text-white-50">
                        @if(Auth::user()->hasRole('GURU'))
                            <i class="fas fa-chalkboard-teacher mr-1"></i>Guru / Pengajar
                        @else
                            <i class="fas fa-user-graduate mr-1"></i>Santri / Siswa
                        @endif
                    </small>
                @endif
            </div>
        </div>

        {{-- Sidebar Menu --}}
        {{-- Sidebar Menu --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column pb-5" data-widget="treeview" role="menu" data-accordion="false">
                
                @php
                    $isInAsramaModule = request()->is('manajemenasetdanasrama*');
                @endphp

                @if($isInAsramaModule)
                    {{-- 
                    |--------------------------------------------------------------------------
                    | KHUSUS MENU MANAJEMEN ASET & ASRAMA
                    |--------------------------------------------------------------------------
                    --}}
                    <li class="nav-item">
                        <a href="{{ url('/') }}" class="nav-link bg-danger mb-3">
                            <i class="nav-icon fas fa-arrow-left"></i>
                            <p>Kembali ke Menu Utama</p>
                        </a>
                    </li>

                    <li class="nav-header text-uppercase">Aset & Asrama</li>
                    
                    <li class="nav-item">
                        <a href="{{ route('manajemenasetdanasrama.index') }}" class="nav-link {{ request()->routeIs('manajemenasetdanasrama.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard Asrama</p>
                        </a>
                    </li>

                    <li class="nav-item has-treeview {{ request()->is('manajemenasetdanasrama/kamar*') || request()->is('manajemenasetdanasrama/penghuni*') || request()->is('manajemenasetdanasrama/jadwal-piket*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('manajemenasetdanasrama/kamar*') || request()->is('manajemenasetdanasrama/penghuni*') || request()->is('manajemenasetdanasrama/jadwal-piket*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                                Pengelolaan Asrama
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/kamar*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Kamar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.penghuni.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/penghuni*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Penghuni</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/jadwal-piket*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Jadwal Piket</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ request()->is('manajemenasetdanasrama/aset*') || request()->is('manajemenasetdanasrama/pengajuan*') || request()->is('manajemenasetdanasrama/persetujuan*') || request()->is('manajemenasetdanasrama/pengadaan*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('manajemenasetdanasrama/aset*') || request()->is('manajemenasetdanasrama/pengajuan*') || request()->is('manajemenasetdanasrama/persetujuan*') || request()->is('manajemenasetdanasrama/pengadaan*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>
                                Manajemen Aset
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/aset*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Daftar Aset</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.pengajuan.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/pengajuan*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pengajuan Baru</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.persetujuan.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/persetujuan*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Persetujuan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.pengadaan.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/pengadaan*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Log Pengadaan</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ request()->is('manajemenasetdanasrama/kerusakan*') || request()->is('manajemenasetdanasrama/pemeliharaan*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('manajemenasetdanasrama/kerusakan*') || request()->is('manajemenasetdanasrama/pemeliharaan*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>
                                Perawatan Aset
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.kerusakan.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/kerusakan*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Laporan Kerusakan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.pemeliharaan.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/pemeliharaan*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pemeliharaan</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('manajemenasetdanasrama.trash.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama/trash*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-trash-restore"></i>
                            <p>Sampah / Trash</p>
                        </a>
                    </li>

                @else
                    {{-- 
                    |--------------------------------------------------------------------------
                    | MENU GLOBAL (HANYA MUNCUL JIKA TIDAK DI MODUL ASRAMA)
                    |--------------------------------------------------------------------------
                    --}}

                    {{-- Dashboard (Hide for Wali Murid) --}}
                    @if(Auth::check() && !Auth::user()->hasRole('WALI_MURID'))
                    <li class="nav-item">
                        <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') || request()->is('akademik') || request()->is('guru/dashboard') || request()->is('siswa/dashboard') || request()->is('penilaiandanpresensi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    @endif

                    {{-- Shortcut Modul Asrama --}}
                    @if(Auth::check() && Auth::user()->hasRole(['SUPER_ADMIN']))
                    <li class="nav-item mt-2 mb-2">
                        <a href="{{ route('manajemenasetdanasrama.index') }}" class="nav-link bg-info">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Modul Aset & Asrama</p>
                        </a>
                    </li>
                    @endif

                    {{-- PORTAL SISWA --}}
                    @if(Auth::check() && Auth::user()->ref_type === \Modules\Siswa\Models\Siswa::class)
                    <li class="nav-header">PORTAL SISWA</li>
                    
                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.presensi.siswa.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/presensi/siswa*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-fingerprint"></i>
                            <p>Absensi Hari Ini</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.izinsakit.siswa.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/izinsakit/siswa*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope-open-text"></i>
                            <p>Izin & Sakit</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaianakademik*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>Nilai Akademik</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaiantahfidz*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-quran"></i>
                            <p>Nilai Tahfidz</p>
                        </a>
                    </li>
                    @endif
                    
                    {{-- PORTAL WALI MURID --}}
                    @if(Auth::check() && Auth::user()->hasRole('WALI_MURID'))
                    <li class="nav-header">PORTAL WALI MURID</li>
                    
                    <li class="nav-item">
                        <a href="{{ route('walimurid.portal.dashboard') }}" class="nav-link {{ request()->is('walimurid/portal/dashboard*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Dashboard Wali</p>
                        </a>
                    </li>
                    @endif

                    {{-- DYNAMIC MODULE MENUS --}}
                    @isset($moduleMenus)
                        @foreach($moduleMenus as $section)
                            @if($section['header'] != 'MANAJEMEN ASET & ASRAMA') {{-- Sembunyikan header asrama dari menu global --}}
                                <li class="nav-header">{{ $section['header'] }}</li>

                                @foreach($section['items'] as $item)
                                    @if(!empty($item['children']))
                                        {{-- Treeview menu item with children --}}
                                        @php
                                            $isTreeOpen = false;
                                            foreach ($item['children'] as $child) {
                                                if (!empty($child['match']) && request()->is($child['match'])) {
                                                    $isTreeOpen = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <li class="nav-item has-treeview {{ $isTreeOpen ? 'menu-open' : '' }}">
                                            <a href="{{ $item['url'] ?? '#' }}" class="nav-link {{ $isTreeOpen ? 'active' : '' }}">
                                                <i class="nav-icon {{ $item['icon'] ?? 'far fa-circle' }}"></i>
                                                <p>
                                                    {{ $item['label'] }}
                                                    <i class="fas fa-angle-left right"></i>
                                                </p>
                                            </a>
                                            <ul class="nav nav-treeview">
                                                @foreach($item['children'] as $child)
                                                    @php
                                                        $childUrl = '#';
                                                        if (!empty($child['route'])) {
                                                            try { $childUrl = route($child['route']); } catch (\Exception $e) { $childUrl = '#'; }
                                                        } elseif (!empty($child['url'])) {
                                                            $childUrl = $child['url'];
                                                        }
                                                        $childActive = !empty($child['match']) && request()->is($child['match']);
                                                    @endphp
                                                    <li class="nav-item">
                                                        <a href="{{ $childUrl }}" class="nav-link {{ $childActive ? 'active' : '' }}">
                                                            <i class="nav-icon {{ $child['icon'] ?? 'far fa-circle' }}"></i>
                                                            <p>{{ $child['label'] }}</p>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        {{-- Single menu item --}}
                                        @php
                                            $itemUrl = '#';
                                            if (!empty($item['route'])) {
                                                try { $itemUrl = route($item['route']); } catch (\Exception $e) { $itemUrl = '#'; }
                                            } elseif (!empty($item['url'])) {
                                                $itemUrl = $item['url'];
                                            }
                                            $itemActive = !empty($item['match']) && request()->is($item['match']);
                                        @endphp
                                        <li class="nav-item">
                                            <a href="{{ $itemUrl }}" class="nav-link {{ $itemActive ? 'active' : '' }}">
                                                <i class="nav-icon {{ $item['icon'] ?? 'far fa-circle' }}"></i>
                                                <p>{{ $item['label'] }}</p>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endisset

                    {{-- Data Wali Murid --}}
                    @if(Auth::check() && Auth::user()->hasRole('SUPER_ADMIN'))
                    <li class="nav-item">
                        <a href="{{ route('walimurid.index') }}" class="nav-link {{ request()->is('walimurid*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data Wali Murid</p>
                        </a>
                    </li>
                    @endif

                {{-- 
                |--------------------------------------------------------------------------
                | PENILAIAN & PRESENSI (SUPER_ADMIN, GURU)
                |--------------------------------------------------------------------------
                --}}
                @if(Auth::check() && (Auth::user()->hasRole(['SUPER_ADMIN', 'GURU'])))
                <li class="nav-header">PENILAIAN & PRESENSI</li>
                
                <li class="nav-item">
                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaianakademik*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-graduation-cap"></i>
                        <p>Penilaian Akademik</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaiantahfidz*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-quran"></i>
                        <p>Penilaian Tahfidz</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.raport.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaianakademik/raport*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-print"></i>
                        <p>Cetak Raport</p>
                    </a>
                </li>

                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/presensi') ? 'active' : (request()->is('penilaiandanpresensi/presensi/*') && !request()->is('penilaiandanpresensi/presensi/siswa*') ? 'active' : '') }}">
                            <i class="nav-icon fas fa-user-check"></i>
                            <p>Presensi (Daftar)</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/izinsakit') ? 'active' : (request()->is('penilaiandanpresensi/izinsakit/*') && !request()->is('penilaiandanpresensi/izinsakit/siswa*') ? 'active' : '') }}">
                            <i class="nav-icon fas fa-user-clock"></i>
                            <p>Konfirmasi Izin Sakit</p>
                        </a>
                    </li>
                    @endif

                    {{-- KEUANGAN --}}
                    @if(Auth::check() && (Auth::user()->hasRole(['SUPER_ADMIN', 'KEUANGAN'])))
                    <li class="nav-header">KEUANGAN</li>
                    
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
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Laporan Keuangan</p>
                        </a>
                    </li>
                    @endif

                    {{-- PENGATURAN --}}
                    @if(Auth::check() && Auth::user()->hasRole('SUPER_ADMIN'))
                    <li class="nav-header">PENGATURAN</li>
                    
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('rolepermission.index') }}" class="nav-link {{ request()->is('rolepermission*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Roles & Permissions</p>
                        </a>
                    </li>
                    @endif
                @endif

                {{-- Logout for Guru and Siswa at the bottom --}}
                @if($isAcademicRole)
                <li class="nav-header">AKUN</li>
                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->is('profile*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-circle"></i>
                        <p>Profil Saya</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link btn-logout">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Keluar</p>
                    </a>
                    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
                @endif

                {{-- Keuangan Menu --}}
                @if(Auth::check() && (request()->is('keuangan*') || request()->is('*/keuangan*')))
                    @include('keuangan::partials.menu')
                @endif

            </ul>
        </nav>
    </div>
</aside>
