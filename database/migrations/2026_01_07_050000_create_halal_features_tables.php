<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHalalFeaturesTables extends Migration
{
    public function up()
    {
        // 1. Halal Certificates
        Schema::create('halal_certificates', function (Blueprint $table) {
            $table->id('id_certificate');
            $table->string('certificate_number')->unique();
            $table->string('product_name');
            $table->string('manufacturer');
            $table->string('certifying_body'); // MUI, BPJPH, etc
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('status')->default('active'); // active, expired, suspended
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['certifying_body', 'status']);
        });

        // 2. Product Halal Status (link to products table)
        Schema::create('product_halal_status', function (Blueprint $table) {
            $table->id('id_halal_status');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
            
            $table->unsignedBigInteger('certificate_id')->nullable();
            $table->foreign('certificate_id')->references('id_certificate')->on('halal_certificates')->onDelete('set null');
            
            $table->enum('halal_status', ['halal', 'syubhat', 'non_halal', 'not_verified'])->default('not_verified');
            $table->text('analysis_notes')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable(); // admin who verified
            $table->foreign('verified_by')->references('id_user')->on('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
        });

        // 3. Ingredients Database
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id('id_ingredient');
            $table->string('name');
            $table->string('e_number')->nullable(); // E-codes like E120, E441
            $table->enum('halal_status', ['halal', 'haram', 'syubhat', 'unknown'])->default('unknown');
            $table->enum('health_risk', ['safe', 'low_risk', 'high_risk', 'dangerous'])->default('safe');
            $table->text('description')->nullable();
            $table->text('sources')->nullable(); // animal, plant, synthetic
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->unique(['name']);
            $table->unique(['e_number']);
            $table->index(['halal_status', 'health_risk']);
        });

        // 4. Product Ingredients (Many-to-Many)
        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id('id_product_ingredient');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
            
            $table->unsignedBigInteger('ingredient_id');
            $table->foreign('ingredient_id')->references('id_ingredient')->on('ingredients')->onDelete('cascade');
            
            $table->decimal('percentage', 5, 2)->nullable(); // percentage in product
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['product_id', 'ingredient_id']);
        });

        // 5. User Favorites
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id('id_favorite');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
            
            $table->timestamps();
            
            $table->unique(['user_id', 'product_id']);
        });

        // 6. Health Scores
        Schema::create('health_scores', function (Blueprint $table) {
            $table->id('id_health_score');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
            
            $table->integer('overall_score')->default(0); // 0-100
            $table->integer('sugar_score')->default(0);
            $table->integer('fat_score')->default(0);
            $table->integer('salt_score')->default(0);
            $table->integer('additive_score')->default(0);
            $table->text('analysis')->nullable();
            $table->enum('grade', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->timestamps();
            
            $table->unique(['product_id']);
        });

        // 7. Allergens
        Schema::create('allergens', function (Blueprint $table) {
            $table->id('id_allergen');
            $table->string('name');
            $table->string('code')->unique(); // milk, egg, peanut, etc
            $table->text('description')->nullable();
            $table->string('severity')->default('medium'); // low, medium, high, severe
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 8. Product Allergens
        Schema::create('product_allergens', function (Blueprint $table) {
            $table->id('id_product_allergen');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
            
            $table->unsignedBigInteger('allergen_id');
            $table->foreign('allergen_id')->references('id_allergen')->on('allergens')->onDelete('cascade');
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['product_id', 'allergen_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_allergens');
        Schema::dropIfExists('allergens');
        Schema::dropIfExists('health_scores');
        Schema::dropIfExists('user_favorites');
        Schema::dropIfExists('product_ingredients');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('product_halal_status');
        Schema::dropIfExists('halal_certificates');
    }
}
