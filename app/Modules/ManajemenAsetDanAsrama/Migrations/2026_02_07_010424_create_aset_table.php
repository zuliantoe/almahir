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
        Schema::create('aset', function (Blueprint $table) {
            $table->id(); // otomatis jadi id_aset

            $table->string('kode_aset')->unique();
            $table->string('nama_aset');

            $table->date('tanggal_pengajuan')->nullable();

            $table->decimal('harga', 15, 2)->default(0);

            $table->enum('status', [
                'baik',
                'rusak',
                'dalam_perbaikan',
                'sudah_diperbaiki'
            ])->default('baik');

            $table->date('tanggal_pengadaan')->nullable();

            $table->string('kondisi')->nullable();
            // kondisi bisa detail: "retak", "layar pecah", dll

            $table->text('deskripsi_aset')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
