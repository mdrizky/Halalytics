<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table untuk data makanan sajian (non-kemasan)
     * seperti nasi goreng, mie, martabak, dll
     */
    public function up()
    {
        Schema::create('street_foods', function (Blueprint $table) {
            $table->id();
            
            // Basic info
            $table->string('name'); // "Nasi Goreng"
            $table->string('name_en')->nullable(); // "Fried Rice"
            $table->string('slug')->unique(); // "nasi-goreng"
            $table->text('description')->nullable();
            $table->string('category'); // "Nasi", "Mie", "Gorengan", "Berkuah"
            
            // Nutrition per 1 PORSI STANDAR (bukan per 100g)
            $table->decimal('calories_min', 8, 2); // kalori minimum
            $table->decimal('calories_max', 8, 2); // kalori maksimum
            $table->decimal('calories_typical', 8, 2); // kalori paling umum
            
            $table->decimal('protein', 8, 2); // gram
            $table->decimal('carbs', 8, 2); // gram
            $table->decimal('fat', 8, 2); // gram
            $table->decimal('fiber', 8, 2)->nullable(); // gram
            $table->decimal('sugar', 8, 2)->nullable(); // gram
            $table->decimal('sodium', 8, 2)->nullable(); // mg
            
            // Serving info
            $table->integer('serving_size_grams')->default(250); // gram per porsi
            $table->string('serving_description')->default('1 porsi'); // "1 piring", "1 mangkok"
            
            // Halal status
            $table->enum('halal_status', ['halal_umum', 'syubhat', 'haram', 'tergantung_bahan'])
                  ->default('halal_umum');
            $table->text('halal_notes')->nullable();
            
            // Health metadata
            $table->json('health_tags')->nullable(); // ["tinggi_kalori", "tinggi_karbohidrat"]
            $table->text('health_notes')->nullable();
            
            // AI Recognition keywords
            $table->json('ai_keywords')->nullable(); // ["nasi", "goreng", "rice", "fried"]
            $table->json('common_ingredients')->nullable(); // ["nasi", "telur", "kecap"]
            
            // Media
            $table->string('image_url')->nullable();
            
            // Meta
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('search_count')->default(0); // tracking popularity
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('street_foods');
    }
};
