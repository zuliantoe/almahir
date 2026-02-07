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
        Schema::create('tagihan_siswas', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->decimal('amount', 15, 2);

            $table->date('tanggal_tagihan');
            $table->date('batas_waktu');

            $table->enum('status', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');

            // Polymorphic columns
            $table->morphs('target');
            // => target_type (string)
            // => target_id (bigint)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_siswas');
    }
};
