{{-- Main Navbar --}}
<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 shadow-sm" style="height: 65px;">
    {{-- Left navbar links --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" title="Lipat Menu">
                <i class="fas fa-bars text-primary" style="font-size: 1.2rem;"></i>
            </a>
        </li>
        {{-- Live Date Display --}}
        <li class="nav-item d-none d-md-inline-block ml-2">
            <div class="nav-link text-dark font-weight-bold" style="cursor: default;">
                <i class="far fa-calendar-alt mr-2 text-info"></i>
                <span id="live-date">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </li>
    </ul>

    {{-- Right navbar links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Search form --}}
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button" title="Cari Data">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Cari info di sistem..." aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        {{-- Notifications Dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Notifikasi Sistem">
                <i class="far fa-bell" style="font-size: 1.1rem;"></i>
                <span class="badge badge-warning navbar-badge" style="top: 5px; right: 5px; font-size: 0.6rem;">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-lg mt-2">
                <span class="dropdown-item dropdown-header font-weight-bold">3 Notifikasi Masuk</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-soft p-2 rounded-circle mr-3">
                            <i class="fas fa-envelope text-primary"></i>
                        </div>
                        <div>
                            <span class="d-block font-weight-bold text-sm">4 Pesan Baru</span>
                            <small class="text-muted">Diterima 3 menit yang lalu</small>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer text-primary font-weight-bold">Lihat Semua Notifikasi</a>
            </div>
        </li>

        {{-- Fullscreen button --}}
        <li class="nav-item d-none d-sm-inline-block">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Ganti Mode Layar">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        {{-- User Account Dropdown --}}
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
                @auth
                    <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=0D8ABC&color=fff' }}" 
                         class="user-image img-circle elevation-1 border mr-2" 
                         alt="User Image"
                         style="width: 32px; height: 32px; object-fit: cover;">
                    <span class="d-none d-md-inline font-weight-bold text-dark">{{ Auth::user()->name }}</span>
                @else
                    <i class="far fa-user-circle mr-1"></i> Tamu
                @endauth
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-lg mt-2">
                {{-- User Header --}}
                @auth
                    <div class="dropdown-header text-center bg-light py-4 rounded-top">
                        <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=0D8ABC&color=fff' }}" 
                             class="img-circle elevation-2 mb-2" 
                             alt="User Image"
                             style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #fff;">
                        <p class="mb-0 font-weight-bold text-dark h6">{{ Auth::user()->name }}</p>
                        <small class="text-primary font-weight-bold">{{ Auth::user()->primary_role ?? 'Pengguna' }}</small>
                    </div>
                    
                    {{-- Menu Body --}}
                    <div class="dropdown-divider m-0"></div>
                    <div class="p-2">
                        <a href="#" class="dropdown-item rounded py-2">
                            <i class="fas fa-user-cog mr-2 text-primary"></i> Pengaturan Profil
                        </a>
                        <a href="#" class="dropdown-item rounded py-2">
                            <i class="fas fa-key mr-2 text-info"></i> Ganti Password
                        </a>
                    </div>
                    
                    {{-- Footer --}}
                    <div class="dropdown-divider m-0"></div>
                    <div class="p-2 bg-light rounded-bottom">
                        @if(Route::has('logout'))
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-block btn-sm py-2 font-weight-bold">
                                    <i class="fas fa-sign-out-alt mr-1"></i> LOGOUT
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    <div class="p-3 text-center">
                        <p class="text-muted small">Anda belum masuk ke sistem.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-block btn-sm">
                            <i class="fas fa-sign-in-alt mr-1"></i> LOGIN SEKARANG
                        </a>
                    </div>
                @endauth
            </div>
        </li>
    </ul>
</nav>
