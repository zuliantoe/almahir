<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->id(); // id_kamar otomatis

            $table->string('nama_kamar'); 
            // contoh: Kamar A1, Kamar Umar

            $table->integer('kapasitas'); 
            // maksimal penghuni kamar

            $table->text('deskripsi')->nullable(); 
            // catatan tambahan (optional)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};
