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
        Schema::create('cities', function (Blueprint $table) {
            // Primary key UUID
            $table->uuid()->primary()->comment('Primary key UUID');
            
            // Basic fields
            $table->string('name')->comment('City name');
            $table->string('plate_code', 2)->unique()->comment('Vehicle plate code');
            
            // Status field
            $table->tinyInteger('status')->default(1)->comment('Status (0=inactive, 1=active)');
            
            // Standard timestamps
            $table->timestamps();
            
            // Soft deletes
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('status');
            $table->index('plate_code');
            $table->index(['status', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
