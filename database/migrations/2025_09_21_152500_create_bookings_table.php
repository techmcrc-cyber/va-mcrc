<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id')->unique(); // RB1, RB2, etc.
            $table->foreignId('retreat_id')->constrained()->onDelete('cascade');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('whatsapp_number');
            $table->integer('age');
            $table->string('email');
            $table->text('address');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('city');
            $table->string('state');
            $table->string('diocese')->nullable();
            $table->string('parish')->nullable();
            $table->string('congregation')->nullable();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->integer('additional_participants')->default(0);
            $table->text('special_remarks')->nullable();
            $table->string('flag')->nullable(); // For RECURRENT_BOOKING, CRITERIA_FAILED, etc.
            $table->integer('participant_number')->default(1); // 1 for primary, 2+ for additional
            
            // For tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('booking_id');
            $table->index('whatsapp_number');
            $table->index('email');
            $table->index('flag');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
