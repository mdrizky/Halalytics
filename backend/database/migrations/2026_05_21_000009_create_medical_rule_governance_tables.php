<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medical_rule_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique();
            $table->string('notes')->nullable();
            $table->json('snapshot')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('medical_rule_audits', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('table_name');
            $table->unsignedBigInteger('record_id');
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_rule_audits');
        Schema::dropIfExists('medical_rule_releases');
    }
};
