<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeliharaan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemeliharaan', 'status')) {
                $table->enum('status', ['proses', 'selesai'])->default('proses')->after('catatan');
            }
            if (!Schema::hasColumn('pemeliharaan', 'catatan_selesai')) {
                $table->text('catatan_selesai')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pemeliharaan', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_selesai']);
        });
    }
};
