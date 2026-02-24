<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('avatar_url');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('phone');
            }
            
            // Health Preferences
            if (!Schema::hasColumn('users', 'dietary_preferences')) {
                $table->json('dietary_preferences')->nullable()->after('bio'); // gluten_free, nut_allergy, etc
            }
            if (!Schema::hasColumn('users', 'allergies')) {
                $table->json('allergies')->nullable()->after('dietary_preferences'); // list of allergies
            }
            if (!Schema::hasColumn('users', 'notifications_enabled')) {
                $table->boolean('notifications_enabled')->default(true)->after('allergies');
            }
            
            // Statistics
            if (!Schema::hasColumn('users', 'total_scans')) {
                $table->integer('total_scans')->default(0)->after('notifications_enabled');
            }
            if (!Schema::hasColumn('users', 'halal_products_count')) {
                $table->integer('halal_products_count')->default(0)->after('total_scans');
            }
            
            // Profile Settings
            if (!Schema::hasColumn('users', 'profile_visibility')) {
                $table->string('profile_visibility')->default('public')->after('halal_products_count'); // public, private, friends
            }
            if (!Schema::hasColumn('users', 'show_health_tips')) {
                $table->boolean('show_health_tips')->default(true)->after('profile_visibility');
            }
            
            // Indexes
            $table->index('total_scans');
            $table->index('halal_products_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Profile Information
            $table->dropColumn(['avatar_url', 'birth_date', 'gender', 'phone', 'bio']);
            
            // Health Preferences
            $table->dropColumn(['dietary_preferences', 'allergies', 'notifications_enabled']);
            
            // Statistics
            $table->dropColumn(['total_scans', 'halal_products_count']);
            
            // Profile Settings
            $table->dropColumn(['profile_visibility', 'show_health_tips']);
        });
    }
}
