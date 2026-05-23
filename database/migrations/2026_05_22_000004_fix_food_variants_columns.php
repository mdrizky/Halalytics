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
        Schema::table('food_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('food_variants', 'name')) {
                $table->string('name')->after('street_food_id');
            }
            if (!Schema::hasColumn('food_variants', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('food_variants', 'ingredients')) {
                $table->json('ingredients')->nullable()->after('description');
            }
            if (!Schema::hasColumn('food_variants', 'calories')) {
                $table->integer('calories')->default(0)->after('ingredients');
            }
            if (!Schema::hasColumn('food_variants', 'price_adjustment')) {
                $table->decimal('price_adjustment', 10, 2)->default(0)->after('calories');
            }
            if (!Schema::hasColumn('food_variants', 'serving_size_grams')) {
                $table->integer('serving_size_grams')->default(0)->after('price_adjustment');
            }
            if (!Schema::hasColumn('food_variants', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('serving_size_grams');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_variants', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'ingredients', 'calories', 'price_adjustment', 'serving_size_grams', 'is_available']);
        });
    }
};
