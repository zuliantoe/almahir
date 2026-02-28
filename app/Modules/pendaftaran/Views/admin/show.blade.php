<div class="container">

<h2>Detail Pendaftaran</h2>

<p><b>Nama:</b> {{ $pendaftaran->nama_lengkap }}</p>

<p><b>NISN:</b> {{ $pendaftaran->nisn }}</p>

<p><b>Tempat Lahir:</b> {{ $pendaftaran->tempat_lahir }}</p>

<p><b>No HP:</b> {{ $pendaftaran->no_hp }}</p>

<p><b>Email:</b> {{ $pendaftaran->email }}</p>

<p><b>Status:</b> {{ $pendaftaran->status }}</p>

<a href="/pendaftaran/admin/pendaftaran" class="btn btn-view">
Kembali
</a>

</div>