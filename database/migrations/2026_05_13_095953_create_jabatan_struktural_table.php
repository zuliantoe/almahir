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
        Schema::create('jabatan_struktural', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke user account (untuk login/otorisasi)
            $table->uuid('user_id')->index();
            
            // Relasi ke profil (bisa guru atau pegawai)
            $table->uuid('pegawai_id')->nullable()->index();
            $table->uuid('guru_id')->nullable()->index();
            
            $table->string('jenis_jabatan'); // e.g. kepala_sekolah, bendahara, wakasek_kurikulum
            
            $table->date('periode_mulai');
            $table->date('periode_selesai')->nullable();
            
            $table->string('sk_pengangkatan')->nullable();
            $table->string('ttd_digital')->nullable();
            $table->string('stempel_jabatan')->nullable();
            
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('catatan')->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('sys_users')->onDelete('cascade');
            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');
            $table->foreign('guru_id')->references('id')->on('guru')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_struktural');
    }
};
