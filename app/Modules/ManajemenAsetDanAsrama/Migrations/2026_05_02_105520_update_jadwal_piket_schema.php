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
        // Because SQLite doesn't support dropping multiple columns easily in older versions,
        // and to ensure data integrity during schema rebuild, we will drop the table and recreate it.
        // It's safe since this is a new module upgrade.
        Schema::dropIfExists('jadwal_piket');

        Schema::create('jadwal_piket', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('kamar_id');
            $table->foreign('kamar_id')->references('id')->on('kamar')->onDelete('cascade');

            $table->date('tanggal');

            // UUID FK ke siswa
            $table->char('siswa_id', 36);
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');

            $table->enum('status', ['belum', 'sudah'])->default('belum');

            $table->timestamps();

            // Seseorang tidak boleh piket 2 kali di kamar yang sama pada tanggal yang sama
            $table->unique(['kamar_id', 'tanggal', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_piket');
    }
};
