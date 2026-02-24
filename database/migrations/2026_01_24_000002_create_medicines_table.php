<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master Table for Medicines
        Schema::create('medicines', function (Blueprint $table) {
            $table->id('id_medicine'); 
            $table->string('name')->unique();
            $table->string('generic_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->text('indications')->nullable(); 
            $table->text('ingredients')->nullable();
            $table->text('dosage_info')->nullable();
            $table->string('frequency_per_day')->nullable();
            $table->string('max_daily_dose')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('contraindications')->nullable();
            $table->string('route')->nullable();
            $table->string('halal_status', 20)->default('syubhat');
            $table->string('halal_certificate_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('country_origin')->nullable();
            $table->string('dosage_form')->nullable(); 
            $table->string('category')->nullable();
            $table->string('source')->default('local');
            $table->boolean('is_prescription_required')->default(false);
            $table->boolean('is_verified_by_admin')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // User Personal Schedule
        Schema::create('user_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->foreignId('medicine_id')->nullable()->constrained('medicines', 'id_medicine')->nullOnDelete(); 
            $table->string('custom_name')->nullable(); // If med not in master DB
            $table->string('dosage')->nullable(); // e.g., "1 Tablet"
            $table->time('reminder_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_medicines');
        Schema::dropIfExists('medicines');
    }
};
