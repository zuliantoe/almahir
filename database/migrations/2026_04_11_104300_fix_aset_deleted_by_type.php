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
        if (config('database.default') === 'mysql') {
            // MySQL specific raw SQL
            try {
                DB::statement('ALTER TABLE aset DROP FOREIGN KEY aset_deleted_by_foreign');
            } catch (\Exception $e) {
                // Ignore if FK doesn't exist
            }
            DB::statement('ALTER TABLE aset MODIFY COLUMN deleted_by CHAR(36) NULL;');
        } else {
            // SQLite and others: Use Laravel Schema Builder
            Schema::table('aset', function (Blueprint $table) {
                $table->uuid('deleted_by')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
