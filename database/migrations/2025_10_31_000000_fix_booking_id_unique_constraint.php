<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop the unique constraint on booking_id
            $table->dropUnique(['booking_id']);
            
            // Keep the index for performance but remove uniqueness
            // The index already exists from the original migration
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Restore the unique constraint if rolling back
            $table->unique('booking_id');
        });
    }
};
