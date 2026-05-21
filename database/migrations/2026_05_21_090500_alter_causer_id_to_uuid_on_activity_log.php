<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Change causer_id to a CHAR(36) to store UUIDs
            $table->char('causer_id', 36)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Revert back to bigInteger (unsigned) if needed
            $table->bigInteger('causer_id')->unsigned()->nullable()->change();
        });
    }
};
