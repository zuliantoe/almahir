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
        Schema::create('kerusakan', function (Blueprint $table) {
            $table->id(); // otomatis jadi id_kerusakan

            // FK ke tabel aset
            $table->foreignId('aset_id')
                ->constrained('aset')
                ->onDelete('cascade');

            // tanggal rusak
            $table->date('tanggal_rusak');

            // deskripsi kerusakan
            $table->text('deskripsi_kerusakan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerusakan');
    }
};
