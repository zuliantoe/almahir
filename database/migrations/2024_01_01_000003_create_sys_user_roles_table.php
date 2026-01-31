<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create sys_user_roles pivot table for RBAC system.
 * 
 * This table implements many-to-many relationship between users and roles,
 * allowing a user to have multiple roles and a role to be assigned to
 * multiple users.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sys_user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('role_id');
            $table->uuid('assigned_by')->nullable()->comment('User who assigned this role');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('sys_users')
                  ->onDelete('cascade');
                  
            $table->foreign('role_id')
                  ->references('id')
                  ->on('sys_roles')
                  ->onDelete('cascade');
                  
            $table->foreign('assigned_by')
                  ->references('id')
                  ->on('sys_users')
                  ->onDelete('set null');

            // Unique constraint to prevent duplicate role assignments
            $table->unique(['user_id', 'role_id'], 'unique_user_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_user_roles');
    }
};
