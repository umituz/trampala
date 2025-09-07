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
        Schema::create('categories', function (Blueprint $table) {
            // Primary key UUID
            $table->uuid()->primary()->comment('Primary key UUID');
            
            // Basic fields
            $table->string('name')->comment('Category name');
            $table->string('slug')->unique()->comment('URL-friendly slug');
            $table->text('description')->nullable()->comment('Category description');
            
            // Status field
            $table->tinyInteger('status')->default(1)->comment('Status (0=inactive, 1=active)');
            
            // Self-referencing parent relationship (will be added after table creation)
            $table->uuid('parent_uuid')->nullable()->comment('Parent category reference for hierarchical structure');
            
            // Standard timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('status');
            $table->index('slug');
            $table->index('parent_uuid');
            $table->index(['status', 'created_at']);
            $table->index(['parent_uuid', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
