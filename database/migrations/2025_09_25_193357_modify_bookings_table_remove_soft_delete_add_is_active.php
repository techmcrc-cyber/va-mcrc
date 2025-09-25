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
        Schema::table('bookings', function (Blueprint $table) {
            // Add is_active column with default value true
            $table->boolean('is_active')->default(true)->after('flag');
            
            // Remove soft delete columns if they exist
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Remove is_active column
            $table->dropColumn('is_active');
            
            // Add back soft delete columns
            $table->softDeletes();
        });
    }
};
