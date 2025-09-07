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
        Schema::create('listings', function (Blueprint $table) {
            // Primary key UUID
            $table->uuid()->primary()->comment('Primary key UUID');
            
            // Unique listing number (auto-generated, non-editable)
            $table->string('unique_number')->unique()->comment('Auto-generated unique listing number');
            
            // Basic listing fields
            $table->string('name')->comment('Listing name');
            $table->text('description')->comment('Listing description');
            $table->string('image_path')->comment('Required listing image path');
            
            // Foreign keys
            $table->foreignUuid('category_uuid')->constrained('categories', 'uuid')->comment('Category reference');
            $table->foreignUuid('city_uuid')->constrained('cities', 'uuid')->comment('City reference');
            $table->foreignUuid('district_uuid')->constrained('districts', 'uuid')->comment('District reference');
            $table->foreignUuid('user_uuid')->constrained('users', 'uuid')->comment('Listing creator reference');
            
            // Status and approval system
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('Listing approval status');
            $table->foreignUuid('approved_by')->nullable()->constrained('users', 'uuid')->comment('Admin who approved the listing');
            $table->timestamp('approved_at')->nullable()->comment('Approval timestamp');
            $table->text('rejection_reason')->nullable()->comment('Reason for rejection if status is rejected');
            
            // Standard timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('unique_number');
            $table->index('status');
            $table->index('category_uuid');
            $table->index('city_uuid');
            $table->index('district_uuid');
            $table->index('user_uuid');
            $table->index('approved_by');
            $table->index(['status', 'created_at']);
            $table->index(['category_uuid', 'status']);
            $table->index(['city_uuid', 'district_uuid', 'status']);
            $table->index(['user_uuid', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
