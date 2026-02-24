<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewHealthTables extends Migration
{
    public function up()
    {
        // 1. Nutrition Scans
        Schema::create('nutrition_scans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->string('product_image_path');
            $table->string('product_name')->nullable();
            $table->string('barcode')->nullable();
            $table->json('ai_nutrition_analysis');
            $table->enum('halal_status', ['Halal', 'Syubhat', 'Haram', 'Tidak Diketahui']);
            $table->integer('health_score')->default(0);
            $table->boolean('is_flagged')->default(false);
            $table->string('admin_verification')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->index('halal_status');
            $table->index('health_score');
        });

        // 2. Medical Records
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->enum('record_type', ['Lab', 'Resep', 'Diagnosis', 'Vaksinasi', 'Operasi']);
            $table->date('record_date');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('hospital_name')->nullable();
            $table->string('doctor_name')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->index(['id_user', 'record_type']);
            $table->index('record_date');
        });

        // 3. Emergency First Aid Logs
        Schema::create('emergency_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->string('emergency_type');
            $table->decimal('location_latitude', 10, 8)->nullable();
            $table->decimal('location_longitude', 11, 8)->nullable();
            $table->json('ai_guidance');
            $table->boolean('was_helpful')->nullable();
            $table->text('user_feedback')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->index('emergency_type');
        });
        
        // 4. Lab Parameters (Detail per parameter for Lab Results)
        // Note: lab_results table is already created in advanced_health_features_tables
        Schema::create('lab_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_result_id');
            $table->string('parameter_name');
            $table->decimal('user_value', 10, 2);
            $table->string('normal_range');
            $table->enum('status', ['Normal', 'Tinggi', 'Rendah']);
            $table->text('explanation');
            $table->timestamps();

            $table->foreign('lab_result_id')->references('id')->on('lab_results')->onDelete('cascade');
            $table->index('parameter_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_parameters');
        Schema::dropIfExists('emergency_logs');
        Schema::dropIfExists('medical_records');
        Schema::dropIfExists('nutrition_scans');
    }
}
