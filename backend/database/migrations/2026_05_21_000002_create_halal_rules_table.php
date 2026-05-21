<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('halal_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('rule_type', 50);
            $table->string('keyword', 150);
            $table->enum('status', ['halal', 'syubhat', 'haram', 'unknown'])->default('unknown');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halal_rules');
    }
};
