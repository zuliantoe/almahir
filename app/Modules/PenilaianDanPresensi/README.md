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

## 4. Alur Sistem Keseluruhan
 
### A. Alur Presensi & Kedisiplinan
1.  **Pengumpulan Data**: Data absensi masuk melalui 3 pintu (Scan Mandiri Siswa, Scan Kartu oleh Guru, atau Input Manual Admin).
2.  **Validasi Waktu**: Sistem membandingkan waktu absensi dengan `JadwalPelajaran`. Jika melebihi batas toleransi, status otomatis tercatat sebagai **Telat**.
3.  **Sinkronisasi Perizinan**: Jika ada data `IzinSakit` yang disetujui, sistem secara otomatis akan meng-override (menindih) data absen pada jam tersebut menjadi Izin/Sakit.
4.  **Rekapitulasi**: Data diproses menjadi persentase kehadiran bulanan dan semesteran yang akan tampil di raport.
 
### B. Alur Penilaian Akademik & Tahfidz
1.  **Input Nilai**: Guru menginput nilai per Rombel dan per Mata Pelajaran (Harian, UTS, UAS).
2.  **Kalkulasi**: Sistem menghitung nilai akhir berdasarkan bobot yang ditentukan (default: rata-rata).
3.  **Validasi KKM**: Nilai dibandingkan dengan KKM dari Modul Akademik untuk menentukan predikat (A, B, C, D).
4.  **Tahfidz**: Pencatatan setoran hafalan secara linear (Surat Awal -> Surat Akhir) untuk memantau progres hafalan santri.
 
---
 
## 5. Rencana Pengembangan (Roadmap)
 
Fitur-fitur berikut direncanakan untuk meningkatkan fungsionalitas modul:
 
1.  **Sistem Rekapitulasi Lanjutan**:
    *   Pembuatan modul **Rekap Tahunan** yang menggabungkan seluruh nilai dan kehadiran dalam satu dashboard statistik.
    *   Dashboard khusus Kepala Sekolah untuk memantau performa akademik seluruh kelas secara visual (grafik).
2.  **Export Data**:
    *   Fitur ekspor seluruh rekapan presensi dan nilai ke format **Excel** dan **PDF** untuk keperluan administrasi offline.
3.  **Notifikasi Real-time**:
    *   Integrasi WhatsApp/Email Gateway untuk mengirim notifikasi otomatis kepada Orang Tua saat siswa tercatat *Alpha* atau saat nilai ujian di bawah KKM.
4.  **Analisis Prediktif**:
    *   Sistem peringatan dini bagi siswa yang memiliki tren penurunan nilai secara signifikan selama 3 bulan berturut-turut.
 
## 6. Logika Perhitungan (Calculations)
 
Modul ini menggunakan beberapa formula standar untuk menghasilkan nilai raport dan statistik:
 
### A. Nilai Akhir Mata Pelajaran
Sistem menghitung nilai akhir dengan menggabungkan tiga komponen utama:
- **Formula**: `(Rata-rata Harian + Nilai UTS + Nilai UAS) / Jumlah Komponen`
- **Rata-rata Harian**: Jumlah seluruh nilai kategori 'Harian' dibagi jumlah entri.
- **Jumlah Komponen**: Sistem hanya membagi dengan komponen yang sudah terisi (misal: jika belum UTS/UAS, maka pembaginya hanya 1 yaitu rata-rata harian).
 
### B. Penentuan Predikat
Predikat ditentukan berdasarkan ambang batas (threshold) berikut:
- **A (Sangat Baik)**: Nilai ≥ 90
- **B (Baik)**: Nilai ≥ 80
- **C (Cukup)**: Nilai ≥ 70
- **D (Kurang)**: Nilai < 70
 
### C. Rerata Kelas (Class Average)
Nilai ini ditampilkan di raport untuk membandingkan performa santri dengan teman sekelasnya:
- **Alur**: Sistem menghitung Nilai Akhir untuk **setiap santri** dalam satu Rombel, kemudian menjumlahkannya dan membaginya dengan total santri di Rombel tersebut.
 
### D. Statistik Kehadiran
- **Persentase Kehadiran**: `(Total Hadir / Total Hari Efektif) * 100`.
- **Total Hari Efektif**: Dihitung berdasarkan jadwal pelajaran yang ada di kalender akademik hingga hari ini.
 
---
 
*Dokumentasi ini dibuat untuk memastikan konsistensi pengembangan fitur penilaian di SIAKAD Almahira.*

