# Dokumentasi Modul Penilaian & Presensi SIAKAD ALMAHIRA

Modul Penilaian & Presensi adalah sistem hilir yang mengelola seluruh data capaian santri, baik dari sisi akademik, tahfidz, maupun kedisiplinan (kehadiran). Modul ini sangat bergantung pada struktur yang ditetapkan di Modul Akademik.

---

## 1. Struktur Modul & Fungsi Controller

Modul ini mengelola proses input data harian hingga output akhir berupa Raport Digital. Berikut adalah daftar Controller dan fungsinya:

| Controller | Fungsi Utama |
| :--- | :--- |
| `DashboardController` | Menampilkan statistik ringkas (rata-rata nilai, persentase kehadiran) untuk Admin, Guru, dan Siswa. |
| `PenilaianAkademikController` | Mengelola input nilai harian, UTS, dan UAS. Melakukan kalkulasi nilai akhir dan mencetak Raport Digital dengan format resmi. |
| `PenilaianTahfidzController` | Mengelola catatan hafalan santri (surat, ayat, nilai) dan progres setoran hafalan. |
| `PresensiController` | Mengelola absensi santri berdasarkan Jadwal Pelajaran. Mendukung scan QR (jika tersedia) atau input manual oleh guru pengampu. |
| `IzinSakitController` | Mengelola surat izin dan keterangan sakit santri. Data ini otomatis terintegrasi ke dalam rekap absensi di raport. |

---

## 2. Struktur Database & Model

Modul ini memiliki beberapa Model utama yang menyimpan data transaksi harian:

### Data Penilaian
*   **`PenilaianAkademik`**: Menyimpan nilai santri per mata pelajaran. Memiliki field `jenis_nilai` (Harian, UTS, UAS) dan `tahunajaran_id`.
*   **`PenilaianTahfidz`**: Menyimpan riwayat setoran hafalan (Surat Awal - Surat Akhir) beserta kualitas bacaan.
*   **`RaportCatatan`**: Menyimpan narasi/deskripsi saran dari Wali Kelas yang akan tampil di lembar raport.

### Data Kehadiran
*   **`Presensi`**: Catatan kehadiran santri per jam pelajaran. Berelasi ke `JadwalPelajaran`. Status: `Hadir`, `Izin`, `Sakit`, `Alpha`.
*   **`IzinSakit`**: Modul khusus untuk mengunggah bukti/keterangan izin. Memiliki field `tipe` (Izin/Sakit) dan `status_persetujuan`.

---

## 3. Keterkaitan dengan Modul Lain (Integration)

Modul ini merupakan konsumen data (downstream) dari beberapa modul berikut:

### A. Kaitan dengan Modul Akademik (Wajib)
*   **Tahun Ajaran**: Seluruh nilai dan presensi wajib difilter berdasarkan `tahunajaran_id` yang aktif.
*   **Rombel & Siswa**: Penilaian hanya bisa diinput untuk siswa yang terdaftar aktif dalam Rombel tertentu.
*   **Jadwal Pelajaran**: Presensi dilakukan berdasarkan plotting jadwal yang dibuat di modul Akademik. Tanpa jadwal, guru tidak bisa melakukan presensi.

### B. Kaitan dengan Modul Guru
*   **Guru Pengampu**: Hanya guru yang terdaftar di `JadwalPelajaran` yang memiliki hak akses untuk menginput nilai dan presensi di kelas tersebut.
*   **Wali Kelas**: Memiliki hak akses khusus untuk memberikan catatan (saran) dan mencetak raport untuk kelas binaannya.

---

## 4. Panduan Penggunaan & Pengembangan

### 1. Menampilkan Tabel Nilai di Raport
Data nilai dikelompokkan berdasarkan kategori mapel (Umum/Diniyyah) yang diambil dari relasi `mataPelajaran->kategori`.
```php
// Contoh pemanggilan di Controller
$scores = PenilaianAkademik::with(['mataPelajaran.kategori'])
    ->where('siswa_id', $id)
    ->get()
    ->groupBy('mataPelajaran.kategori.kategori');
```

### 2. Integrasi Izin/Sakit ke Absensi
Saat menghitung jumlah ketidakhadiran di raport, sistem akan menjumlahkan data dari tabel `presensi` dan memvalidasi durasi dari tabel `izin_sakit`.

### 3. Perhitungan Rerata Kelas
Rerata kelas dihitung secara dinamis dengan mengambil nilai seluruh siswa dalam satu `kelas_id` pada mata pelajaran yang sama, kemudian dibagi jumlah siswa.

---

*Dokumentasi ini dibuat untuk memastikan konsistensi pengembangan fitur penilaian di SIAKAD Almahira.*
