<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. PENILAIAN
        if (Schema::hasTable('penilaian')) {
            Schema::rename('penilaian', 'penilaian_old');
            Schema::create('penilaian', function (Blueprint $table) {
                $table->id();
                $table->uuid('siswa_id');
                $table->uuid('guru_id');
                $table->unsignedBigInteger('mapel_id');
                $table->unsignedBigInteger('tahunajaran_id');
                $table->string('jenis_nilai')->nullable();
                $table->string('semester', 20)->nullable();
                $table->uuid('author_id')->nullable();
                $table->integer('nilai');
                $table->integer('kkm')->default(75);
                $table->timestamps();

                $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
                $table->foreign('guru_id')->references('id')->on('guru')->onDelete('cascade');
                $table->foreign('mapel_id')->references('id')->on('mata_pelajaran')->onDelete('cascade');
                $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');
                $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
            });

            // Use COALESCE for tahunajaran_id to merge id_tahun_ajaran and tahunajaran_id
            DB::statement("INSERT INTO penilaian (id, siswa_id, guru_id, mapel_id, tahunajaran_id, jenis_nilai, semester, author_id, nilai, kkm, created_at, updated_at) 
                           SELECT id, id_siswa, id_guru, id_mapel, COALESCE(id_tahun_ajaran, tahunajaran_id), jenis_nilai, semester, author_id, nilai, kkm, created_at, updated_at FROM penilaian_old");
            
            Schema::dropIfExists('penilaian_old');
        }

        // 2. PRESENSI
        if (Schema::hasTable('presensi')) {
            Schema::rename('presensi', 'presensi_old');
            Schema::create('presensi', function (Blueprint $table) {
                $table->id();
                $table->uuid('siswa_id');
                $table->uuid('guru_id');
                $table->unsignedBigInteger('mapel_id');
                $table->unsignedBigInteger('jadwal_pelajaran_id');
                $table->unsignedBigInteger('tahunajaran_id')->nullable();
                $table->string('semester', 20)->nullable();
                $table->uuid('author_id')->nullable();
                $table->time('jam');
                $table->string('status');
                $table->string('kategori');
                $table->string('scan_id')->nullable();
                $table->timestamps();

                $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
                $table->foreign('guru_id')->references('id')->on('guru')->onDelete('cascade');
                $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
                $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
            });

            DB::statement("INSERT INTO presensi (id, siswa_id, guru_id, mapel_id, jadwal_pelajaran_id, tahunajaran_id, semester, author_id, jam, status, kategori, scan_id, created_at, updated_at) 
                           SELECT id, id_siswa, id_guru, id_mapel, id_jadwal_pelajaran, tahunajaran_id, semester, author_id, jam, status, kategori, scan_id, created_at, updated_at FROM presensi_old");
            
            Schema::dropIfExists('presensi_old');
        }

        // 3. PENILAIAN TAHFIDZ
        if (Schema::hasTable('penilaian_tahfidz')) {
            Schema::rename('penilaian_tahfidz', 'penilaian_tahfidz_old');
            Schema::create('penilaian_tahfidz', function (Blueprint $table) {
                $table->id();
                $table->uuid('siswa_id');
                $table->unsignedBigInteger('kelas_id');
                $table->uuid('guru_id');
                $table->unsignedBigInteger('tahunajaran_id')->nullable();
                $table->string('semester', 20)->nullable();
                $table->uuid('author_id')->nullable();
                $table->date('tanggal');
                $table->string('surat_awal');
                $table->string('surat_akhir');
                $table->integer('ayat_awal');
                $table->integer('ayat_akhir');
                $table->integer('nilai');
                $table->string('status_capaian')->nullable();
                $table->timestamps();

                $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
                $table->foreign('guru_id')->references('id')->on('guru')->onDelete('cascade');
                $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
                $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
            });

            DB::statement("INSERT INTO penilaian_tahfidz (id, siswa_id, kelas_id, guru_id, tahunajaran_id, semester, author_id, tanggal, surat_awal, surat_akhir, ayat_awal, ayat_akhir, nilai, status_capaian, created_at, updated_at) 
                           SELECT id, id_siswa, id_kelas, id_guru, tahunajaran_id, semester, author_id, tanggal, surat_awal, surat_akhir, ayat_awal, ayat_akhir, nilai, status_capaian, created_at, updated_at FROM penilaian_tahfidz_old");

            Schema::dropIfExists('penilaian_tahfidz_old');
        }

        // 4. IZIN SAKIT
        if (Schema::hasTable('izin_sakit')) {
            Schema::rename('izin_sakit', 'izin_sakit_old');
            Schema::create('izin_sakit', function (Blueprint $table) {
                $table->id();
                $table->uuid('siswa_id');
                $table->unsignedBigInteger('kelas_id');
                $table->unsignedBigInteger('mapel_id')->nullable();
                $table->unsignedBigInteger('jadwal_pelajaran_id')->nullable();
                $table->string('jenis');
                $table->string('tipe_izin')->default('Harian');
                $table->date('tgl_mulai');
                $table->date('tgl_selesai');
                $table->text('keterangan')->nullable();
                $table->string('bukti_foto')->nullable();
                $table->string('status')->default('Pending');
                $table->uuid('konfirmasi_oleh')->nullable();
                $table->timestamp('waktu_konfirmasi')->nullable();
                $table->unsignedBigInteger('tahunajaran_id')->nullable();
                $table->string('semester')->nullable();
                $table->uuid('author_id')->nullable();
                $table->timestamps();

                $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
                $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
                $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
            });

            DB::statement("INSERT INTO izin_sakit (id, siswa_id, kelas_id, mapel_id, jadwal_pelajaran_id, jenis, tipe_izin, tgl_mulai, tgl_selesai, keterangan, bukti_foto, status, konfirmasi_oleh, waktu_konfirmasi, tahunajaran_id, semester, author_id, created_at, updated_at) 
                           SELECT id, id_siswa, id_kelas, id_mapel, id_jadwal_pelajaran, jenis, tipe_izin, tgl_mulai, tgl_selesai, keterangan, bukti_foto, status, konfirmasi_oleh, waktu_konfirmasi, tahunajaran_id, semester, author_id, created_at, updated_at FROM izin_sakit_old");

            Schema::dropIfExists('izin_sakit_old');
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback would be complex, but we follow the same pattern if needed.
        // For now, we omit it as this is a cleanup migration.
    }
};
