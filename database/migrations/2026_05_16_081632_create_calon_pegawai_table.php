<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calon_pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke posisi yang dilamar
            $table->uuid('type_pegawai_id')->nullable();

            // Data Pribadi Dasar (Standar HRD Almahira)
            $table->string('nama');
            $table->string('email')->unique(); // Penting untuk notifikasi seleksi & konversi akun
            $table->string('no_hp')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('mata_pelajaran')->nullable(); // Khusus pelamar guru

            // Data Spesifik Rekrutmen (Pelamar)
            $table->string('berkas_cv')->nullable();
            $table->string('berkas_lamaran')->nullable();
            $table->enum('status_seleksi', ['baru', 'wawancara', 'diterima', 'ditolak'])->default('baru');
            $table->date('tanggal_melamar')->useCurrent();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calon_pegawai');
    }
};
