<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Temporarily change column to string to avoid constraint/truncation issues in MySQL
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });

        // 2. Map existing 'ditunda' statuses to 'pending' to match Laravel code conventions
        DB::table('pendaftarans')
            ->where('status', 'ditunda')
            ->update(['status' => 'pending']);

        // 3. Change column type back to the new ENUM definition
        DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM('pending', 'diproses', 'diterima', 'ditolak') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Temporarily change column to string
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('status', 50)->default('ditunda')->change();
        });

        // 2. Map 'pending' back to 'ditunda'
        DB::table('pendaftarans')
            ->where('status', 'pending')
            ->update(['status' => 'ditunda']);

        // 3. Revert column back to original ENUM definition
        DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM('ditunda', 'diproses', 'diterima', 'ditolak') NOT NULL DEFAULT 'ditunda'");
    }
};
