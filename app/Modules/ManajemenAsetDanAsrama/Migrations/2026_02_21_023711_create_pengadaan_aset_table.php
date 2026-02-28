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
            
            // Nomor PO: PO-YYYYMM-XXXX (contoh: PO-202602-0001)
            $table->string('nomor_po')->unique();
            
            $table->foreignId('pengajuan_id')->constrained('pengajuan_aset');
            
            $table->string('vendor');
            $table->date('tanggal_pesan');
            $table->date('estimasi_datang');
            $table->date('tanggal_datang')->nullable(); // diisi saat barang datang
            $table->decimal('biaya_riil', 15, 2)->default(0);
            $table->text('catatan_pengadaan')->nullable();
            
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