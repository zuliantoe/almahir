# 📚 Dokumentasi Modul Akademik — SIAKAD Almahir

Modul Akademik adalah **inti (core)** dari seluruh sistem SIAKAD. Hampir semua modul lain (Penilaian, Presensi, Keuangan, dll) bergantung pada data yang dihasilkan oleh modul ini.

---

## 1. Sub-Modul & Controller

| Controller | Fungsi |
|-----------|--------|
| `AkademikController` | Dashboard akademik (berbeda per role: Admin/Guru/Siswa) |
| `TahunAjaranController` | CRUD Tahun Ajaran — hanya 1 yang boleh aktif |
| `KelasController` | CRUD Master Kelas (template, bukan kelas aktual) |
| `RombelController` | CRUD Rombongan Belajar + daftarkan siswa + history |
| `MataPelajaranController` | CRUD Mata Pelajaran + Bulk Store + Import |
| `KategoriPelajaranController` | CRUD Kategori Mata Pelajaran |
| `MasterKurikulumController` | CRUD template kurikulum (K-13, Merdeka, dll) |
| `KurikulumController` | Pemetaan mapel ke kelas/tingkat per tahun + Bulk Store |
| `JadwalPelajaranController` | CRUD Jadwal + Bulk Store + Copy antar rombel |
| `BebanMengajarController` | Laporan beban mengajar guru |
| `KalenderAkademikController` | CRUD Kalender + tampilan FullCalendar + Export iCal |
| `JenisKegiatanController` | CRUD tipe kegiatan (KBM, Libur, Ujian, dll) |
| `KenaikanKelasController` | Workflow kenaikan kelas: Naik / Lulus / Tidak Naik |

---

## 2. Model & Relasi Database

### Hierarki Data

```
TahunAjaran (1)
  └── Rombel (M) ──────────────── Kelas (1)
        └── RombelSiswa (M)       └── Tingkat (1)
              └── Siswa (1) [Modul Siswa]
        └── JadwalPelajaran (M)
              ├── MataPelajaran (1)
              └── Guru (1) [Modul Guru]

TahunAjaran (1)
  └── Kurikulum (M)
        ├── MasterKurikulum (1)
        ├── Tingkat (1)
        ├── Kelas (1)
        └── MataPelajaran (1)
              └── KategoriPelajaran (1)

TahunAjaran (1)
  └── KalenderAkademik (M)
        └── JenisKegiatan (1)
```

### Status Siklus Hidup Siswa di `rombel_siswa`

| Status | Keterangan |
|--------|-----------|
| `aktif` | Siswa sedang aktif di rombel tahun ini |
| `naik` | Siswa naik kelas (arsip historis) |
| `lulus` | Siswa lulus/tamat (arsip historis) |
| `tidak_naik` | Siswa tinggal kelas (arsip historis) |

---

## 3. Alur Data (End-to-End)

```
STEP 1 — Setup Master Data
  Admin → Tahun Ajaran → Tingkat → Kelas → Kategori Mapel → Mata Pelajaran
  Admin → Jenis Kegiatan → Master Kurikulum

STEP 2 — Isi Kurikulum
  Admin → Kurikulum (Master + Tingkat + Kelas + Mapel + Total Jam + KKM)

STEP 3 — Buat Rombel
  Admin → Rombel (nama, kelas, tahun ajaran, wali kelas)
        → Daftarkan Siswa → rombel_siswa (status: aktif)

STEP 4 — Buat Jadwal
  Admin → JadwalPelajaran (hari, jam, mapel, guru) per Rombel
  Fitur: Bulk Store, Copy jadwal antar rombel

STEP 5 — Kalender Akademik
  Admin → Input kegiatan (ujian, libur, rapat)
        → Tampil di FullCalendar → Export ke Google Calendar (iCal)

STEP 6 — KBM Berjalan
  Guru → Modul PenilaianDanPresensi menggunakan:
    siswa_id + guru_id + mapel_id + jadwal_pelajaran_id + tahunajaran_id
    (semua dari Modul Akademik)

STEP 7 — Akhir Tahun → Kenaikan Kelas
  Admin → Pilih Rombel asal + Tahun Ajaran tujuan + Kelas tujuan
        → Set status per siswa: Naik / Lulus / Tidak Naik
  Sistem:
    a. Update rombel_siswa lama (naik/lulus/tidak_naik)
    b. Buat Rombel baru di tahun tujuan
    c. Insert siswa yang naik → rombel_siswa baru (status: aktif)
```

---

## 4. Keterkaitan dengan Modul Lain

### A. Modul Siswa
- `Rombel` ↔ `Siswa` → Many-to-Many via `rombel_siswa`
- `Siswa.rombelSiswa()` → ambil semua riwayat rombel
- `Siswa.currentRombel()` → rombel aktif siswa saat ini
- Dashboard Siswa → redirect ke view jadwal berdasarkan rombel aktif

### B. Modul Guru
- `Rombel.walikelas()` → `Guru` sebagai wali kelas
- `JadwalPelajaran.guru()` → `Guru` sebagai pengampu
- `Guru.jadwalPelajaran()` → semua jadwal mengajar guru
- Dashboard Guru → tampil timetable jadwal mengajar guru

### C. Modul PenilaianDanPresensi
- `PenilaianAkademik` pakai: `siswa_id`, `guru_id`, `mapel_id`, `tahunajaran_id`
- `Presensi` pakai: `siswa_id`, `guru_id`, `jadwal_pelajaran_id`, `mapel_id`
- Kedua model import langsung dari namespace Akademik

### D. Modul Pendaftaran
- Siswa baru dari PPDB → setelah terima → dimasukkan ke Rombel

---

## 5. Cara Pakai di Modul Lain (Panduan Developer)

### Ambil Tahun Ajaran Aktif
```php
use App\Modules\Akademik\Models\TahunAjaran;

$tahunAktif = TahunAjaran::current(); // Shortcut static method
$tahunAktif = TahunAjaran::aktif()->first(); // Sama hasilnya
```

### Cari Rombel Aktif Siswa
```php
use App\Modules\Akademik\Models\RombelSiswa;

$rombel = RombelSiswa::with('rombel')
    ->where('siswa_id', $siswaId)
    ->where('status', 'aktif')
    ->whereHas('rombel', fn($q) => $q->where('tahunajaran_id', $tahunId))
    ->first();
```

### Ambil Jadwal Guru
```php
use App\Modules\Akademik\Models\JadwalPelajaran;

$jadwal = JadwalPelajaran::with(['rombel', 'mataPelajaran'])
    ->where('guru_id', $guruId)
    ->where('hari', 'Senin')
    ->get();
```

### Cek Hari Libur dari Kalender
```php
use App\Modules\Akademik\Models\KalenderAkademik;

$isLibur = KalenderAkademik::whereHas('jenisKegiatan', fn($q) => $q->where('is_kbm', false))
    ->whereDate('tanggal_awal', '<=', $today)
    ->whereDate('tanggal_akhir', '>=', $today)
    ->exists();
```

---

## 6. Lingkup Modul Akademik

### ✅ Sudah Selesai
- CRUD Tahun Ajaran, Tingkat, Kelas, Rombel
- Manajemen Siswa dalam Rombel (daftar, edit, hapus)
- Riwayat / History Rombel Siswa
- Workflow Kenaikan Kelas (Naik/Lulus/Tidak Naik)
- CRUD Mata Pelajaran + Kategori + Bulk Store
- Master Kurikulum & Detail Kurikulum (Bulk Store)
- Jadwal Pelajaran (CRUD + Bulk Store + Copy)
- Timetable Guru (grid mingguan)
- Timetable Siswa (grid mingguan)
- Beban Mengajar (laporan JP guru)
- Kalender Akademik + FullCalendar + Export iCal
- Jenis Kegiatan (flag KBM/non-KBM + warna)
- Dashboard Akademik (statistik + upcoming events)
- RBAC (ReadOnly untuk Guru & Siswa)

### 🔲 Pengembangan Selanjutnya
- Mutasi Siswa Antar Rombel (tanpa kenaikan kelas)
- Cetak/Print Jadwal ke PDF
- Notifikasi perubahan jadwal
- Substitusi Guru sementara
- Laporan cetak Kurikulum / Silabus
- Import Jadwal dari Excel
- Pembagian Rombel otomatis berdasarkan kriteria

---

## 7. Route yang Tersedia

```
GET  /akademik                        → Dashboard
GET  /akademik/tahun-ajaran           → List tahun ajaran
GET  /akademik/kelas                  → List kelas
GET  /akademik/rombel                 → List rombel
GET  /akademik/rombel/history         → History rombel siswa
GET  /akademik/mata-pelajaran         → List mapel
GET  /akademik/kurikulum              → List kurikulum
GET  /akademik/jadwal-pelajaran       → Jadwal / Timetable
GET  /akademik/beban-mengajar         → Laporan beban mengajar
GET  /akademik/kalender-akademik      → Kalender (list)
GET  /akademik/kalender-akademik?view=calendar → Kalender visual
GET  /akademik/kalender-akademik-export/ical   → Export iCal (publik)
GET  /akademik/kenaikan-kelas         → Form kenaikan kelas
POST /akademik/kenaikan-kelas/process → Proses kenaikan kelas
```

---

*Dokumentasi ini adalah sumber kebenaran (source of truth) untuk Modul Akademik SIAKAD Almahir.*
