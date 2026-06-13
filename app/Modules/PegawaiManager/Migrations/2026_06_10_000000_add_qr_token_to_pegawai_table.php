<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pegawai', 'qr_token')) {
            // Add column first as nullable
            Schema::table('pegawai', function (Blueprint $table) {
                $table->string('qr_token')->nullable()->after('status');
            });

            // Populate existing rows with random UUID
            $pegawais = DB::table('pegawai')->whereNull('qr_token')->get();
            foreach ($pegawais as $pegawai) {
                DB::table('pegawai')
                    ->where('id', $pegawai->id)
                    ->update(['qr_token' => (string) Str::uuid()]);
            }

            // Make it unique
            Schema::table('pegawai', function (Blueprint $table) {
                $table->unique('qr_token');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropColumn('qr_token');
        });
    }
};
