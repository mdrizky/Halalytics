<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medical_symptom_rules', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->index();
            $table->string('drug_name');
            $table->string('drug_type')->default('OTC');
            $table->string('indication');
            $table->unsignedTinyInteger('severity_score')->default(1);
            $table->json('warnings')->nullable();
            $table->timestamps();
        });

        Schema::create('medical_contraindication_rules', function (Blueprint $table) {
            $table->id();
            $table->string('drug_name')->index();
            $table->string('condition_keyword')->index();
            $table->string('warning_text');
            $table->timestamps();
        });

        Schema::create('drug_interaction_blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('drug_a')->index();
            $table->string('drug_b')->index();
            $table->string('risk_level')->default('high');
            $table->string('warning_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_interaction_blacklists');
        Schema::dropIfExists('medical_contraindication_rules');
        Schema::dropIfExists('medical_symptom_rules');
    }
};

