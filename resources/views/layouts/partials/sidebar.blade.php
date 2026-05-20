@php
    $isSiswa = Auth::check() && Auth::user()->hasRole('SISWA');
    $isGuru = Auth::check() && Auth::user()->hasRole('GURU');
    $isWali = Auth::check() && Auth::user()->hasRole('WALI_MURID');
    $isStaffOrAdmin = Auth::check() && !($isSiswa || $isGuru || $isWali);

    // Sidebar color class
    $sidebarClass = ($isSiswa || $isGuru) ? 'sidebar-dark-info' : 'sidebar-dark-primary';

    $homeUrl = url('/');
    if (Auth::check()) {
        if ($isWali) {
            $homeUrl = route('walimurid.portal.dashboard');
        }
    }

    // Active module detection for Siswa, Guru, and Admin
    $activeModule = null;
    if ($isSiswa) {
        if (request()->is('akademik*')) {
            $activeModule = 'akademik';
        } elseif (request()->is('penilaiandanpresensi*')) {
            $activeModule = 'penilaiandanpresensi';
        } elseif (request()->is('siswa/asrama*')) {
            $activeModule = 'asrama';
        } elseif (request()->is('keuangan*')) {
            $activeModule = 'keuangan';
        }
    } elseif ($isGuru) {
        if (request()->is('manajemenasetdanasrama*')) {
            $activeModule = 'asrama';
        }
    } elseif ($isStaffOrAdmin) {
        if (request()->is('akademik*')) {
            $activeModule = 'akademik';
        } elseif (request()->is('pegawaimanager*')) {
            $activeModule = 'kepegawaian';
        } elseif (request()->is('absensi*') || request()->is('perizinan*')) {
            $activeModule = 'kehadiran';
        } elseif (request()->is('penilaiandanpresensi*')) {
            $activeModule = 'penilaiandanpresensi';
        } elseif (request()->is('keuangan*')) {
            $activeModule = 'keuangan';
        } elseif (request()->is('manajemenasetdanasrama*')) {
            $activeModule = 'asrama';
        } elseif (request()->is('users*') || request()->is('rolepermission*') || request()->is('role-permission*') || request()->is('roles*')) {
            $activeModule = 'pengaturan';
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
        <span class="brand-text font-weight-light"><strong>SIAKAD</strong> ALMAHIR</span>
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
                <a href="javascript:void(0);" class="d-block">{{ Auth::user()->name ?? 'Guest' }}</a>
                @if(Auth::check())
                    <small class="text-white-50">
                        @if($isGuru)
                            <i class="fas fa-chalkboard-teacher mr-1"></i>Guru / Pengajar
                        @elseif($isSiswa)
                            <i class="fas fa-user-graduate mr-1"></i>Santri / Siswa
                        @elseif($isWali)
                            <i class="fas fa-user-shield mr-1"></i>Wali Murid
                        @else
                            <i class="fas fa-user-cog mr-1"></i>Staf / Admin
                        @endif
                    </small>
                @endif
            </div>
        </div>

        {{-- Sidebar Menu --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column pb-5" data-widget="treeview" role="menu" data-accordion="false">
                
                @if($isGuru)
                    {{-- ========================================== --}}
                    {{-- PORTAL GURU                                --}}
                    {{-- ========================================== --}}
                    @if($activeModule === 'asrama')
                        {{-- 1. TOMBOL KEMBALI KE MENU UTAMA --}}
                        <li class="nav-item">
                            <a href="{{ url('/') }}" class="nav-link bg-danger text-white mb-3" style="border-radius: 12px; box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);">
                                <i class="nav-icon fas fa-arrow-left"></i>
                                <p class="font-weight-bold">Menu Utama</p>
                            </a>
                        </li>

<<<<<<< HEAD
                    <li class="nav-header">MENU GURU</li>
                    
                    {{-- 1. Kaldik --}}
                    <li class="nav-item">
                        <a href="{{ route('akademik.kalender-akademik.index') }}" class="nav-link {{ request()->is('akademik/kalender-akademik*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt text-success"></i>
                            <p>Kaldik</p>
                        </a>
                    </li>
=======
                        {{-- 2. DYNAMIC MODULE SUBMENUS --}}
                        @isset($moduleMenus)
                            @foreach($moduleMenus as $section)
                                @if($section['header'] === 'MANAJEMEN ASET & ASRAMA')
                                    <li class="nav-header">{{ $section['header'] }}</li>
>>>>>>> 4d41c3e (fix and add menu at guru)

                                    @foreach($section['items'] as $item)
                                        @if(!empty($item['children']))
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
                                                <a href="javascript:void(0);" class="nav-link {{ $isTreeOpen ? 'active' : '' }}">
                                                    <i class="nav-icon {{ $item['icon'] ?? 'far fa-circle' }}"></i>
                                                    <p>
                                                        {{ $item['label'] }}
                                                        <i class="fas fa-angle-left right"></i>
                                                    </p>
                                                </a>
                                                <ul class="nav nav-treeview">
                                                    @foreach($item['children'] as $child)
                                                        @php
                                                            $childUrl = 'javascript:void(0);';
                                                            if (!empty($child['route'])) {
                                                                try { $childUrl = route($child['route']); } catch (\Exception $e) { $childUrl = 'javascript:void(0);'; }
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
                                            @php
                                                $itemUrl = 'javascript:void(0);';
                                                if (!empty($item['route'])) {
                                                    try { $itemUrl = route($item['route']); } catch (\Exception $e) { $itemUrl = 'javascript:void(0);'; }
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
                    @else
                        <li class="nav-item">
                            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard Utama</p>
                            </a>
                        </li>

<<<<<<< HEAD
                    {{-- Penilaian Akademik & Tahfidz --}}
                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaianakademik') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-graduation-cap text-purple"></i>
                            <p>Penilaian Akademik</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaiantahfidz*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-quran text-pink"></i>
                            <p>Penilaian Tahfidz</p>
                        </a>
                    </li>

                    {{-- 3. Absensi Siswa --}}
                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/presensi*') && !request()->is('penilaiandanpresensi/presensi/siswa*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-check text-primary"></i>
                            <p>Absensi Siswa</p>
                        </a>
                    </li>
=======
                        <li class="nav-header">MENU GURU</li>
                        
                        {{-- 1. Kaldik --}}
                        <li class="nav-item">
                            <a href="{{ route('guru.kalender-akademik') }}" class="nav-link {{ request()->is('guru/kalender-akademik*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-calendar-alt text-success"></i>
                                <p>Kaldik</p>
                            </a>
                        </li>
>>>>>>> 4d41c3e (fix and add menu at guru)

                        {{-- 2. Jadwal Mengajar --}}
                        <li class="nav-item">
                            <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="nav-link {{ request()->is('akademik/jadwal-pelajaran*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-calendar-day text-info"></i>
                                <p>Jadwal Mengajar</p>
                            </a>
                        </li>

                        {{-- 3. Absensi Siswa --}}
                        <li class="nav-item">
                            <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/presensi*') && !request()->is('penilaiandanpresensi/presensi/siswa*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-check text-primary"></i>
                                <p>Absensi Siswa</p>
                            </a>
                        </li>

                        {{-- 4. Cetak Raport --}}
                        <li class="nav-item">
                            <a href="{{ route('penilaiandanpresensi.penilaianakademik.raport.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaianakademik/raport*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-invoice text-warning"></i>
                                <p>Cetak Raport</p>
                            </a>
                        </li>

<<<<<<< HEAD
                    {{-- 7. Absensi Pegawai --}}
                    <li class="nav-item">
                        <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->is('absensi*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-fingerprint text-info"></i>
                            <p>Absensi Pegawai</p>
                        </a>
                    </li>
=======
                        {{-- 5. Konfirmasi Izin Sakit --}}
                        <li class="nav-item">
                            <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/izinsakit*') && !request()->is('penilaiandanpresensi/izinsakit/siswa*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-envelope-open-text text-danger"></i>
                                <p>Konfirmasi Izin Sakit</p>
                            </a>
                        </li>

                        {{-- 6. Pengajuan Izin --}}
                        <li class="nav-item">
                            <a href="{{ route('perizinan.index') }}" class="nav-link {{ request()->is('perizinan*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-paper-plane text-teal"></i>
                                <p>Pengajuan Izin</p>
                            </a>
                        </li>

                        {{-- 7. Absensi Pegawai --}}
                        <li class="nav-item">
                            <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->is('absensi*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-fingerprint text-purple"></i>
                                <p>Absensi Pegawai</p>
                            </a>
                        </li>

                        {{-- 8. Aset & Asrama --}}
                        <li class="nav-item">
                            <a href="{{ route('manajemenasetdanasrama.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building text-orange"></i>
                                <p>Aset & Asrama</p>
                            </a>
                        </li>
                    @endif
>>>>>>> 4d41c3e (fix and add menu at guru)

                @elseif($isWali)
                    {{-- ========================================== --}}
                    {{-- PORTAL WALI MURID                           --}}
                    {{-- ========================================== --}}
                    <li class="nav-header">PORTAL WALI MURID</li>
                    <li class="nav-item">
                        <a href="{{ route('walimurid.portal.dashboard') }}" class="nav-link {{ request()->is('walimurid/portal/dashboard*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield text-info"></i>
                            <p>Dashboard Wali</p>
                        </a>
                    </li>

                @elseif($isSiswa)
                    {{-- ========================================== --}}
                    {{-- PORTAL SISWA DIRECT VIEW                     --}}
                    {{-- ========================================== --}}
                    <li class="nav-item">
                        <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt text-primary"></i>
                            <p>Dashboard Utama</p>
                        </a>
                    </li>

                    <li class="nav-header">AKADEMIK SANTRI</li>
                    <li class="nav-item">
                        <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="nav-link {{ request()->is('akademik/jadwal-pelajaran*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-day text-info"></i>
                            <p>Jadwal Pelajaran</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('akademik.kalender-akademik.index') }}" class="nav-link {{ request()->is('akademik/kalender-akademik*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt text-success"></i>
                            <p>Kalender Akademik</p>
                        </a>
                    </li>

                    <li class="nav-header">KEHADIRAN & NILAI</li>
                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.presensi.siswa.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/presensi/siswa*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-fingerprint text-info"></i>
                            <p>Absensi Saya</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.izinsakit.siswa.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/izinsakit/siswa*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope-open-text text-warning"></i>
                            <p>Izin & Sakit</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaianakademik*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-graduation-cap text-success"></i>
                            <p>Nilai Akademik</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaiantahfidz*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-quran text-pink"></i>
                            <p>Nilai Tahfidz</p>
                        </a>
                    </li>

                    <li class="nav-header">ASRAMA & PIKET</li>
                    <li class="nav-item">
                        <a href="{{ route('siswa.asrama.jadwal-piket.index') }}" class="nav-link {{ request()->is('siswa/asrama/jadwal-piket*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list text-orange"></i>
                            <p>Jadwal Piket</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('siswa.asrama.kamar.index') }}" class="nav-link {{ request()->is('siswa/asrama/kamar*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hotel text-info"></i>
                            <p>Lihat Kamar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('siswa.asrama.penghuni.index') }}" class="nav-link {{ request()->is('siswa/asrama/penghuni*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users text-success"></i>
                            <p>Data Penghuni</p>
                        </a>
                    </li>

                    <li class="nav-header">KEUANGAN SANTRI</li>
                    <li class="nav-item">
                        <a href="{{ route('keuangan.uangsakus.index') }}" class="nav-link {{ request()->is('keuangan/uangsakus*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-wallet text-teal"></i>
                            <p>Uang Saku Saya</p>
                        </a>
                    </li>

                @else
                    {{-- ========================================== --}}
                    {{-- PORTAL ADMIN & STAFF (WITH MODULE SWITCHER) --}}
                    {{-- ========================================== --}}
                    
                    @if($activeModule)
                        {{-- 1. TOMBOL KEMBALI KE MENU UTAMA --}}
                        <li class="nav-item">
                            <a href="{{ url('/') }}" class="nav-link bg-danger text-white mb-3" style="border-radius: 12px; box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);">
                                <i class="nav-icon fas fa-arrow-left"></i>
                                <p class="font-weight-bold">Menu Utama</p>
                            </a>
                        </li>

                        {{-- 2. DYNAMIC MODULE SUBMENUS --}}
                        @if(in_array($activeModule, ['akademik', 'kepegawaian', 'kehadiran', 'asrama']))
                            @isset($moduleMenus)
                                @foreach($moduleMenus as $section)
                                    @php
                                        $matchHeader = false;
                                        if ($activeModule === 'akademik' && $section['header'] === 'SISTEM AKADEMIK') {
                                            $matchHeader = true;
                                        } elseif ($activeModule === 'kepegawaian' && $section['header'] === 'KEPEGAWAIAN') {
                                            $matchHeader = true;
                                        } elseif ($activeModule === 'kehadiran' && $section['header'] === 'KEHADIRAN') {
                                            $matchHeader = true;
                                        } elseif ($activeModule === 'asrama' && $section['header'] === 'MANAJEMEN ASET & ASRAMA') {
                                            $matchHeader = true;
                                        }
                                    @endphp

                                    @if($matchHeader)
                                        <li class="nav-header">{{ $section['header'] }}</li>

                                        @foreach($section['items'] as $item)
                                            @if(!empty($item['children']))
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
                                                    <a href="javascript:void(0);" class="nav-link {{ $isTreeOpen ? 'active' : '' }}">
                                                        <i class="nav-icon {{ $item['icon'] ?? 'far fa-circle' }}"></i>
                                                        <p>
                                                            {{ $item['label'] }}
                                                            <i class="fas fa-angle-left right"></i>
                                                        </p>
                                                    </a>
                                                    <ul class="nav nav-treeview">
                                                        @foreach($item['children'] as $child)
                                                            @php
                                                                $childUrl = 'javascript:void(0);';
                                                                if (!empty($child['route'])) {
                                                                    try { $childUrl = route($child['route']); } catch (\Exception $e) { $childUrl = 'javascript:void(0);'; }
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
                                                @php
                                                    $itemUrl = 'javascript:void(0);';
                                                    if (!empty($item['route'])) {
                                                        try { $itemUrl = route($item['route']); } catch (\Exception $e) { $itemUrl = 'javascript:void(0);'; }
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
                        @endif

                {{-- 
                |--------------------------------------------------------------------------
                | PENILAIAN & PRESENSI (SUPER_ADMIN, GURU)
                |--------------------------------------------------------------------------
                --}}
                @if(Auth::check() && (Auth::user()->hasRole(['SUPER_ADMIN', 'GURU'])))
                <li class="nav-header">PENILAIAN & PRESENSI</li>
                
                <li class="nav-item">
                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi/penilaianakademik') ? 'active' : '' }}">
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

                        {{-- 4. KEUANGAN MODULE VIEW --}}
                        @if($activeModule === 'keuangan' && Auth::user()->hasRole(['SUPER_ADMIN', 'KEUANGAN']))
                            @include('keuangan::partials.menu')
                        @endif

                        {{-- 5. PENGATURAN MODULE VIEW --}}
                        @if($activeModule === 'pengaturan' && Auth::user()->hasRole('SUPER_ADMIN'))
                            <li class="nav-header">PENGATURAN</li>
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users-cog text-primary"></i>
                                    <p>Manajemen User</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('rolepermission.index') }}" class="nav-link {{ request()->is('rolepermission*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-shield text-info"></i>
                                    <p>Roles & Permissions</p>
                                </a>
                            </li>
                        @endif

                    @else
                        {{-- ================================================== --}}
                        {{-- MENU UTAMA (HANYA MUNCUL DI DASHBOARD)             --}}
                        {{-- ================================================== --}}
                        <li class="nav-item">
                            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard Utama</p>
                            </a>
                        </li>

                        <li class="nav-header">MODUL UTAMA</li>

                        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'STAFF']))
                            <li class="nav-item">
                                <a href="{{ route('akademik.index') }}" class="nav-link {{ request()->is('akademik*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap text-info"></i>
                                    <p>Modul Akademik</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('pegawaimanager.dashboard') }}" class="nav-link {{ request()->is('pegawaimanager*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chalkboard-teacher text-success"></i>
                                    <p>Modul Kepegawaian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->is('absensi*') || request()->is('perizinan*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-fingerprint text-warning"></i>
                                    <p>Modul Kehadiran</p>
                                </a>
                            </li>
                            <li class="nav-item has-treeview {{ request()->is('siswa*') || request()->is('guru*') || request()->is('walimurid*') || request()->is('users*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->is('siswa*') || request()->is('guru*') || request()->is('walimurid*') || request()->is('users*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users text-primary"></i>
                                    <p>
                                        Modul User
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('siswa.index') }}" class="nav-link {{ request()->is('siswa*') && !request()->is('siswa/asrama*') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Data Siswa</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('guru.index') }}" class="nav-link {{ request()->is('guru*') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Data Guru</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('walimurid.index') }}" class="nav-link {{ request()->is('walimurid*') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Data Wali Murid</p>
                                        </a>
                                    </li>
                                    @if(Auth::user()->hasRole('SUPER_ADMIN'))
                                    <li class="nav-item">
                                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Semua Akun</p>
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'GURU']))
                            <li class="nav-item">
                                <a href="{{ route('penilaiandanpresensi.index') }}" class="nav-link {{ request()->is('penilaiandanpresensi*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-check text-primary"></i>
                                    <p>Penilaian & Presensi</p>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'KEUANGAN']))
                            <li class="nav-item">
                                <a href="{{ route('keuangan.index') }}" class="nav-link {{ request()->is('keuangan*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-money-bill-wave text-teal"></i>
                                    <p>Modul Keuangan</p>
                                </a>
                            </li>
                        @endif





                        {{-- Asrama di menu utama admin --}}
                        @if(Auth::user()->hasRole(['SUPER_ADMIN', 'STAF_TU']))
                            <li class="nav-item">
                                <a href="{{ route('manajemenasetdanasrama.index') }}" class="nav-link {{ request()->is('manajemenasetdanasrama*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-building text-orange"></i>
                                    <p>Aset & Asrama</p>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->hasRole('SUPER_ADMIN'))
                            <li class="nav-header">PENGATURAN</li>
                            <li class="nav-item">
                                <a href="{{ route('rolepermission.index') }}" class="nav-link {{ request()->is('rolepermission*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-shield text-secondary"></i>
                                    <p>Roles & Permissions</p>
                                </a>
                            </li>
                        @endif
                    @endif
                @endif

            {{-- PROFILE & LOGOUT SECTION FOR ALL ROLES --}}
                @if(Auth::check())
                    <li class="nav-header">AKUN</li>
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->is('profile*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-circle text-muted"></i>
                            <p>Profil Saya</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('logout') }}" class="nav-link btn-logout">
                            <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                            <p>Keluar</p>
                        </a>
                        <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
