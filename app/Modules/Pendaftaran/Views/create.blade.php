<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        color: #1e293b;
        min-height: 100vh;
        margin: 0;
        padding: 0;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }

    .container {
        max-width: 800px;
        margin: 60px auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05), 0 15px 25px -10px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .form-header {
    display: flex;
    flex-direction: column;
    align-items: center;    
    gap: 10px;
    margin: 30px 0;
    padding: 20px 0;
    border-bottom: 2px solid #f1f5f9;
}

    .form-header img {
        height: 60px;
        width: auto;
    }

    h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    @media (max-width: 600px) {
        .form-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .form-header img {
            height: 50px;
        }
        h2 {
            font-size: 22px;
        }
    }

    h3 {
        margin-top: 40px;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 8px;
        margin-bottom: 20px;
        letter-spacing: -0.2px;
    }

    h4 {
        font-size: 15px;
        font-weight: 600;
        color: #475569;
        margin-top: 15px;
        margin-bottom: 10px;
    }

    label {
        font-weight: 600;
        display: block;
        margin-top: 20px;
        font-size: 13px;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 12px 16px;
        margin-top: 8px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-family: inherit;
        font-size: 14px;
        color: #0f172a;
        background-color: #f8fafc;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-sizing: border-box;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #4f46e5;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    button {
        margin-top: 25px;
        padding: 14px;
        width: 100%;
        border: none;
        border-radius: 10px;
        font-family: inherit;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .btn-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: #fff;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3), 0 4px 6px -2px rgba(79, 70, 229, 0.2);
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
        border-radius: 20px;
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
        font-size: 12px;
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
        color: #4f46e5;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 8px;
        font-size: 15px;
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

    /* Responsive preview grid */
    @media (max-width: 600px) {
        .preview-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Success Alert Styling (Stunning, Beautiful & Glowing) */
    .success-alert {
        display: flex;
        align-items: center;
        background: rgba(240, 253, 244, 0.95);
        backdrop-filter: blur(10px);
        border: 1.5px solid rgba(74, 222, 128, 0.4);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 35px;
        box-shadow: 0 15px 30px -10px rgba(22, 163, 74, 0.12), 
                    0 10px 20px -15px rgba(22, 163, 74, 0.08), 
                    inset 0 1px 0 rgba(255, 255, 255, 0.8);
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
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 0 5px rgba(240, 253, 244, 1);
        animation: pulseGlow 2.5s infinite;
        margin-right: 20px;
        flex-shrink: 0;
    }

    @keyframes pulseGlow {
        0% { box-shadow: 0 0 0 0px rgba(34, 197, 94, 0.5); }
        70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0px rgba(34, 197, 94, 0); }
    }

    .success-alert-icon {
        color: #10b981;
        width: 28px;
        height: 28px;
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
        margin: 0 0 6px 0;
        color: #065f46;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -0.2px;
    }

    .success-alert-content p {
        margin: 0;
        color: #065f46;
        font-size: 13.5px;
        line-height: 1.6;
        font-weight: 500;
    }

    .success-alert-close {
        background: rgba(209, 250, 229, 0.6);
        border: none;
        color: #065f46;
        font-size: 18px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 15px;
        transition: all 0.2s ease;
        margin-top: 0;
        flex-shrink: 0;
    }

    .success-alert-close:hover {
        background: #d1fae5;
        transform: scale(1.1);
    }
</style>


<div class="container">

    <div class="form-header">
        <img src="{{ asset('logo.png') }}" alt="Logo PPQITA">
        <h2>Form Pendaftaran Siswa Baru</h2>
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

        <h3>Data Siswa</h3>

        <label>NISN</label>
        <input type="text" name="nisn">
        <small id="error-nisn" class="error"></small>

        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap">
        <small id="error-nama_lengkap" class="error"></small>

        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir">
        <small id="error-tempat_lahir" class="error"></small>

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir">
        <small class="mini-text" style="color: #64748b; font-weight: 500; display: block; margin-top: 4px;">Calon siswa wajib berusia antara 13 sampai 17 tahun.</small>
        <small id="error-tanggal_lahir" class="error"></small>

        <label>Jenis Kelamin</label>
        <select name="jenis_kelamin">
            <option value="L">Laki laki</option>
            <option value="P">Perempuan</option>
        </select>
        <small id="error-jenis_kelamin" class="error"></small>

        <label>Berat Badan</label>
        <input type="number" name="berat_badan">
        <small id="error-berat_badan" class="error"></small>

        <label>Tinggi Badan</label>
        <input type="number" name="tinggi_badan">
        <small id="error-tinggi_badan" class="error"></small>

        <label>Riwayat Sakit</label>
        <textarea name="riwayat_sakit"></textarea>
        <small class="mini-text">" - " jika tidak ada</small>
        <small id="error-riwayat_sakit" class="error"></small>


        <h3>Alamat</h3>

        <label>Kelurahan</label>
        <input type="text" name="kelurahan">
        <small id="error-kelurahan" class="error"></small>

        <label>Kecamatan</label>
        <input type="text" name="kecamatan">
        <small id="error-kecamatan" class="error"></small>

        <label>Kota</label>
        <input type="text" name="kota">
        <small id="error-kota" class="error"></small>

        <label>Provinsi</label>
        <input type="text" name="provinsi">
        <small id="error-provinsi" class="error"></small>

        <label>Alamat</label>
        <textarea name="alamat"></textarea>
        <small id="error-alamat" class="error"></small>


        <h3>Data Orang Tua</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4>Data Ayah</h4>
                <label>Nama Ayah</label>
                <input type="text" name="nama_ayah">
                <small id="error-nama_ayah" class="error"></small>

                <label>Pekerjaan Ayah</label>
                <input type="text" name="pekerjaan_ayah">
                <small id="error-pekerjaan_ayah" class="error"></small>

                <label>No HP Ayah</label>
                <input type="text" name="no_hp_ayah">
                <small id="error-no_hp_ayah" class="error"></small>

                <label>Alamat Ayah</label>
                <textarea name="alamat_ayah" rows="2"></textarea>
                <small id="error-alamat_ayah" class="error"></small>
            </div>
            <div>
                <h4>Data Ibu</h4>
                <label>Nama Ibu</label>
                <input type="text" name="nama_ibu">
                <small id="error-nama_ibu" class="error"></small>

                <label>Pekerjaan Ibu</label>
                <input type="text" name="pekerjaan_ibu">
                <small id="error-pekerjaan_ibu" class="error"></small>

                <label>No HP Ibu</label>
                <input type="text" name="no_hp_ibu">
                <small id="error-no_hp_ibu" class="error"></small>

                <label>Alamat Ibu</label>
                <textarea name="alamat_ibu" rows="2"></textarea>
                <small id="error-alamat_ibu" class="error"></small>
            </div>
        </div>

        <label>Email</label>
        <input type="email" name="email">
        <small id="error-email" class="error"></small>


        <button type="button" onclick="openModal()" class="btn-primary">
            Daftar
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
