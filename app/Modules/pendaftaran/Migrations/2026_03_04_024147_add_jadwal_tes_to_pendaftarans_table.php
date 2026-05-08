<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('seleksis')) {
            Schema::create('seleksis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');

                $table->string('nama_tes');
                $table->date('tanggal');
                $table->time('jam');
                $table->string('pengampu')->nullable();
                $table->string('metode'); // offline / online
                $table->string('lokasi')->nullable();
                $table->string('link')->nullable();
                $table->integer('nilai')->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            //
        });
    }
};
