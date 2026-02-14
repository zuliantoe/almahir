<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('nama');
            $table->uuid('type_pegawai_id');

            $table->string('no_hp', 20)->nullable();
            $table->string('email')->unique();

            $table->text('alamat')->nullable();
            $table->date('tanggal_masuk');

            $table->string('foto')->nullable();

            $table->timestamps();

            $table->foreign('type_pegawai_id')
                  ->references('id')
                  ->on('type_pegawai')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
