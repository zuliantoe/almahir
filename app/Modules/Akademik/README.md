# Dokumentasi Modul Akademik SIAKAD ALMAHIRA

Modul Akademik adalah tulang punggung dari sistem SIAKAD (Sistem Informasi Akademik) yang mengelola seluruh siklus akademik sekolah/pesantren, mulai dari pengaturan kurikulum, pembagian kelas, penjadwalan, hingga proses kenaikan kelas dan kelulusan.

---

## 1. Struktur Modul & Fungsi Controller

Modul ini dibangun dengan arsitektur MVC (Model-View-Controller). Berikut adalah daftar Controller dan fungsinya:

| Controller | Fungsi Utama |
| :--- | :--- |
| `AkademikController` | Mengelola Dashboard Akademik. Menampilkan tampilan berbeda berdasarkan *Role* (Siswa, Guru, atau Admin) beserta statistik dan jadwal hari ini. |
| `TahunAjaranController` | CRUD Tahun Ajaran. Menentukan tahun ajaran dan semester yang sedang aktif (hanya boleh ada 1 yang aktif). |
| `KelasController` | CRUD Master Kelas. (Catatan: Kelas adalah template, sedangkan pelaksanaannya ada di Rombel). |
| `MataPelajaranController` | CRUD Mata Pelajaran beserta klasifikasinya (Kategori Pelajaran). |
| `KategoriPelajaranController`| Mengelola Kategori Mapel (Misal: Muatan Nasional, Muatan Lokal, Kepesantrenan). |
| `MasterKurikulumController` | Mengelola data master kurikulum (Misal: K-13, Kurikulum Merdeka). |
| `KurikulumController` | Memetakan Mata Pelajaran ke dalam Master Kurikulum berdasarkan Tingkat, Kelas, dan Semester beserta jam pelajaran dan KKM. |
| `RombelController` | Mengelola Rombongan Belajar (Kelas Aktual). Memasukkan *Siswa* ke dalam Rombel dan menunjuk *Guru* sebagai Wali Kelas. |
| `JadwalPelajaranController` | Membuat jadwal pelajaran per Rombel, menentukan Hari, Jam, Mata Pelajaran, dan Guru Pengampu. |
| `BebanMengajarController` | (Terkait Jadwal) Mengelola plotting beban mengajar guru per mata pelajaran di suatu rombel. |
| `KalenderAkademikController`| Mengelola agenda/event tahunan (hari libur, ujian, kegiatan sekolah). |
| `JenisKegiatanController` | Mengelola kategori event kalender akademik beserta warnanya. |
| `KenaikanKelasController` | *Workflow* untuk menaikkan/memindahkan siswa dari Rombel lama ke Rombel baru di tahun ajaran berikutnya secara kolektif. |
| `KelulusanController` | *Workflow* untuk meluluskan siswa tingkat akhir dan mengubah status mereka menjadi alumni. |

---

## 2. Struktur Database & Model

Modul ini memiliki beberapa Model Eloquent utama yang saling berelasi:

### Master Data Akademik
*   **`TahunAjaran`**: Menyimpan periode tahun ajaran (misal: 2024/2025) dan semester (Ganjil/Genap). Memiliki field `is_active`.
*   **`Tingkat` & `Jurusan`**: Master data untuk jenjang pendidikan.
*   **`Kelas`**: Master kelas (Misal: 7A, 8B).
*   **`MataPelajaran`**: Master mata pelajaran. Berelasi dengan `KategoriPelajaran`.

### Kurikulum
*   **`Kurikulum`**: Menghubungkan `MasterKurikulum`, `Tingkat`, `Kelas`, dan `MataPelajaran`. Menyimpan `total_jam`, `semester`, dan `kkm`.

### Eksekusi Akademik (Rombel & Jadwal)
*   **`Rombel`**: Rombongan Belajar. 
    *   Berelasi `belongsTo` ke `Kelas` dan `TahunAjaran`.
    *   Berelasi `belongsTo` ke `Guru` (sebagai wali kelas).
    *   Berelasi *Many-to-Many* ke `Siswa` (melalui tabel pivot `rombel_siswa`).
*   **`RombelSiswa`**: Model Pivot/Riwayat yang menyimpan status siswa (`aktif`, `lulus`, `pindah`, `naik`, `tinggal_kelas`) pada suatu Rombel.
*   **`JadwalPelajaran`**: Penjadwalan.
    *   Berelasi ke `Rombel`, `MataPelajaran`, dan `Guru` (Pengampu).

---

## 3. Keterkaitan dengan Modul Lain (Integration)

Modul Akademik sangat bergantung dan terhubung erat dengan modul lain. Berikut adalah daftarnya:

### A. Kaitan dengan Modul Siswa (`Modules\Siswa`)
*   **Relasi**: Model `Rombel` berelasi *Many-to-Many* dengan model `Siswa` melalui tabel `rombel_siswa`.
*   **Penggunaan**: Modul akademik menarik data siswa aktif untuk dimasukkan ke dalam Rombel. Saat kenaikan kelas/kelulusan, data siswa ini dimanipulasi statusnya di tabel pivot.
*   **Dashboard**: Saat user bersatus *SISWA* login, `AkademikController` memanggil `Auth::user()->ref` (mengembalikan objek Siswa) untuk mencari `RombelSiswa` yang aktif, lalu menampilkan Jadwal Pelajaran khusus untuk Rombel siswa tersebut.

### B. Kaitan dengan Modul Guru (`Modules\Guru`)
*   **Relasi Wali Kelas**: `Rombel` `belongsTo` `Guru` (Kolom `guru_id` di tabel rombel).
*   **Relasi Pengampu**: `JadwalPelajaran` `belongsTo` `Guru` (Kolom `guru_id`).
*   **Dashboard**: Saat user berstatus *GURU* login, sistem mengambil id Guru (`Auth::user()->ref->id`) dan memfilter `JadwalPelajaran` untuk menampilkan jadwal mengajar guru tersebut hari ini dan minggu ini.

### C. Kaitan dengan Modul User Manager / RBAC (`App\Models\User`)
*   **Penggunaan**: Menggunakan Spatie Permission / Role untuk validasi akses (`$user->hasRole('GURU')` atau `SISWA`). Model User melakukan *Polymorphic Relation* (`ref_type` dan `ref_id`) ke model Guru/Siswa.

### D. Kaitan dengan Modul Penilaian & Presensi (Hiliran)
*   Modul Penilaian dan Presensi (Absensi) **wajib** mengambil struktur dasar dari Modul Akademik. Contoh: Guru absensi berdasar `JadwalPelajaran`, Penilaian didasarkan pada `Kurikulum` (KKM) dan peserta didik diambil dari relasi `RombelSiswa` yang berstatus `aktif`.

---

## 4. Cara Menghubungkan (Panduan untuk Developer)

Jika Anda membuat modul baru dan perlu mengaitkannya dengan data Akademik, ikuti panduan berikut:

### 1. Mengambil Tahun Ajaran Aktif (Global Scope)
Semua transaksi (Absensi, Pembayaran, Nilai) harus selalu merujuk pada Tahun Ajaran yang aktif.
```php
use App\Modules\Akademik\Models\TahunAjaran;

$tahunAktif = TahunAjaran::aktif()->first(); // Gunakan scope aktif()
$tahunAjaranId = $tahunAktif->id;
```

### 2. Mencari Rombel (Kelas Aktual) Siswa Saat Ini
Jika ingin membuat fitur rapor atau absensi siswa, cari rombel siswa di tahun ajaran aktif:
```php
use App\Modules\Akademik\Models\RombelSiswa;

$rombelSiswa = RombelSiswa::with('rombel')
    ->where('siswa_id', $siswa_id)
    ->where('status', 'aktif') // Hanya ambil yang aktif
    ->whereHas('rombel', function($query) use ($tahunAjaranId) {
        $query->where('tahunajaran_id', $tahunAjaranId);
    })->first();

$rombelAktif = $rombelSiswa->rombel;
```

### 3. Mengambil Jadwal Mengajar Guru
Jika membuat modul rekap mengajar guru:
```php
use App\Modules\Akademik\Models\JadwalPelajaran;

$jadwal = JadwalPelajaran::with(['rombel', 'mataPelajaran'])
    ->where('guru_id', $guru_id)
    ->where('hari', 'Senin')
    ->get();
```

### 4. Menambahkan Event Kalender (Kalender Akademik)
Untuk mengambil daftar hari libur di modul absensi (agar tidak bisa absen di hari libur):
```php
use App\Modules\Akademik\Models\KalenderAkademik;

$hariLibur = KalenderAkademik::whereHas('jenisKegiatan', function($q) {
        $q->where('kategori', 'libur'); // Contoh jika ada flag khusus libur
    })
    ->whereDate('tanggal_awal', '<=', $today)
    ->whereDate('tanggal_akhir', '>=', $today)
    ->exists();
    
if ($hariLibur) {
   // Blokir input absensi
}
```

---

*Dokumentasi ini di-generate secara otomatis untuk membantu pemahaman developer terkait alur data SIAKAD Almahira.*
