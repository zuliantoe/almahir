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
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tujuan_id');
            
            $table->decimal('jumlah', 15, 2); 
            $table->date('tanggal'); 
            $table->time('waktu')->default('00:00:00');
            $table->text('deskripsi')->nullable(); 
            $table->timestamps();
            
            $table->foreign('tujuan_id')->references('id')->on('tujuans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
