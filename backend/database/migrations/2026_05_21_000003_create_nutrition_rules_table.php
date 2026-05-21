<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nutrition_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('metric', 80);
            $table->decimal('threshold', 12, 4);
            $table->string('unit', 20);
            $table->enum('severity', ['low', 'moderate', 'high', 'critical'])->default('moderate');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_rules');
    }
};
