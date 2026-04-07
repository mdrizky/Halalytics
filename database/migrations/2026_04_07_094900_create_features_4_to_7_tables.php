<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // FASE 4: OFFLINE OCR — Haram Ingredients Sync Table
        // ============================================================
        if (!Schema::hasTable('haram_ingredients')) {
            Schema::create('haram_ingredients', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->json('aliases')->nullable();
                $table->enum('category', ['haram', 'syubhat', 'alergen_umum'])->default('haram');
                $table->tinyInteger('severity')->default(2); // 1=info, 2=warning, 3=danger
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('category');
                $table->index('is_active');
            });
        }

        if (!Schema::hasTable('ocr_scan_histories')) {
            Schema::create('ocr_scan_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('product_name')->nullable();
                $table->text('raw_text');
                $table->json('detected_haram')->nullable();
                $table->tinyInteger('severity')->nullable();
                $table->timestamp('scanned_at')->useCurrent();
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->index(['user_id', 'scanned_at']);
            });
        }

        // ============================================================
        // FASE 5: SMART NUTRITION LOGGING
        // ============================================================
        if (!Schema::hasTable('daily_nutrition_logs')) {
            Schema::create('daily_nutrition_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->enum('meal_type', ['sarapan', 'makan_siang', 'makan_malam', 'camilan']);
                $table->json('food_items')->nullable();
                $table->integer('total_calories')->default(0);
                $table->decimal('total_carbs', 8, 2)->default(0);
                $table->decimal('total_protein', 8, 2)->default(0);
                $table->decimal('total_fat', 8, 2)->default(0);
                $table->string('image_path')->nullable();
                $table->text('gemini_response')->nullable();
                $table->date('logged_at');
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->index(['user_id', 'logged_at']);
            });
        }

        if (!Schema::hasTable('nutrition_goals')) {
            Schema::create('nutrition_goals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->integer('daily_calories')->default(2000);
                $table->decimal('daily_carbs', 8, 2)->default(250);
                $table->decimal('daily_protein', 8, 2)->default(60);
                $table->decimal('daily_fat', 8, 2)->default(65);
                $table->enum('goal_type', ['diet', 'maintain', 'bulking'])->default('maintain');
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
            });
        }

        // ============================================================
        // FASE 6: RECIPE AI
        // ============================================================
        if (!Schema::hasTable('recipes')) {
            Schema::create('recipes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->json('ingredients'); // [{name, amount, unit}]
                $table->json('steps');       // [string]
                $table->string('category')->nullable();
                $table->boolean('is_halal_verified')->default(false);
                $table->string('image_path')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->nullOnDelete();
                $table->index('category');
            });
        }

        if (!Schema::hasTable('recipe_substitutions')) {
            Schema::create('recipe_substitutions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('recipe_id');
                $table->json('original_ingredients');
                $table->json('substitution_result');
                $table->unsignedBigInteger('requested_by')->nullable();
                $table->timestamps();

                $table->foreign('recipe_id')->references('id')->on('recipes')->cascadeOnDelete();
                $table->foreign('requested_by')->references('id_user')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('recipe_substitutions');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('nutrition_goals');
        Schema::dropIfExists('daily_nutrition_logs');
        Schema::dropIfExists('ocr_scan_histories');
        Schema::dropIfExists('haram_ingredients');
        Schema::enableForeignKeyConstraints();
    }
};
