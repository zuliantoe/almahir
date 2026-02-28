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
                | DYNAMIC MODULE MENUS
                | Auto-generated from each module's menu.php config file
                |--------------------------------------------------------------------------
                --}}
                @isset($moduleMenus)
                    @foreach($moduleMenus as $section)
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
                                                    <i class="far fa-circle nav-icon"></i>
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
                    @endforeach
                @endisset

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
