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
        Schema::table('medical_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_profiles', 'food_allergies')) {
                $table->json('food_allergies')->nullable()->after('drug_allergies');
            }
            if (!Schema::hasColumn('medical_profiles', 'activity_level')) {
                $table->string('activity_level')->default('sedentary')->after('has_gerd');
            }
            if (!Schema::hasColumn('medical_profiles', 'daily_calories_target')) {
                $table->integer('daily_calories_target')->nullable()->after('activity_level');
            }
            if (!Schema::hasColumn('medical_profiles', 'daily_sugar_limit_g')) {
                $table->decimal('daily_sugar_limit_g', 8, 2)->nullable()->after('daily_calories_target');
            }
            if (!Schema::hasColumn('medical_profiles', 'daily_sodium_limit_mg')) {
                $table->integer('daily_sodium_limit_mg')->nullable()->after('daily_sugar_limit_g');
            }
            if (!Schema::hasColumn('medical_profiles', 'daily_fat_limit_g')) {
                $table->decimal('daily_fat_limit_g', 8, 2)->nullable()->after('daily_sodium_limit_mg');
            }
        });

        Schema::table('family_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('family_profiles', 'weight_kg')) {
                $table->decimal('weight_kg', 8, 2)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('family_profiles', 'height_cm')) {
                $table->decimal('height_cm', 8, 2)->nullable()->after('weight_kg');
            }
            if (!Schema::hasColumn('family_profiles', 'activity_level')) {
                $table->string('activity_level')->default('sedentary')->after('height_cm');
            }
            if (!Schema::hasColumn('family_profiles', 'daily_calories_target')) {
                $table->integer('daily_calories_target')->nullable()->after('activity_level');
            }
            if (!Schema::hasColumn('family_profiles', 'daily_sugar_limit_g')) {
                $table->decimal('daily_sugar_limit_g', 8, 2)->nullable()->after('daily_calories_target');
            }
            if (!Schema::hasColumn('family_profiles', 'daily_sodium_limit_mg')) {
                $table->integer('daily_sodium_limit_mg')->nullable()->after('daily_sugar_limit_g');
            }
            if (!Schema::hasColumn('family_profiles', 'daily_fat_limit_g')) {
                $table->decimal('daily_fat_limit_g', 8, 2)->nullable()->after('daily_sodium_limit_mg');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'food_allergies', 'activity_level', 'daily_calories_target',
                'daily_sugar_limit_g', 'daily_sodium_limit_mg', 'daily_fat_limit_g'
            ]);
        });

        Schema::table('family_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'weight_kg', 'height_cm', 'activity_level', 'daily_calories_target',
                'daily_sugar_limit_g', 'daily_sodium_limit_mg', 'daily_fat_limit_g'
            ]);
        });
    }
};
