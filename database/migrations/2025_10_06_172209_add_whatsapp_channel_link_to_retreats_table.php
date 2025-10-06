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
        Schema::table('retreats', function (Blueprint $table) {
            $table->string('whatsapp_channel_link')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retreats', function (Blueprint $table) {
            $table->dropColumn('whatsapp_channel_link');
        });
    }
};
