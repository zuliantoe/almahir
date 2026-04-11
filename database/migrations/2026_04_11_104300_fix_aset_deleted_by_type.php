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
        // Temukan FK jika ada
        DB::statement('ALTER TABLE aset DROP FOREIGN KEY aset_deleted_by_foreign');
        DB::statement('ALTER TABLE aset MODIFY COLUMN deleted_by CHAR(36) NULL;');
        // DB::statement('ALTER TABLE aset ADD CONSTRAINT aset_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES sys_users(id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
