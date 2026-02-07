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
        Schema::create('pemeliharaan', function (Blueprint $table) {
            $table->id(); // otomatis jadi id_pemeliharaan

            // FK ke tabel aset
            $table->foreignId('aset_id')
                ->constrained('aset')
                ->onDelete('cascade');

            // tanggal mulai pemeliharaan
            $table->date('tanggal_mulai_pemeliharaan');

            // tanggal selesai pemeliharaan
            $table->date('tanggal_selesai_pemeliharaan')->nullable();

            // deskripsi pemeliharaan
            $table->text('deskripsi_pemeliharaan');

            // biaya pemeliharaan
            $table->decimal('biaya_pemeliharaan', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeliharaan');
    }
};
