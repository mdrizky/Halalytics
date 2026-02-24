<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table untuk varian makanan
     * Contoh: Nasi Goreng Telur, Nasi Goreng Ayam, Nasi Goreng Seafood
     */
    public function up()
    {
        Schema::create('food_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('street_food_id')->constrained()->onDelete('cascade');
            
            $table->string('variant_name'); // "Nasi Goreng Telur"
            $table->string('variant_type'); // "topping", "cooking_method", "size", "basic"
            
            // Nutrition adjustments (modifier dari base food)
            $table->decimal('calories_modifier', 8, 2)->default(0); // +50 kalori untuk telur
            $table->decimal('protein_modifier', 8, 2)->default(0); // +6g untuk ayam
            $table->decimal('carbs_modifier', 8, 2)->default(0);
            $table->decimal('fat_modifier', 8, 2)->default(0);
            
            // Price modifier (opsional, untuk referensi)
            $table->decimal('price_modifier', 8, 2)->default(0);
            
            $table->boolean('is_default')->default(false);
            $table->integer('popularity')->default(0); // ranking varian
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('food_variants');
    }
};
