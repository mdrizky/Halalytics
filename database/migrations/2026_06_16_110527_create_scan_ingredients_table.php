<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_history_id')->constrained('scan_histories')->onDelete('cascade');
            $table->string('ingredient_name');
            $table->string('status')->comment('halal, syubhat, haram');
            $table->text('warning')->nullable();
            $table->string('e_code')->nullable();
            $table->string('category')->nullable();
            $table->string('source_type')->nullable()->comment('ingredient, additive, e_number');
            $table->timestamps();

            $table->index('scan_history_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_ingredients');
    }
};
