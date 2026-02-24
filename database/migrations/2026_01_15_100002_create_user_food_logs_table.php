<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table untuk riwayat scan makanan sajian user
     */
    public function up()
    {
        Schema::create('user_food_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('street_food_id');
            $table->unsignedBigInteger('food_variant_id')->nullable();
            
            // Foreign keys
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('street_food_id')->references('id')->on('street_foods')->onDelete('cascade');
            $table->foreign('food_variant_id')->references('id')->on('food_variants')->onDelete('set null');
            
            // Input method
            $table->enum('input_method', ['photo', 'text', 'manual'])->default('text');
            $table->decimal('ai_confidence', 3, 2)->nullable(); // 0.00-1.00
            
            // Portion
            $table->decimal('portion_multiplier', 4, 2)->default(1.00); // 0.5, 1.0, 1.5, 2.0
            
            // Calculated nutrition (final result)
            $table->integer('total_calories');
            $table->decimal('total_protein', 8, 2);
            $table->decimal('total_carbs', 8, 2);
            $table->decimal('total_fat', 8, 2);
            
            // Meal context
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack'])->nullable();
            $table->timestamp('consumed_at')->useCurrent();
            
            // Notes
            $table->text('user_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('user_food_logs');
    }
};
