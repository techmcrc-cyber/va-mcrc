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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('need', ['retreat', 'custom']);
            $table->unsignedBigInteger('retreat_id')->nullable();
            $table->string('heading');
            $table->string('subject');
            $table->text('body');
            $table->text('additional_users_emails')->nullable();
            $table->unsignedInteger('total_recipients')->default(0);
            $table->enum('status', ['pending', 'queued', 'processing', 'sent', 'failed', 'partially_sent'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('retreat_id')->references('id')->on('retreats')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('need');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
