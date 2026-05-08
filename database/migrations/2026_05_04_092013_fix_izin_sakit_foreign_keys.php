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
        // SQLite doesn't support dropping/modifying foreign keys easily.
        // We drop and recreate the table with correct constraints.
        Schema::disableForeignKeyConstraints();
        
        Schema::dropIfExists('izin_sakit');
        
        Schema::create('izin_sakit', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_siswa'); // Siswa uses UUID
            $table->unsignedBigInteger('id_kelas');
            $table->unsignedBigInteger('id_mapel')->nullable();
            $table->unsignedBigInteger('id_jadwal_pelajaran')->nullable();
            
            $table->string('jenis'); // Izin/Sakit
            $table->string('tipe_izin')->default('Harian'); // Harian/Per Matpel
            
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

            // Correct Constraints
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_sakit');
    }
};
