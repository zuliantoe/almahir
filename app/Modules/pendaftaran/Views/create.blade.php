<style>
    /* tetap sama style kamu */
    body {
        font-family: Arial;
        background: #f4f6f9;
    }

    .container {
        max-width: 800px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    h2 {
        text-align: center;
    }

    h3 {
        margin-top: 30px;
        border-bottom: 2px solid #eee;
        padding-bottom: 5px;
    }

    label {
        font-weight: 600;
        display: block;
        margin-top: 15px;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }

    button {
        margin-top: 25px;
        padding: 12px;
        width: 100%;
        border: none;
        border-radius: 8px;
        font-size: 16px;
    }

    .btn-primary {
        background: #4f46e5;
        color: #fff;
    }

    .btn-success {
        background: #16a34a;
        color: #fff;
    }

    .btn-secondary {
        background: #6b7280;
        color: #fff;
        margin-top: 10px;
    }

    #confirmModal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        width: 600px;
        max-height: 80vh;
        overflow: auto;
    }

    .error {
        color: red;
        font-size: 12px;
    
    }
    .mini-text {
        font-size: 11px;
        color: #555;
        
    }
</style>


<div class="container">

    <h2>Form Pendaftaran Siswa Baru</h2>

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

        <label>Nama Ayah</label>
        <input type="text" name="nama_ayah">
        <small id="error-nama_ayah" class="error"></small>

        <label>Pekerjaan Ayah</label>
        <input type="text" name="pekerjaan_ayah">
        <small id="error-pekerjaan_ayah" class="error"></small>

        <label>No HP</label>
        <input type="text" name="no_hp">
        <small id="error-no_hp" class="error"></small>

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

        if (umur < 14 || umur > 18)
            showError("tanggal_lahir", "Umur harus 14-18 tahun");
        else
            clearError("tanggal_lahir");

    });


    // ================= MODAL =================

    function openModal() {

        if (!validateRequired()) return;

        if (document.querySelectorAll(".error:not(:empty)").length > 0) {
            alert("Perbaiki error dulu");
            return;
        }

        let data = new FormData(form);

        let html = "<table>";

        data.forEach((v, k) => {
            if (k != "_token")
                html += `<tr><td>${format(k)}</td><td>${v}</td></tr>`;
        });

        html += "</table>";

        document.getElementById("previewContent").innerHTML = html;
        document.getElementById("confirmModal").style.display = "flex";

    }

    function closeModal() {
        document.getElementById("confirmModal").style.display = "none";
    }

    function submitFinal() {
        form.submit();
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
