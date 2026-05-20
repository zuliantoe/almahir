{{-- Main Footer --}}
<footer class="main-footer border-0 no-print" style="position: relative; z-index: 1000; clear: both; margin-top: 30px; background: #1a1a2e; color: #ccc;">
    {{-- Footer Top Content --}}
    <div class="container-fluid py-4 px-4" style="border-bottom: 1px solid rgba(255,255,255,0.07);">
        <div class="row">
            {{-- Brand & Deskripsi --}}
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <div class="d-flex align-items-center mb-3">
                    <div class="d-flex align-items-center justify-content-center mr-2"
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #4361ee, #4cc9f0); border-radius: 8px;">
                        <i class="fas fa-graduation-cap text-white" style="font-size: 0.9rem;"></i>
                    </div>
                    <div>
                        <span class="font-weight-bold text-white" style="font-size: 1rem; letter-spacing: 0.5px;">SIAKAD ALMAHIR</span>
                    </div>
                </div>
                <p style="font-size: 0.82rem; color: rgba(255,255,255,0.55); line-height: 1.7; margin-bottom: 12px;">
                    Sistem Informasi Akademik Madrasah — platform digital terpadu untuk mengelola seluruh kegiatan akademik, kepesertadidikan, penilaian, kehadiran, keuangan santri, dan manajemen asrama pesantren.
                </p>
                <div class="d-flex" style="gap: 10px;">
                    <span style="background: rgba(67,97,238,0.15); color: #4895ef; border: 1px solid rgba(72,149,239,0.3); padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">
                        <i class="fas fa-shield-alt mr-1"></i>Aman & Terenkripsi
                    </span>
                    <span style="background: rgba(40,167,69,0.15); color: #28a745; border: 1px solid rgba(40,167,69,0.3); padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">
                        <i class="fas fa-server mr-1"></i>Online
                    </span>
                </div>
            </div>

            {{-- Menu Cepat --}}
            <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                <h6 class="font-weight-bold text-white mb-3" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Menu Utama</h6>
                <ul class="list-unstyled" style="margin-bottom: 0;">
                    <li class="mb-2"><a href="{{ route('dashboard') }}" style="color: rgba(255,255,255,0.5); font-size: 0.8rem; text-decoration: none;" class="footer-link"><i class="fas fa-home mr-2" style="width: 14px;"></i>Beranda</a></li>
                    <li class="mb-2"><a href="{{ route('profile.edit') }}" style="color: rgba(255,255,255,0.5); font-size: 0.8rem; text-decoration: none;" class="footer-link"><i class="fas fa-user-circle mr-2" style="width: 14px;"></i>Profil Saya</a></li>
                    @auth
                        @if(auth()->user()->hasRole('SISWA'))
                            <li class="mb-2"><a href="{{ route('penilaiandanpresensi.presensi.siswa.index') }}" style="color: rgba(255,255,255,0.5); font-size: 0.8rem; text-decoration: none;"><i class="fas fa-fingerprint mr-2" style="width: 14px;"></i>Absensi Saya</a></li>
                            <li class="mb-2"><a href="{{ route('keuangan.uangsakus.index') }}" style="color: rgba(255,255,255,0.5); font-size: 0.8rem; text-decoration: none;"><i class="fas fa-wallet mr-2" style="width: 14px;"></i>Uang Saku</a></li>
                        @else
                            <li class="mb-2"><a href="{{ route('akademik.index') }}" style="color: rgba(255,255,255,0.5); font-size: 0.8rem; text-decoration: none;"><i class="fas fa-book mr-2" style="width: 14px;"></i>Akademik</a></li>
                            <li class="mb-2"><a href="{{ route('penilaiandanpresensi.index') }}" style="color: rgba(255,255,255,0.5); font-size: 0.8rem; text-decoration: none;"><i class="fas fa-chart-bar mr-2" style="width: 14px;"></i>Penilaian</a></li>
                        @endif
                    @endauth
                </ul>
            </div>

            {{-- Modul Sistem --}}
            <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                <h6 class="font-weight-bold text-white mb-3" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Modul Sistem</h6>
                <ul class="list-unstyled" style="margin-bottom: 0;">
                    <li class="mb-2"><span style="color: rgba(255,255,255,0.5); font-size: 0.8rem;"><i class="fas fa-calendar-day mr-2" style="width: 14px; color: #4cc9f0;"></i>Jadwal Pelajaran</span></li>
                    <li class="mb-2"><span style="color: rgba(255,255,255,0.5); font-size: 0.8rem;"><i class="fas fa-user-check mr-2" style="width: 14px; color: #28a745;"></i>Absensi & Presensi</span></li>
                    <li class="mb-2"><span style="color: rgba(255,255,255,0.5); font-size: 0.8rem;"><i class="fas fa-graduation-cap mr-2" style="width: 14px; color: #6f42c1;"></i>Penilaian Akademik</span></li>
                    <li class="mb-2"><span style="color: rgba(255,255,255,0.5); font-size: 0.8rem;"><i class="fas fa-quran mr-2" style="width: 14px; color: #e83e8c;"></i>Tahfidz Al-Qur'an</span></li>
                    <li class="mb-2"><span style="color: rgba(255,255,255,0.5); font-size: 0.8rem;"><i class="fas fa-hotel mr-2" style="width: 14px; color: #fd7e14;"></i>Manajemen Asrama</span></li>
                    <li class="mb-2"><span style="color: rgba(255,255,255,0.5); font-size: 0.8rem;"><i class="fas fa-wallet mr-2" style="width: 14px; color: #20c997;"></i>Keuangan Santri</span></li>
                </ul>
            </div>

            {{-- Informasi --}}
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <h6 class="font-weight-bold text-white mb-3" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Informasi Sistem</h6>
                <div class="mb-3">
                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-map-marker-alt mt-1 mr-3" style="color: #4cc9f0; min-width: 14px;"></i>
                        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">Pondok Pesantren ALMAHIR, Indonesia</span>
                    </div>
                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-code-branch mt-1 mr-3" style="color: #4cc9f0; min-width: 14px;"></i>
                        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">Versi Sistem: <span class="text-white font-weight-bold">v1.0</span> &bull; Laravel 11</span>
                    </div>
                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-clock mt-1 mr-3" style="color: #4cc9f0; min-width: 14px;"></i>
                        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);" id="footer-time">-</span>
                    </div>
                </div>
                <div class="p-3 rounded" style="background: rgba(67,97,238,0.1); border: 1px solid rgba(67,97,238,0.2);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle mr-2" style="color: #4895ef;"></i>
                        <span style="font-size: 0.75rem; color: rgba(255,255,255,0.6);">Jika mengalami kendala teknis, hubungi <strong class="text-white">Tim IT / Tata Usaha</strong> sekolah.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer Bottom --}}
    <div class="container-fluid py-3 px-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
                <span style="font-size: 0.78rem; color: rgba(255,255,255,0.4);">
                    &copy; {{ date('Y') }} <strong style="color: rgba(255,255,255,0.7);">SIAKAD ALMAHIR</strong> &mdash; Sistem Informasi Akademik Madrasah. Hak Cipta Dilindungi.
                </span>
            </div>
            <div class="col-md-6 text-center text-md-right">
                <span style="font-size: 0.78rem; color: rgba(255,255,255,0.35);">
                    Dibuat dengan <i class="fas fa-heart mx-1" style="color: #e83e8c;"></i> untuk kemajuan pendidikan Islam
                </span>
            </div>
        </div>
    </div>
</footer>

<style>
    .main-footer a:hover {
        color: #4cc9f0 !important;
        transition: color 0.2s;
    }
</style>
<script>
    (function() {
        function updateFooterTime() {
            const el = document.getElementById('footer-time');
            if (!el) return;
            const now = new Date();
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const d = days[now.getDay()];
            const dd = String(now.getDate()).padStart(2,'0');
            const mm = months[now.getMonth()];
            const yyyy = now.getFullYear();
            const hh = String(now.getHours()).padStart(2,'0');
            const min = String(now.getMinutes()).padStart(2,'0');
            el.textContent = `${d}, ${dd} ${mm} ${yyyy} — ${hh}:${min} WIB`;
        }
        updateFooterTime();
        setInterval(updateFooterTime, 60000);
    })();
</script>
