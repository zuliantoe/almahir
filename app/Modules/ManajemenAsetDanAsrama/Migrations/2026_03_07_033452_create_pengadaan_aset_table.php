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
        Schema::create('pengadaan_aset', function (Blueprint $table) {
            $table->id();
            
            // Nomor PO: format PO-YYYYMM-XXXX (unique)
            $table->string('nomor_po')->unique();
            
            // Foreign key ke pengajuan_aset (integer)
            $table->foreignId('pengajuan_id')
                  ->constrained('pengajuan_aset')
                  ->onDelete('cascade');
            
            // Informasi vendor dan pengadaan
            $table->string('vendor');
            $table->date('tanggal_pesan');
            $table->date('estimasi_datang');
            $table->date('tanggal_datang')->nullable(); // diisi saat barang datang
            $table->decimal('biaya_riil', 15, 2)->default(0);
            $table->text('catatan_pengadaan')->nullable();
            
            // Status pengadaan
            $table->enum('status', [
                'dipesan',
                'datang',
                'batal'
            ])->default('dipesan');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan_aset');
    }
};