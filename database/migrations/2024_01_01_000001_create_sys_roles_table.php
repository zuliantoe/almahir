<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create sys_roles table for RBAC system.
 * 
 * This table stores all system roles that can be assigned to users.
 * Each role has a unique name and optional description.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sys_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->unique()->comment('Role identifier: SUPER_ADMIN, GURU, etc.');
            $table->string('display_name', 100)->comment('Human readable name');
            $table->text('description')->nullable()->comment('Role description');
            $table->json('permissions')->nullable()->comment('JSON array of permission keys');
            $table->boolean('is_system')->default(false)->comment('If true, role cannot be deleted');
            $table->timestamps();

            // Index for faster lookups
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_roles');
    }
};
