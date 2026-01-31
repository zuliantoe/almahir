<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create sys_users table for RBAC system.
 * 
 * This table stores all system users with:
 * - UUID as primary key for better security and distribution
 * - Polymorphic ref_id for linking to Student/Staff/Teacher entities
 * - Standard authentication fields
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sys_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Basic authentication fields
            $table->string('username', 50)->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            
            // Profile information
            $table->string('name')->comment('Display name');
            $table->string('avatar')->nullable()->comment('Profile picture path');
            $table->string('phone', 20)->nullable();
            
            // Polymorphic relationship to link user to Siswa/Guru/Staff
            $table->uuid('ref_id')->nullable()->comment('Polymorphic reference ID');
            $table->string('ref_type', 100)->nullable()->comment('Polymorphic reference type (e.g., Modules\\Siswa\\Models\\Siswa)');
            
            // Account status
            $table->enum('account_status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['ref_id', 'ref_type'], 'idx_sys_users_ref');
            $table->index('account_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_users');
    }
};
