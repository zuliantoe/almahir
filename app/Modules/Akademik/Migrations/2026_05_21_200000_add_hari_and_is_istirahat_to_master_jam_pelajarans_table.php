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
        Schema::table('master_jam_pelajarans', function (Blueprint $table) {
            try {
                // Drop unique constraint on jamke
                $table->dropUnique('master_jam_pelajarans_jamke_unique');
            } catch (\Exception $e) {
                // Ignore if it was not created with this exact name
            }

            $table->string('hari')->default('Senin')->after('id');
            $table->boolean('is_istirahat')->default(false)->after('jamakhir');

            // Add unique index on combination of hari and jamke
            $table->unique(['hari', 'jamke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_jam_pelajarans', function (Blueprint $table) {
            try {
                $table->dropUnique(['hari', 'jamke']);
            } catch (\Exception $e) {}

            $table->dropColumn(['hari', 'is_istirahat']);
            $table->unique('jamke');
        });
    }
};
