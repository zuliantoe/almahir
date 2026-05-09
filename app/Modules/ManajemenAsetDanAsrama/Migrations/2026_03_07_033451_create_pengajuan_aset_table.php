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
        if (!Schema::hasTable('pengajuan_aset')) {
            Schema::create('pengajuan_aset', function (Blueprint $table) {
                $table->id();
                
                // Nomor pengajuan: format PJ-YYYYMM-XXXX (unique)
                $table->string('nomor_pengajuan')->unique();
                
                // Informasi aset yang diajukan
                $table->string('nama_aset');
                $table->text('deskripsi_pengajuan');
                $table->decimal('estimasi_harga', 15, 2);
                $table->date('tanggal_pengajuan');
                
                // Foreign key ke pengaju (UUID dari tabel sys_users)
                $table->uuid('pengaju_id');
                $table->foreign('pengaju_id')
                    ->references('id')
                    ->on('sys_users')
                    ->onDelete('cascade');
                
                // Status pengajuan
                $table->enum('status', [
                    'diajukan',
                    'disetujui',
                    'ditolak',
                    'proses_pengadaan'
                ])->default('diajukan');
                
                // Catatan dan alasan (untuk reject dan resubmit)
                $table->text('catatan_tolak')->nullable();
                $table->text('alasan_pengajuan_ulang')->nullable();
                
                // Data persetujuan (diisi ketika approve/reject)
                $table->uuid('approved_by')->nullable();
                $table->foreign('approved_by')
                    ->references('id')
                    ->on('sys_users')
                    ->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                
                // Soft delete fields
                $table->softDeletes(); // menambah kolom deleted_at
                $table->uuid('deleted_by')->nullable();
                $table->foreign('deleted_by')
                    ->references('id')
                    ->on('sys_users')
                    ->onDelete('set null');
                $table->text('alasan_hapus')->nullable();
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_aset');
    }
};