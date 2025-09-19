<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('retreats', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('timings');
            $table->integer('seats')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('criteria', [
                'male_only', 
                'female_only', 
                'priests_only', 
                'sisters_only', 
                'youth_only', 
                'children', 
                'no_criteria'
            ])->default('no_criteria');
            $table->text('special_remarks')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('start_date');
            $table->index('end_date');
            $table->index('criteria');
            $table->index('is_featured');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('retreats');
    }
};
