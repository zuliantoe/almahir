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
        Schema::create('pembayaran_siswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tagihan_siswa_id');
            $table->unsignedBigInteger('siswa_id')->nullable();

            $table->enum('tipe_pembayaran', ['Pembayaran SPP', 'Pembayaran Daftar Ulang', 'Bimbel', 'Uang Saku'])->default('Pembayaran SPP');
            $table->decimal('jumlah_dibayarkan', 15, 2);
            $table->decimal('jumlah_tersisa', 15, 2)->default(0);
            $table->date('tanggal_pembayaran');
            $table->string('metode_pembayaran')->default('transfer');
            $table->string('bukti_gambar')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_siswas');
    }
};
