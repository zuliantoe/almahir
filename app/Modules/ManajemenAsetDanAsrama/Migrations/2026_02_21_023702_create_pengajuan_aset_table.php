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
        Schema::create('pengajuan_aset', function (Blueprint $table) {
            $table->id();
            
            // Nomor pengajuan: PJ-YYYYMM-XXXX (contoh: PJ-202602-0001)
            $table->string('nomor_pengajuan')->unique();
            
            $table->string('nama_aset');
            $table->text('deskripsi_pengajuan');
            $table->decimal('estimasi_harga', 15, 2);
            $table->date('tanggal_pengajuan');
            
            $table->foreignId('pengaju_id')->constrained('users');
            
            $table->enum('status', [
                'diajukan',
                'disetujui', 
                'ditolak',
                'proses_pengadaan'
            ])->default('diajukan');
            
            $table->text('catatan_tolak')->nullable();
            $table->text('alasan_pengajuan_ulang')->nullable(); // diisi saat ajukan ulang
            
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            // Soft delete fields
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('alasan_hapus')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_aset');
    }
};