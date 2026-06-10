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
        // Drop the master_beban_mengajars table as requested (no master for beban mengajar)
        Schema::dropIfExists('master_beban_mengajars');

        // Add the beban_mengajar_maksimal column to the master_kurikulum table
        Schema::table('master_kurikulum', function (Blueprint $table) {
            if (!Schema::hasColumn('master_kurikulum', 'beban_mengajar_maksimal')) {
                $table->unsignedInteger('beban_mengajar_maksimal')->default(24)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kurikulum', function (Blueprint $table) {
            if (Schema::hasColumn('master_kurikulum', 'beban_mengajar_maksimal')) {
                $table->dropColumn('beban_mengajar_maksimal');
            }
        });

        if (!Schema::hasTable('master_beban_mengajars')) {
            Schema::create('master_beban_mengajars', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('standar_jam_per_minggu')->default(24);
                $table->timestamps();
            });
        }
    }
};
