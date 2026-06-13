<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPQ Al-Mahir - Pondok Pesantren Qur'an & IT</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f8fafc;
            background-image: 
                radial-gradient(#cbd5e1 1.2px, transparent 1.2px), 
                radial-gradient(#cbd5e1 1.2px, #f8fafc 1.2px);
            background-size: 24px 24px;
            background-position: 0 0, 12px 12px;
            color: #1e293b;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Header Banner matching reference image */
        .header-banner {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            padding: 22px 40px;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.15);
            position: relative;
            overflow: hidden;
        }
        
        .header-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1.5px, transparent 1.5px);
            background-size: 16px 16px;
            opacity: 0.85;
            pointer-events: none;
        }

        .header-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .header-logo {
            display: flex;
            align-items: center;
        }

        .header-logo img {
            height: 52px;
            width: auto;
            display: block;
        }

        .header-nav {
            display: flex;
            gap: 28px;
            align-items: center;
        }

        .header-nav a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 600;
            transition: all 0.2s ease;
            letter-spacing: 0.2px;
        }

        .header-nav a:hover {
            color: #ffffff;
        }

        .header-nav a.active {
            color: #ffffff;
            font-weight: 700;
            position: relative;
        }

        .header-nav a.active::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            right: 0;
            height: 2.5px;
            background: #ffffff;
            border-radius: 2px;
        }

        .header-action {
            display: flex;
            align-items: center;
        }

        .login-btn {
            background: #ffffff;
            color: #2563eb;
            font-weight: 700;
            padding: 8px 22px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            display: inline-block;
        }

        .login-btn:hover {
            background: #f8fafc;
            color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        /* Hero Section */
        .hero-section {
            max-width: 1100px;
            margin: 60px auto 40px auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 50px;
        }

        .hero-content {
            flex: 1;
        }

        .hero-badge {
            background: #dbeafe;
            color: #1e40af;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero-content h1 {
            font-size: 46px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
            margin: 0 0 20px 0;
            letter-spacing: -1px;
        }

        .hero-content h1 span {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            font-size: 17px;
            color: #475569;
            margin: 0 0 35px 0;
            font-weight: 500;
        }

        .hero-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
        }

        .btn-outline {
            border: 2px solid #cbd5e1;
            color: #475569;
            background: transparent;
        }

        .btn-outline:hover {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Feature Section */
        .features-section {
            max-width: 1100px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
            border-color: #bfdbfe;
        }

        .feature-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 12px 0;
        }

        .feature-card p {
            font-size: 14.5px;
            color: #64748b;
            margin: 0;
            line-height: 1.6;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 40px 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #64748b;
            background: #ffffff;
            margin-top: 80px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 900px) {
            .hero-section {
                flex-direction: column;
                margin-top: 40px;
                text-align: center;
                gap: 40px;
            }
            .hero-content h1 {
                font-size: 36px;
            }
            .hero-actions {
                justify-content: center;
            }
            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .header-banner {
                padding: 16px 20px;
            }
            .header-container {
                flex-direction: column;
                gap: 12px;
            }
            .header-nav {
                gap: 16px;
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
            .header-action {
                margin-top: 8px;
            }
        }
    </style>
</head>
<body>

<!-- Header Banner -->
<div class="header-banner">
    <div class="header-container">
        <div class="header-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('logo.png') }}" alt="Logo PPQITA">
            </a>
        </div>
        <div class="header-nav">
            <a href="{{ url('/') }}" class="active">Home</a>
            <a href="#">Blogs</a>
            <a href="#">Profile</a>
            <a href="#">Akademik</a>
            <a href="{{ url('/pendaftaran') }}">PPDB</a>
            <a href="#">Kontak</a>
        </div>
        <div class="header-action">
            <a href="{{ url('/login') }}" class="login-btn">Login</a>
        </div>
    </div>
</div>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-content">
        <div class="hero-badge">PONDOK PESANTREN QUR'AN & IT</div>
        <h1>Mencetak Generasi Huffazh <span>Cerdas Teknologi</span></h1>
        <p>PPQ Al-Mahir mengintegrasikan kurikulum tahfidz Al-Qur'an mutqin dengan keahlian pemrograman modern (Software Engineering, Web/Mobile Dev, & AI) untuk melahirkan pemimpin masa depan yang beriman dan kompeten.</p>
        <div class="hero-actions">
            <a href="{{ url('/pendaftaran') }}" class="btn btn-gradient">Daftar PPDB</a>
            <a href="#" class="btn btn-outline">Pelajari Program</a>
        </div>
    </div>
    <div class="hero-image">
        <!-- Stunning Modern Tech/Quran Inline SVG Illustration -->
        <svg width="400" height="320" viewBox="0 0 400 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="400" height="320" rx="20" fill="white" fill-opacity="0.6"/>
            <!-- Background glow -->
            <circle cx="200" cy="160" r="120" fill="#3b82f6" fill-opacity="0.06"/>
            <circle cx="200" cy="160" r="70" fill="#2563eb" fill-opacity="0.04"/>
            <!-- Laptop screen wrapper -->
            <rect x="70" y="60" width="260" height="170" rx="10" fill="#0f172a" stroke="#e2e8f0" stroke-width="6"/>
            <!-- Keyboard/Base of laptop -->
            <path d="M50 230H350V238C350 244.627 344.627 250 338 250H62C55.3726 250 50 244.627 50 238V230Z" fill="#e2e8f0"/>
            <rect x="170" y="234" width="60" height="6" rx="3" fill="#cbd5e1"/>
            <!-- Islamic star pattern decoration behind the laptop -->
            <path d="M200 10L208.5 25H224L213.5 35L220 50L200 42L180 50L186.5 35L176 25H191.5L200 10Z" fill="#2563eb" fill-opacity="0.1"/>
            <!-- Quran emblem inside laptop screen -->
            <path d="M140 160C140 145 160 135 200 135C240 135 260 145 260 160V195C260 180 240 170 200 170C160 170 140 180 140 195V160Z" fill="#3b82f6" fill-opacity="0.8"/>
            <path d="M200 135V170" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round"/>
            <path d="M140 160C140 145 160 135 200 135V170C160 170 140 180 140 195V160Z" fill="#2563eb" fill-opacity="0.9"/>
            <circle cx="200" cy="152" r="6" fill="#facc15"/>
            <!-- Code text placeholder lines in the background of quran -->
            <rect x="90" y="80" width="80" height="6" rx="3" fill="#38bdf8" fill-opacity="0.5"/>
            <rect x="90" y="92" width="120" height="6" rx="3" fill="#38bdf8" fill-opacity="0.3"/>
            <rect x="90" y="104" width="60" height="6" rx="3" fill="#38bdf8" fill-opacity="0.4"/>
            <rect x="250" y="80" width="60" height="6" rx="3" fill="#38bdf8" fill-opacity="0.4"/>
            <rect x="230" y="92" width="80" height="6" rx="3" fill="#38bdf8" fill-opacity="0.3"/>
        </svg>
    </div>
</div>

<!-- Features Section -->
<div class="features-section">
    <div class="features-grid">
        <!-- Feature 1: Qur'an -->
        <div class="feature-card">
            <div class="feature-icon-wrapper">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10M6 10h10"/></svg>
            </div>
            <h3>Program Tahfidz</h3>
            <p>Fokus hafalan Al-Qur'an mutqin dengan metode setoran intensif, muraja'ah mandiri, tahsin bersanad, serta pemahaman tafsir dasar.</p>
        </div>

        <!-- Feature 2: IT -->
        <div class="feature-card">
            <div class="feature-icon-wrapper">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <h3>Pendidikan IT</h3>
            <p>Mempelajari rekayasa perangkat lunak, pengembangan website modern, aplikasi mobile, algoritma, serta dasar kecerdasan buatan (AI).</p>
        </div>

        <!-- Feature 3: Akhlak -->
        <div class="feature-card">
            <div class="feature-icon-wrapper">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3>Karakter Islami</h3>
            <p>Menanamkan adab dan akhlakul karimah sehari-hari, latihan kepemimpinan (leadership), kemandirian santri, serta kecerdasan sosial.</p>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; {{ date('Y') }} Pondok Pesantren Qur'an & IT Al-Mahir. All rights reserved.</p>
</footer>

</body>
</html>
