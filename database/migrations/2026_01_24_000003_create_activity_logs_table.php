<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users', 'id_user')->nullOnDelete(); // Nullable for guest actions
            $table->string('action'); // e.g., "SCAN_FOOD", "SCAN_EMERGENCY_QR", "ADD_MEDICINE"
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('device_info')->nullable();
            $table->boolean('is_risk_detected')->default(false); // Quick filter for Admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
