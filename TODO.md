- [ ] Investigate why migrations stop at `2026_05_16_075005_standardize_guru_and_pegawai_tables` (SQLite error: dropping column `email` that doesn't exist)
- [ ] Fix migration to be idempotent on SQLite (use Schema::hasColumn checks before dropColumn)
- [ ] Re-run `php artisan migrate --database=sqlite` until it completes `2026_05_16_081632_create_calon_pegawai_table`
- [ ] Re-test the page that triggers the `calon_pegawai` count query

