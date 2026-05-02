<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. blood_events
        Schema::create('blood_events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('location', 255);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('address');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('quota');
            $table->integer('registered_count')->default(0);
            $table->string('organizer', 255);
            $table->string('contact_phone', 20)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->enum('status', ['draft', 'active', 'ongoing', 'completed', 'cancelled'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id_user')->on('users')->onDelete('set null');
        });

        // 2. donor_appointments
        Schema::create('donor_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            $table->string('qr_code', 100)->unique();
            $table->integer('queue_number');
            $table->enum('status', ['pending', 'checked_in', 'approved', 'rejected', 'no_show'])->default('pending');
            $table->boolean('screening_passed')->nullable();
            $table->text('screening_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('hemoglobin', 4, 1)->nullable();
            $table->string('blood_pressure', 20)->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('blood_events')->onDelete('cascade');
        });

        // 3. blood_stocks
        Schema::create('blood_stocks', function (Blueprint $table) {
            $table->id();
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'A', 'B', 'AB', 'O']);
            $table->integer('volume_ml');
            $table->integer('bags_count')->default(1);
            $table->unsignedBigInteger('source_appointment_id')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->date('collected_date');
            $table->date('expiry_date');
            $table->string('location', 255)->nullable();
            $table->enum('status', ['available', 'reserved', 'used', 'expired'])->default('available');
            $table->timestamps();

            $table->foreign('source_appointment_id')->references('id')->on('donor_appointments')->onDelete('set null');
            $table->foreign('event_id')->references('id')->on('blood_events')->onDelete('set null');
        });

        // 4. blood_emergency_requests
        Schema::create('blood_emergency_requests', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_name', 255);
            $table->enum('blood_type_needed', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'A', 'B', 'AB', 'O']);
            $table->integer('bags_needed');
            $table->enum('urgency_level', ['critical', 'high', 'medium'])->default('high');
            $table->string('contact_person', 255);
            $table->string('contact_phone', 20);
            $table->text('notes')->nullable();
            $table->boolean('is_fulfilled')->default(false);
            $table->timestamp('fulfilled_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id_user')->on('users')->onDelete('set null');
        });

        // 5. donor_rewards
        Schema::create('donor_rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->integer('points_earned')->default(10);
            $table->string('badge_awarded', 100)->nullable();
            $table->timestamp('awarded_at')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('donor_appointments')->onDelete('set null');
        });

        // 6. Alter users table
        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_donor_count')->default(0);
            $table->integer('total_donor_points')->default(0);
            $table->date('last_donor_date')->nullable();
            $table->date('next_eligible_date')->nullable();
            $table->string('fcm_token', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_donor_count', 'total_donor_points', 'last_donor_date', 'next_eligible_date', 'fcm_token']);
        });
        
        Schema::dropIfExists('donor_rewards');
        Schema::dropIfExists('blood_emergency_requests');
        Schema::dropIfExists('blood_stocks');
        Schema::dropIfExists('donor_appointments');
        Schema::dropIfExists('blood_events');
    }
};
