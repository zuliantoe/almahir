<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: #f1f5f9;
        background-image: 
            radial-gradient(#cbd5e1 1.2px, transparent 1.2px), 
            radial-gradient(#cbd5e1 1.2px, #f1f5f9 1.2px);
        background-size: 24px 24px;
        background-position: 0 0, 12px 12px;
        color: #1e293b;
        min-height: 100vh;
        margin: 0;
        padding: 0;
        line-height: 1.5;
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
        max-width: 1000px;
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
        padding: 8px 20px;
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

    .login-btn:active {
        transform: translateY(0);
    }

    /* Main Container & Layout */
    .container {
        max-width: 900px;
        margin: 40px auto;
        background: #ffffff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
    }

    .form-title-wrapper {
        text-align: center;
        margin-bottom: 35px;
    }

    .form-title-wrapper h2 {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.5px;
    }

    .form-title-wrapper p {
        color: #64748b;
        font-size: 14px;
        margin-top: 8px;
        font-weight: 500;
    }

    /* Form Card Sections */
    .form-section {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    }

    .form-section h3 {
        margin-top: 0;
        font-size: 17px;
        font-weight: 700;
        color: #1e3a8a;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-icon {
        width: 4px;
        height: 18px;
        background: #3b82f6;
        border-radius: 2px;
        display: inline-block;
    }

    /* Grid System for Fields */
    .form-row {
        display: flex;
        gap: 20px;
    }

    .form-col {
        flex: 1;
        min-width: 0;
    }

    .form-col.full-width {
        flex: none;
        width: 100%;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-group label {
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        color: #0f172a;
        background-color: #f8fafc;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #3b82f6;
        background-color: #ffffff;
        box-shadow: 0 0 0 3.5px rgba(59, 130, 246, 0.12);
    }

    textarea {
        resize: vertical;
        min-height: 90px;
    }

    /* Parent Cards (Ayah & Ibu Side-by-side) */
    .parent-cards-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 15px;
    }

    .parent-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .parent-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .parent-card-header.ayah {
        background: #f0f7ff;
        border-left: 4px solid #3b82f6;
    }

    .parent-card-header.ibu {
        background: #fdf2f8;
        border-left: 4px solid #ec4899;
    }

    .parent-card-header h4 {
        margin: 0;
        font-size: 13.5px;
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .parent-card-body {
        padding: 20px;
    }

    /* Buttons */
    button {
        padding: 14px;
        width: 100%;
        border: none;
        border-radius: 8px;
        font-family: inherit;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #fff;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
    }

    .btn-secondary {
        background: #64748b;
        color: #fff;
        margin-top: 10px;
    }

    .btn-secondary:hover {
        background: #475569;
        transform: translateY(-2px);
    }

    /* Modal Design */
    #confirmModal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: #fff;
        padding: 35px;
        border-radius: 16px;
        width: 650px;
        max-width: 90%;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.2);
        animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes modalPop {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .error {
        color: #ef4444;
        font-size: 12px;
        font-weight: 500;
        margin-top: 6px;
        display: block;
    }

    .mini-text {
        font-size: 11px;
        color: #64748b;
        margin-top: 6px;
        display: block;
    }

    /* Success Card & Confirmation Styling */
    .success-card {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .preview-section {
        margin-bottom: 24px;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 20px;
        background: #f8fafc;
        text-align: left;
    }

    .preview-section-title {
        margin-top: 0;
        margin-bottom: 16px;
        color: #2563eb;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 8px;
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 24px;
    }

    .preview-item {
        font-size: 13px;
        line-height: 1.6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 6px;
    }

    .preview-item strong {
        color: #64748b;
        font-weight: 600;
        margin-right: 10px;
    }

    .preview-item span {
        color: #0f172a;
        font-weight: 600;
        text-align: right;
    }

    /* Success Alert Styling */
    .success-alert {
        display: flex;
        align-items: center;
        background: rgba(240, 253, 244, 0.95);
        backdrop-filter: blur(10px);
        border: 1.5px solid rgba(74, 222, 128, 0.4);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 35px;
        box-shadow: 0 15px 30px -10px rgba(22, 163, 74, 0.08);
        position: relative;
        animation: slideDownSpring 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes slideDownSpring {
        from { opacity: 0; transform: translateY(-20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .success-alert-icon-wrapper {
        background: #dcfce7;
        border-radius: 50%;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulseGlow 2.5s infinite;
        margin-right: 16px;
        flex-shrink: 0;
    }

    @keyframes pulseGlow {
        0% { box-shadow: 0 0 0 0px rgba(34, 197, 94, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0px rgba(34, 197, 94, 0); }
    }

    .success-alert-icon {
        color: #10b981;
        width: 24px;
        height: 24px;
    }

    .success-alert-icon svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .success-alert-content {
        flex-grow: 1;
        text-align: left;
    }

    .success-alert-content h5 {
        margin: 0 0 4px 0;
        color: #065f46;
        font-size: 15px;
        font-weight: 800;
    }

    .success-alert-content p {
        margin: 0;
        color: #065f46;
        font-size: 13px;
        line-height: 1.5;
        font-weight: 500;
    }

    .success-alert-close {
        background: rgba(209, 250, 229, 0.6);
        border: none;
        color: #065f46;
        font-size: 18px;
        cursor: pointer;
        padding: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 12px;
        transition: all 0.2s ease;
        margin-top: 0;
        flex-shrink: 0;
    }

    .success-alert-close:hover {
        background: #d1fae5;
        transform: scale(1.05);
    }

    /* Responsiveness Grid */
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

        .container {
            margin: 20px;
            padding: 24px;
        }

        .form-row {
            flex-direction: column;
            gap: 0;
        }

        .parent-cards-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .preview-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Top Header Banner matching user image -->
<div class="header-banner">
    <div class="header-container">
        <div class="header-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('logo.png') }}" alt="Logo PPQITA">
            </a>
        </div>
        <div class="header-nav">
            <a href="{{ url('/') }}">Home</a>
            <a href="#">Blogs</a>
            <a href="#">Profile</a>
            <a href="#">Akademik</a>
            <a href="{{ url('/pendaftaran') }}" class="active">PPDB</a>
            <a href="#">Kontak</a>
        </div>
        <div class="header-action">
            <a href="{{ url('/login') }}" class="login-btn">Login</a>
        </div>
    </div>
</div>

<div class="container">

    <div class="form-title-wrapper">
        <h2>Pendaftaran Calon Siswa Baru</h2>
        <p>Silakan isi formulir di bawah ini dengan data yang lengkap dan valid.</p>
    </div>

    @if(session('success'))
        <div class="success-alert" id="successAlert">
            <div class="success-alert-icon-wrapper">
                <div class="success-alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div class="success-alert-content">
                <h5>✨ Data Pendaftaran Berhasil Dikirim! ✨</h5>
                <p>Terima kasih banyak! Data pendaftaran calon siswa telah aman terkirim ke sistem kami. Silakan pantau nomor telepon Anda dan email secara berkala untuk info jadwal seleksi selanjutnya.</p>
            </div>
            <button type="button" class="success-alert-close" onclick="closeSuccessAlert()">
                &times;
            </button>
        </div>
    @endif

    <form id="pendaftaranForm" method="POST" action="{{ url('/pendaftaran') }}">
        @csrf

        <!-- SECTION 1: Data Calon Siswa -->
        <div class="form-section">
            <h3><span class="section-icon"></span>Data Calon Siswa</h3>

            <div class="form-row">
                <div class="form-col full-width">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap">
                        <small id="error-nama_lengkap" class="error"></small>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label>NISN</label>
                        <input type="text" name="nisn">
                        <small id="error-nisn" class="error"></small>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin">
                            <option value="L">Laki laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <small id="error-jenis_kelamin" class="error"></small>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir">
                        <small id="error-tempat_lahir" class="error"></small>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir">
                        <small class="mini-text">Calon siswa wajib berusia antara 13 sampai 17 tahun.</small>
                        <small id="error-tanggal_lahir" class="error"></small>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label>Berat Badan (kg)</label>
                        <input type="number" name="berat_badan">
                        <small id="error-berat_badan" class="error"></small>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label>Tinggi Badan (cm)</label>
                        <input type="number" name="tinggi_badan">
                        <small id="error-tinggi_badan" class="error"></small>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col full-width">
                    <div class="form-group">
                        <label>Riwayat Sakit</label>
                        <textarea name="riwayat_sakit"></textarea>
                        <small class="mini-text">" - " jika tidak ada</small>
                        <small id="error-riwayat_sakit" class="error"></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Alamat Tinggal -->
        <div class="form-section">
            <h3><span class="section-icon"></span>Alamat Tinggal</h3>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label>Kelurahan</label>
                        <input type="text" name="kelurahan">
                        <small id="error-kelurahan" class="error"></small>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <input type="text" name="kecamatan">
                        <small id="error-kecamatan" class="error"></small>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label>Kota / Kabupaten</label>
                        <input type="text" name="kota">
                        <small id="error-kota" class="error"></small>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi">
                        <small id="error-provinsi" class="error"></small>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col full-width">
                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat"></textarea>
                        <small id="error-alamat" class="error"></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Data Orang Tua / Wali -->
        <div class="form-section">
            <h3><span class="section-icon"></span>Data Orang Tua / Wali</h3>

            <div class="parent-cards-grid">
                <!-- Data Ayah -->
                <div class="parent-card">
                    <div class="parent-card-header ayah">
                        <h4>Data Ayah</h4>
                    </div>
                    <div class="parent-card-body">
                        <div class="form-group">
                            <label>Nama Ayah</label>
                            <input type="text" name="nama_ayah">
                            <small id="error-nama_ayah" class="error"></small>
                        </div>

                        <div class="form-group">
                            <label>Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah">
                            <small id="error-pekerjaan_ayah" class="error"></small>
                        </div>

                        <div class="form-group">
                            <label>No HP Ayah</label>
                            <input type="text" name="no_hp_ayah">
                            <small id="error-no_hp_ayah" class="error"></small>
                        </div>

                        <div class="form-group">
                            <label>Alamat Ayah</label>
                            <textarea name="alamat_ayah" rows="2"></textarea>
                            <small id="error-alamat_ayah" class="error"></small>
                        </div>
                    </div>
                </div>

                <!-- Data Ibu -->
                <div class="parent-card">
                    <div class="parent-card-header ibu">
                        <h4>Data Ibu</h4>
                    </div>
                    <div class="parent-card-body">
                        <div class="form-group">
                            <label>Nama Ibu</label>
                            <input type="text" name="nama_ibu">
                            <small id="error-nama_ibu" class="error"></small>
                        </div>

                        <div class="form-group">
                            <label>Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu">
                            <small id="error-pekerjaan_ibu" class="error"></small>
                        </div>

                        <div class="form-group">
                            <label>No HP Ibu</label>
                            <input type="text" name="no_hp_ibu">
                            <small id="error-no_hp_ibu" class="error"></small>
                        </div>

                        <div class="form-group">
                            <label>Alamat Ibu</label>
                            <textarea name="alamat_ibu" rows="2"></textarea>
                            <small id="error-alamat_ibu" class="error"></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col full-width">
                    <div class="form-group">
                        <label>Email Wali</label>
                        <input type="email" name="email">
                        <small id="error-email" class="error"></small>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" onclick="openModal()" class="btn-primary">
            Daftar Sekarang
        </button>

    </form>
</div>




<!-- MODAL -->
<div id="confirmModal">
    <div class="modal-content">

        <h3>Konfirmasi Data</h3>

        <div id="previewContent"></div>

        <button onclick="submitFinal()" class="btn-success">
            Ya, kirim data
        </button>

        <button onclick="closeModal()" class="btn-secondary">
            Periksa lagi
        </button>

    </div>
</div>



<script>
    const form = document.getElementById("pendaftaranForm");


    // ================= VALIDASI REQUIRED =================

    function validateRequired() {
        let valid = true;

        form.querySelectorAll("input,select,textarea").forEach(el => {

            if (el.name && el.name != "_token") {
                if (!el.value.trim()) {
                    showError(el.name, "Wajib diisi");
                    valid = false;
                } else clearError(el.name);
            }

        });

        // Validasi umur secara eksplisit saat submit
        const tglLahirEl = form.querySelector("[name=tanggal_lahir]");
        if (tglLahirEl && tglLahirEl.value) {
            let year = new Date(tglLahirEl.value).getFullYear();
            let now = new Date().getFullYear();
            let umur = now - year;

            if (umur < 13 || umur > 17) {
                showError("tanggal_lahir", "Umur harus 13-17 tahun");
                valid = false;
            } else {
                clearError("tanggal_lahir");
            }
        }

        return valid;
    }


    // ================= VALIDASI NISN =================

    document.querySelector("[name=nisn]").addEventListener("blur", async function() {

        let val = this.value;

        if (!val) return;

        let res = await fetch(`/pendaftaran/cek-nisn/${val}`);
        let data = await res.json();

        if (data.exists)
            showError("nisn", "NISN sudah terdaftar");
        else
            clearError("nisn");

    });


    // ================= VALIDASI EMAIL =================

    document.querySelector("[name=email]").addEventListener("blur", async function() {

        let val = this.value;

        if (!val) return;

        let res = await fetch(`/pendaftaran/cek-email/${val}`);
        let data = await res.json();

        if (data.exists)
            showError("email", "Email sudah digunakan");
        else
            clearError("email");

    });


    // ================= VALIDASI UMUR =================

    document.querySelector("[name=tanggal_lahir]").addEventListener("change", function() {

        let year = new Date(this.value).getFullYear();
        let now = new Date().getFullYear();
        let umur = now - year;

        if (umur < 13 || umur > 17)
            showError("tanggal_lahir", "Umur harus 13-17 tahun");
        else
            clearError("tanggal_lahir");

    });


    // ================= MODAL =================

    function openModal() {

        if (!validateRequired()) return;

        if (document.querySelectorAll(".error:not(:empty)").length > 0) {
            alert("Perbaiki error dulu sebelum mengirim data.");
            return;
        }

        const getVal = (name) => {
            const el = form.querySelector(`[name="${name}"]`);
            return el ? el.value.trim() : '-';
        };

        const getSelectText = (name) => {
            const el = form.querySelector(`[name="${name}"]`);
            if (!el) return '-';
            return el.options[el.selectedIndex].text;
        };

        let html = `
            <div class="preview-section">
                <h4 class="preview-section-title">Data Siswa</h4>
                <div class="preview-grid">
                    <div class="preview-item"><strong>NISN</strong> <span>${getVal('nisn')}</span></div>
                    <div class="preview-item"><strong>Nama Lengkap</strong> <span>${getVal('nama_lengkap')}</span></div>
                    <div class="preview-item"><strong>Tempat Lahir</strong> <span>${getVal('tempat_lahir')}</span></div>
                    <div class="preview-item"><strong>Tanggal Lahir</strong> <span>${getVal('tanggal_lahir')}</span></div>
                    <div class="preview-item"><strong>Jenis Kelamin</strong> <span>${getSelectText('jenis_kelamin')}</span></div>
                    <div class="preview-item"><strong>Berat Badan</strong> <span>${getVal('berat_badan')} kg</span></div>
                    <div class="preview-item"><strong>Tinggi Badan</strong> <span>${getVal('tinggi_badan')} cm</span></div>
                    <div class="preview-item" style="grid-column: span 2; display: block; border-bottom: none;">
                        <strong style="display: block; margin-bottom: 4px;">Riwayat Sakit</strong>
                        <span style="display: block; text-align: left; color: #334155; font-size: 13px;">${getVal('riwayat_sakit') || '-'}</span>
                    </div>
                </div>
            </div>

            <div class="preview-section">
                <h4 class="preview-section-title">Alamat Tinggal</h4>
                <div class="preview-grid">
                    <div class="preview-item"><strong>Kelurahan</strong> <span>${getVal('kelurahan')}</span></div>
                    <div class="preview-item"><strong>Kecamatan</strong> <span>${getVal('kecamatan')}</span></div>
                    <div class="preview-item"><strong>Kota/Kabupaten</strong> <span>${getVal('kota')}</span></div>
                    <div class="preview-item"><strong>Provinsi</strong> <span>${getVal('provinsi')}</span></div>
                    <div class="preview-item" style="grid-column: span 2; display: block; border-bottom: none;">
                        <strong style="display: block; margin-bottom: 4px;">Alamat Lengkap</strong>
                        <span style="display: block; text-align: left; color: #334155; font-size: 13px;">${getVal('alamat')}</span>
                    </div>
                </div>
            </div>

            <div class="preview-section">
                <h4 class="preview-section-title">Data Orang Tua / Wali</h4>
                <div class="preview-grid">
                    <div class="preview-item"><strong>Nama Ayah</strong> <span>${getVal('nama_ayah')}</span></div>
                    <div class="preview-item"><strong>Nama Ibu</strong> <span>${getVal('nama_ibu')}</span></div>
                    <div class="preview-item"><strong>Pekerjaan Ayah</strong> <span>${getVal('pekerjaan_ayah')}</span></div>
                    <div class="preview-item"><strong>Pekerjaan Ibu</strong> <span>${getVal('pekerjaan_ibu')}</span></div>
                    <div class="preview-item"><strong>No HP Ayah</strong> <span>${getVal('no_hp_ayah')}</span></div>
                    <div class="preview-item"><strong>No HP Ibu</strong> <span>${getVal('no_hp_ibu')}</span></div>
                    <div class="preview-item" style="grid-column: span 2; display: block; border-bottom: none; margin-bottom: 10px;">
                        <strong style="display: block; margin-bottom: 4px;">Alamat Ayah</strong>
                        <span style="display: block; text-align: left; color: #334155; font-size: 13px;">${getVal('alamat_ayah')}</span>
                    </div>
                    <div class="preview-item" style="grid-column: span 2; display: block; border-bottom: none; margin-bottom: 10px;">
                        <strong style="display: block; margin-bottom: 4px;">Alamat Ibu</strong>
                        <span style="display: block; text-align: left; color: #334155; font-size: 13px;">${getVal('alamat_ibu')}</span>
                    </div>
                    <div class="preview-item" style="grid-column: span 2; border-bottom: none; background: #e0e7ff; padding: 6px 10px; border-radius: 6px;">
                        <strong style="color: #4f46e5;">Email Wali</strong>
                        <span style="color: #4f46e5; font-weight: 700;">${getVal('email')}</span>
                    </div>
                </div>
            </div>
        `;

        document.getElementById("previewContent").innerHTML = html;
        document.getElementById("confirmModal").style.display = "flex";

    }

    function closeModal() {
        document.getElementById("confirmModal").style.display = "none";
    }

    function submitFinal() {
        form.submit();
    }

    function closeSuccessAlert() {
        const alert = document.getElementById("successAlert");
        if (alert) {
            alert.style.transition = "opacity 0.4s ease, transform 0.4s ease";
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-10px)";
            setTimeout(() => alert.remove(), 400);
        }
    }


    // ================= HELPER =================

    function showError(name, msg) {
        let el = document.getElementById("error-" + name);
        if (el) el.innerText = msg;
    }

    function clearError(name) {
        let el = document.getElementById("error-" + name);
        if (el) el.innerText = "";
    }

    function format(str) {
        return str.replaceAll("_", " ")
            .replace(/\b\w/g, l => l.toUpperCase());
    }
</script>
