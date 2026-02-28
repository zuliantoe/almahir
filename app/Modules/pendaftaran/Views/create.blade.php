<h2>Form Pendaftaran Siswa Baru</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('pendaftaran.store') }}">
    @csrf

    <h3>Data Siswa</h3>

    NISN:
    <input type="text" name="nisn"><br><br>

    Nama Lengkap:
    <input type="text" name="nama_lengkap"><br><br>

    Tempat Lahir:
    <input type="text" name="tempat_lahir"><br><br>

    Tanggal Lahir:
    <input type="date" name="tanggal_lahir"><br><br>

    Jenis Kelamin:
    <select name="jenis_kelamin">
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
    </select>
    <br><br>

    Berat Badan:
    <input type="number" step="0.01" name="berat_badan"><br><br>

    Tinggi Badan:
    <input type="number" step="0.01" name="tinggi_badan"><br><br>

    Riwayat Sakit:
    <textarea name="riwayat_sakit"></textarea><br><br>

    <h3>Alamat</h3>

    Kelurahan:
    <input type="text" name="kelurahan"><br><br>

    Kecamatan:
    <input type="text" name="kecamatan"><br><br>

    Kota:
    <input type="text" name="kota"><br><br>

    Provinsi:
    <input type="text" name="provinsi"><br><br>

    Alamat Lengkap:
    <textarea name="alamat"></textarea><br><br>

    <h3>Data Orang Tua</h3>

    Nama Ayah:
    <input type="text" name="nama_ayah"><br><br>

    Pekerjaan Ayah:
    <input type="text" name="pekerjaan_ayah"><br><br>

    No HP:
    <input type="text" name="no_hp"><br><br>

    Email:
    <input type="email" name="email"><br><br>

    <button type="submit">Daftar</button>
</form>
