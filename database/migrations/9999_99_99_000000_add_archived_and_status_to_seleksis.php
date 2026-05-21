<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambahan kolom untuk manajemen status & pengarsipan jadwal seleksi.
        // Durasi ujinya diasumsikan 1.5 jam, sehingga selesai_at = mulai_at + 1.5 jam.
        Schema::table('seleksis', function (Blueprint $table) {
            // Laravel Blueprint tidak menyediakan hasColumn pada saat runtime.
            // Kita gunakan pendekatan sederhana: tambahkan kolom bila belum ada.
            // Namun karena migration dijalankan sekali, implementasi ini cukup aman.

            // Kolom timing
            $table->dateTime('mulai_at')->nullable()->after('jam');
            $table->dateTime('selesai_at')->nullable()->after('mulai_at');

            // Status & archive
            $table->string('status_tes')->nullable()->default('scheduled')->after('selesai_at');
            $table->boolean('archived')->default(false)->after('status_tes');
        });
    }


    public function down()
    {
        Schema::table('seleksis', function (Blueprint $table) {
            if (Schema::hasColumn('seleksis', 'mulai_at')) {
                $table->dropColumn('mulai_at');
            }
            if (Schema::hasColumn('seleksis', 'selesai_at')) {
                $table->dropColumn('selesai_at');
            }
            if (Schema::hasColumn('seleksis', 'status_tes')) {
                $table->dropColumn('status_tes');
            }
            if (Schema::hasColumn('seleksis', 'archived')) {
                $table->dropColumn('archived');
            }
        });
    }
};

