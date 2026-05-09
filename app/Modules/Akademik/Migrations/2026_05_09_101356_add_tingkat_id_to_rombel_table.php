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
        Schema::table('rombel', function (Blueprint $table) {
            if (!Schema::hasColumn('rombel', 'tingkat_id')) {
                $table->foreignId('tingkat_id')->nullable()->after('kelas_id')->constrained('tingkat')->onDelete('set null');
            }
        });

        // Populate existing data
        $rombels = DB::table('rombel')->get();
        foreach ($rombels as $r) {
            $kelas = DB::table('kelas')->where('id', $r->kelas_id)->first();
            if ($kelas) {
                DB::table('rombel')->where('id', $r->id)->update(['tingkat_id' => $kelas->tingkat_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            $table->dropForeign(['tingkat_id']);
            $table->dropColumn('tingkat_id');
        });
    }
};
