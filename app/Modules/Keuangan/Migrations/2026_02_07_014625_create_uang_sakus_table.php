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
        Schema::create('uang_sakus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');

            $table->decimal('jumlah', 15, 2); 
            $table->date('tanggal'); 
            $table->string('status')->default('Diterima Bendahara');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_sakus');
    }
};
