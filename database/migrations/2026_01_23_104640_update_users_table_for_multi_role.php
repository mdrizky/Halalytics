<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            }
            if (!Schema::hasColumn('users', 'weight_kg')) {
                $table->integer('weight_kg')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'blood_type')) {
                $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->after('weight_kg');
            }
            if (!Schema::hasColumn('users', 'has_diabetes')) {
                $table->boolean('has_diabetes')->default(false)->after('blood_type');
            }
            // Check if emergency_contact exists
            if (!Schema::hasColumn('users', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            // Note: allergies column might already exist from previous migration, check handled by Schema::hasColumn inside add_profile_fields
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'weight_kg', 'blood_type', 'has_diabetes', 'emergency_contact', 'is_active']);
        });
    }
};
