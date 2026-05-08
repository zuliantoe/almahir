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

            $table->uuid('user_id')->unique(); // 🔥 penting untuk 1-1

            $table->string('nama');
            $table->uuid('type_pegawai_id');
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->string('foto')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('sys_users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
