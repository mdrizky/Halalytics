<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvancedHealthFeaturesTables extends Migration
{
    public function up()
    {
        // 1. Drugs (Master Data) - Skipped as 'medicines' table already exists
        // if (!Schema::hasTable('drugs')) { ... }

        // 2. Pill Identifications
        Schema::create('pill_identifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_medicine')->nullable(); // Changed from drug_id
            $table->string('shape')->nullable();
            $table->string('color')->nullable();
            $table->string('imprint')->nullable();
            $table->string('size')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
            
            // Assuming medicines table uses id_medicine as PK. If not, use id.
            // Based on Model Medicine.php: protected $primaryKey = 'id_medicine';
            $table->foreign('id_medicine')->references('id_medicine')->on('medicines')->onDelete('cascade');
        });

        // 3. Drug Interactions
        Schema::create('drug_interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medicine_a_id'); // Changed from drug_a_id
            $table->unsignedBigInteger('medicine_b_id'); // Changed from drug_b_id
            $table->enum('severity', ['minor', 'moderate', 'major'])->default('moderate');
            $table->text('description')->nullable();
            $table->text('recommendation')->nullable();
            $table->boolean('ai_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('medicine_a_id')->references('id_medicine')->on('medicines')->onDelete('cascade');
            $table->foreign('medicine_b_id')->references('id_medicine')->on('medicines')->onDelete('cascade');
            $table->unique(['medicine_a_id', 'medicine_b_id']);
        });

        // 4. Medicine Reminders (Renamed from medication_reminders)
        Schema::create('medicine_reminders', function (Blueprint $table) {
            $table->bigIncrements('id_reminder'); // Changed from id() to match Model PK
            $table->unsignedBigInteger('id_user'); // Changed from user_id
            $table->unsignedBigInteger('id_medicine'); // Changed from drug_id
            $table->string('dosage')->nullable();
            $table->integer('frequency_per_day')->default(1); // Added based on Controller usage
            $table->json('schedule_times')->nullable(); // Changed from time_slots to schedule_times based on Model
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->json('taken_times')->nullable(); // Added based on Model usage
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_medicine')->references('id_medicine')->on('medicines')->onDelete('cascade');
        });

        // 5. Medication Logs (Could be MedicineLog but sticking to medication_logs if no conflict)
        Schema::create('medication_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_reminder'); // Changed from reminder_id
            $table->unsignedBigInteger('id_user'); // Changed from user_id
            $table->timestamp('taken_at');
            $table->enum('status', ['taken', 'skipped', 'late'])->default('taken');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('id_reminder')->references('id_reminder')->on('medicine_reminders')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });

        // 6. Lab Results
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user'); // Changed from user_id
            $table->date('test_date');
            $table->string('test_type')->nullable(); // "Kolesterol", "Gula Darah"
            $table->decimal('value', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('normal_range_min', 10, 2)->nullable();
            $table->decimal('normal_range_max', 10, 2)->nullable();
            $table->enum('status', ['normal', 'low', 'high'])->default('normal');
            $table->text('ai_analysis')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });

        // 7. Health Metrics
        Schema::create('health_trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user'); // Changed from user_id
            $table->enum('metric_type', ['weight', 'blood_pressure', 'blood_sugar', 'cholesterol']);
            $table->string('value');
            $table->timestamp('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });

        // 8. AI Query Logs
        Schema::create('ai_query_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user')->nullable(); // Changed from user_id
            $table->enum('query_type', ['interaction_check', 'pill_identify', 'lab_analysis', 'halal_alternative']);
            $table->json('input_data')->nullable();
            $table->text('ai_response')->nullable();
            $table->integer('processing_time')->nullable(); // ms
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('set null');
        });

        // 9. Halal Alternatives
        Schema::create('halal_alternatives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_medicine_id'); // Changed from original_drug_id
            $table->unsignedBigInteger('alternative_medicine_id'); // Changed from alternative_drug_id
            $table->text('reason')->nullable();
            $table->decimal('ai_confidence', 5, 2)->nullable();
            $table->boolean('verified_by_admin')->default(false);
            $table->timestamps();

            $table->foreign('original_medicine_id')->references('id_medicine')->on('medicines')->onDelete('cascade');
            $table->foreign('alternative_medicine_id')->references('id_medicine')->on('medicines')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('halal_alternatives');
        Schema::dropIfExists('ai_query_logs');
        Schema::dropIfExists('health_trackings');
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('medication_logs');
        Schema::dropIfExists('medicine_reminders'); // Changed from medication_reminders
        Schema::dropIfExists('drug_interactions');
        Schema::dropIfExists('pill_identifications');
        // Schema::dropIfExists('drugs');
    }
}
