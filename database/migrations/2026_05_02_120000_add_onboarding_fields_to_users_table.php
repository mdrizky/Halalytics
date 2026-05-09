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
        Schema::table('users', function (Blueprint $table) {
            // Onboarding progress tracking
            $table->json('onboarding_progress')->nullable()->after('social_provider');
            $table->integer('onboarding_points')->default(0)->after('onboarding_progress');
            $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_points');
            $table->string('onboarding_level')->default('Newcomer')->after('onboarding_completed_at');
            
            // Engagement tracking
            $table->integer('login_streak')->default(0)->after('onboarding_level');
            $table->timestamp('last_login_date')->nullable()->after('login_streak');
            $table->integer('total_articles_read')->default(0)->after('last_login_date');
            $table->integer('total_categories_explored')->default(0)->after('total_articles_read');
            
            // Achievement tracking
            $table->json('achievements')->nullable()->after('total_categories_explored');
            $table->json('milestones')->nullable()->after('achievements');
            
            // Indexes for performance
            $table->index('onboarding_level');
            $table->index('onboarding_points');
            $table->index('login_streak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'onboarding_progress',
                'onboarding_points',
                'onboarding_completed_at',
                'onboarding_level',
                'login_streak',
                'last_login_date',
                'total_articles_read',
                'total_categories_explored',
                'achievements',
                'milestones'
            ]);
        });
    }
};
