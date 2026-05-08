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
        Schema::rename('penilaian', 'penilaian_old');
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_siswa');
            $table->uuid('id_guru');
            $table->unsignedBigInteger('id_mapel');
            $table->unsignedBigInteger('id_tahun_ajaran');
            $table->unsignedBigInteger('tahunajaran_id')->nullable();
            $table->string('jenis_nilai')->nullable();
            $table->string('semester', 20)->nullable();
            $table->uuid('author_id')->nullable();
            $table->integer('nilai');
            $table->integer('kkm')->default(75);
            $table->timestamps();

            // Correct FKs pointing to sys_users and other tables
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('id_guru')->references('id')->on('guru')->onDelete('cascade');
            $table->foreign('id_mapel')->references('id')->on('mata_pelajaran')->onDelete('cascade');
            $table->foreign('id_tahun_ajaran')->references('id')->on('tahun_ajaran')->onDelete('cascade');
            $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
        });

        // Copy data for Penilaian
        DB::statement('INSERT INTO penilaian (id, id_siswa, id_guru, id_mapel, id_tahun_ajaran, tahunajaran_id, jenis_nilai, semester, author_id, nilai, kkm, created_at, updated_at) 
                       SELECT id, id_siswa, id_guru, id_mapel, id_tahun_ajaran, tahunajaran_id, jenis_nilai, semester, author_id, nilai, kkm, created_at, updated_at FROM penilaian_old');
        
        Schema::dropIfExists('penilaian_old');


        // 2. PRESENSI
        Schema::rename('presensi', 'presensi_old');
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_siswa');
            $table->uuid('id_guru');
            $table->unsignedBigInteger('id_mapel');
            $table->unsignedBigInteger('id_jadwal_pelajaran');
            $table->unsignedBigInteger('tahunajaran_id')->nullable();
            $table->string('semester', 20)->nullable();
            $table->uuid('author_id')->nullable();
            $table->time('jam');
            $table->string('status');
            $table->string('kategori');
            $table->string('scan_id')->nullable();
            $table->timestamps();

            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('id_guru')->references('id')->on('guru')->onDelete('cascade');
            $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
        });

        // Copy data for Presensi
        DB::statement('INSERT INTO presensi (id, id_siswa, id_guru, id_mapel, id_jadwal_pelajaran, tahunajaran_id, semester, author_id, jam, status, kategori, created_at, updated_at) 
                       SELECT id, id_siswa, id_guru, id_mapel, id_jadwal_pelajaran, tahunajaran_id, semester, author_id, jam, status, kategori, created_at, updated_at FROM presensi_old');
        
        Schema::dropIfExists('presensi_old');


        // 3. PENILAIAN TAHFIDZ
        Schema::rename('penilaian_tahfidz', 'penilaian_tahfidz_old');
        Schema::create('penilaian_tahfidz', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_siswa');
            $table->unsignedBigInteger('id_kelas');
            $table->uuid('id_guru');
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

            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('id_guru')->references('id')->on('guru')->onDelete('cascade');
            $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
        });

        // Copy data for Tahfidz
        DB::statement('INSERT INTO penilaian_tahfidz (id, id_siswa, id_kelas, id_guru, tahunajaran_id, semester, author_id, tanggal, surat_awal, surat_akhir, ayat_awal, ayat_akhir, nilai, status_capaian, created_at, updated_at) 
                       SELECT id, id_siswa, id_kelas, id_guru, tahunajaran_id, semester, author_id, tanggal, surat_awal, surat_akhir, ayat_awal, ayat_akhir, nilai, status_capaian, created_at, updated_at FROM penilaian_tahfidz_old');

        Schema::dropIfExists('penilaian_tahfidz_old');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to rollback complex recreation in SQLite without risking data.
        // We leave the new schema as it is more correct.
    }
};
