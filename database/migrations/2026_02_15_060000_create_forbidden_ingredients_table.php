<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForbiddenIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forbidden_ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Carmine"
            $table->string('code')->nullable()->index(); // e.g., "E120"
            $table->json('aliases')->nullable(); // e.g., ["CI 75470", "Cochineal"]
            $table->string('type'); // "halal_haram", "health_hazard", "allergen"
            $table->string('risk_level')->default('high'); // "high", "medium", "low"
            $table->text('reason'); // Why is it forbidden?
            $table->text('description')->nullable();
            $table->string('source')->nullable(); // "MUI", "BPOM", "FDA"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forbidden_ingredients');
    }
}
