<<<<<<< HEAD
- [ ] Investigate why migrations stop at `2026_05_16_075005_standardize_guru_and_pegawai_tables` (SQLite error: dropping column `email` that doesn't exist)
- [ ] Fix migration to be idempotent on SQLite (use Schema::hasColumn checks before dropColumn)
- [ ] Re-run `php artisan migrate --database=sqlite` until it completes `2026_05_16_081632_create_calon_pegawai_table`
- [ ] Re-test the page that triggers the `calon_pegawai` count query
=======
# TODO - Jadwal Pelajaran ambil Jam dari Master Jam Pelajaran

## Step 1: Updates backend validation & jam derivation
- [ ] Update `app/Http/Requests/AkademikRequest/StoreJadwalPelajaranRequest.php`
  - [ ] Tambah rule `master_jam_pelajaran_id`
  - [ ] Buat `jamawal` & `jamakhir` tidak required jika `master_jam_pelajaran_id` ada
  - [ ] Derive `jamke`, `jamawal`, `jamakhir` dari master jam saat request masuk
- [ ] Update `app/Http/Requests/AkademikRequest/UpdateJadwalPelajaranRequest.php`
  - [ ] Hal yang sama seperti Store (derivation dari master jam)

## Step 2: Updates controller load master jam list
- [ ] Update `app/Modules/Akademik/Controllers/JadwalPelajaranController.php`
  - [ ] Di method `create()` load `MasterJamPelajaran` dan pass ke view massal
  - [ ] Di method `edit()` load `MasterJamPelajaran` dan pass ke view edit
>>>>>>> 6acea83 (add master and feature in akademik)

## Step 3: Updates views for massal create
- [ ] Update `app/Modules/Akademik/Views/jadwal-pelajaran/create.blade.php`
  - [ ] Ganti header/kolom jam: tambah dropdown master jam
  - [ ] Hapus/stop penggunaan `JAMKE_MAP` (mapping jamke -> jamawal/jamakhir dari JS)
  - [ ] On change master jam, isi otomatis jamawal & jamakhir (readonly/hidden)
- [ ] Update `app/Modules/Akademik/Views/jadwal-pelajaran/partials/row.blade.php`
  - [ ] Tambah dropdown `master_jam_pelajaran_id` per baris
  - [ ] Jadikan `jamawal` & `jamakhir` readonly/hidden (tetap dikirim ke backend)
  - [ ] Set nilai jam dari existing data saat edit/duplicate
  - [ ] Siapkan attribute/data untuk JS auto-fill

## Step 4: Updates views for single edit
- [ ] Update `app/Modules/Akademik/Views/jadwal-pelajaran/edit.blade.php`
  - [ ] Tambah dropdown master jam
  - [ ] Buat jamawal & jamakhir readonly/hidden dan terisi otomatis dari master jam

## Step 5: Verification
- [ ] Jalankan proses:
  - [ ] Tambah jadwal massal: pilih master jam -> jam terisi otomatis
  - [ ] Edit jadwal: dropdown master jam konsisten dengan jam yang tersimpan
  - [ ] Submit harus lolos validation tanpa jam manual
