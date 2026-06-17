<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_synonyms', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('main_name');
            $table->string('synonym')->unique();
            $table->string('category')->nullable(); // e.g., sweetener, preservative, colour
            $table->timestamps();

            $table->index('main_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_synonyms');
    }
};