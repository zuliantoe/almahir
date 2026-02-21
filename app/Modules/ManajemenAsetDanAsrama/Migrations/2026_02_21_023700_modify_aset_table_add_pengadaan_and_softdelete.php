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
        // Cek apakah kolom 'status' masih ada, rename ke 'status_kondisi'
        if (Schema::hasColumn('aset', 'status')) {
            Schema::table('aset', function (Blueprint $table) {
                // Rename kolom status ke status_kondisi
                $table->renameColumn('status', 'status_kondisi');
            });
        } else if (!Schema::hasColumn('aset', 'status_kondisi')) {
            // Jika belum ada kolom status_kondisi, tambahkan
            Schema::table('aset', function (Blueprint $table) {
                $table->enum('status_kondisi', [
                    'baik',
                    'rusak',
                    'dalam_perbaikan',
                    'sudah_diperbaiki'
                ])->default('baik')->after('harga');
            });
        }

        // Tambah kolom baru di tabel aset
        Schema::table('aset', function (Blueprint $table) {
            // Foreign key ke pengadaan_aset (akan di-add setelah tabelnya dibuat)
            // Untuk sementara tambah dulu kolomnya tanpa foreign key dulu
            $table->unsignedBigInteger('pengadaan_id')->nullable()->after('deskripsi_aset');
            
            // Soft delete fields
            $table->softDeletes(); // ini akan membuat kolom deleted_at
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('deleted_at');
            $table->text('alasan_hapus')->nullable()->after('deleted_by');
        });

        // Note: Kita akan tambahkan foreign key constraint untuk pengadaan_id
        // setelah tabel pengadaan_aset dibuat (di migration berikutnya)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['pengadaan_id', 'deleted_by', 'alasan_hapus']);
            $table->dropSoftDeletes();
            
            // Kembalikan nama kolom jika perlu
            if (Schema::hasColumn('aset', 'status_kondisi')) {
                $table->renameColumn('status_kondisi', 'status');
            }
        });
    }
};