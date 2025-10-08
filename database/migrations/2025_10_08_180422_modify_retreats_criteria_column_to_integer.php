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
            $table->unsignedBigInteger('criteria')->nullable()->change();
            $table->foreign('criteria')->references('id')->on('criteria')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retreats', function (Blueprint $table) {
            $table->dropForeign(['criteria']);
            $table->string('criteria')->nullable()->change();
        });
    }
};
