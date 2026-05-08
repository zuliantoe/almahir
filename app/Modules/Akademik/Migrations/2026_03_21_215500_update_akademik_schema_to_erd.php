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
        // 1. Create Tingkat Table (Grade Levels)
        if (!Schema::hasTable('tingkat')) {
            Schema::create('tingkat', function (Blueprint $table) {
                $table->id();
                $table->string('kode_tingkat')->unique();
                $table->string('nama_tingkat');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 2. Create Jurusan Table (Majors)
        if (!Schema::hasTable('jurusan')) {
            Schema::create('jurusan', function (Blueprint $table) {
                $table->id();
                $table->string('kode_jurusan')->unique();
                $table->string('nama_jurusan');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 3. Create Master Kurikulum Table
        if (!Schema::hasTable('master_kurikulum')) {
            Schema::create('master_kurikulum', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kurikulum');
                $table->boolean('status')->default(false);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 4. Update Tahun Ajaran Table
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            if (!Schema::hasColumn('tahun_ajaran', 'semester')) {
                $table->string('semester', 20)->nullable()->after('tahunajaran');
            }
            if (!Schema::hasColumn('tahun_ajaran', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('status');
            }
        });

        // 5. Update Kelas Table
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'namakelas') && !Schema::hasColumn('kelas', 'nama_kelas')) {
                $table->renameColumn('namakelas', 'nama_kelas');
            }
            
            if (!Schema::hasColumn('kelas', 'kode_kelas')) {
                $table->string('kode_kelas')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('kelas', 'tingkat_id')) {
                $table->foreignId('tingkat_id')->nullable()->constrained('tingkat')->after('nama_kelas');
            }
            
            if (!Schema::hasColumn('kelas', 'jurusan_id')) {
                $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->after('tingkat_id');
            }
            
            if (!Schema::hasColumn('kelas', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->after('jurusan_id');
            }

            if (!Schema::hasColumn('kelas', 'wali_kelas_id')) {
                $table->uuid('wali_kelas_id')->nullable()->after('tahun_ajaran_id');
            }
        });

        // 6. Update Mata Pelajaran Table
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (!Schema::hasColumn('mata_pelajaran', 'kelompok')) {
                $table->string('kelompok', 50)->nullable()->after('kategori_id');
            }
        });

        // 7. Update Kurikulum (Junction/Set Kurikulum) Table
        Schema::table('kurikulum', function (Blueprint $table) {
            if (!Schema::hasColumn('kurikulum', 'master_kurikulum_id')) {
                $table->foreignId('master_kurikulum_id')->nullable()->constrained('master_kurikulum')->after('id');
            }
            if (!Schema::hasColumn('kurikulum', 'tingkat_id')) {
                $table->foreignId('tingkat_id')->nullable()->constrained('tingkat')->after('master_kurikulum_id');
            }
            if (!Schema::hasColumn('kurikulum', 'kkm')) {
                $table->integer('kkm')->default(0)->after('mapel_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For production safety, we don't drop everything in down() for refinement migrations
        // But for development:
        Schema::table('kurikulum', function (Blueprint $table) {
            $table->dropColumn(['master_kurikulum_id', 'tingkat_id', 'kkm']);
        });
        
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->dropColumn('kelompok');
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['kode_kelas', 'tingkat_id', 'jurusan_id', 'tahun_ajaran_id', 'wali_kelas_id']);
        });

        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->dropColumn(['semester', 'keterangan']);
        });

        Schema::dropIfExists('master_kurikulum');
        Schema::dropIfExists('jurusan');
        Schema::dropIfExists('tingkat');
    }
};
