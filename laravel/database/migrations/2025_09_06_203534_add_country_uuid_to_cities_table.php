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
        Schema::table('cities', function (Blueprint $table) {
            $table->uuid('country_uuid')->nullable()->after('uuid');
            $table->foreign('country_uuid')->references('uuid')->on('countries')->onDelete('restrict');
            $table->index('country_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign(['country_uuid']);
            $table->dropIndex(['country_uuid']);
            $table->dropColumn('country_uuid');
        });
    }
};
