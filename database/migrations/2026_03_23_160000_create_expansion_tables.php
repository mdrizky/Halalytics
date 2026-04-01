<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════
        // 1. USER POINTS — Gamification & Leaderboard
        // ═══════════════════════════════════════════════
        Schema::create('user_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('points')->default(0);
            $table->string('source', 50); // scan, contribution, streak, referral, achievement
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('source');
            $table->index('created_at');
        });

        // ═══════════════════════════════════════════════
        // 2. AI USAGE LOGS — Track Gemini usage & costs
        // ═══════════════════════════════════════════════
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('feature', 50); // health_assistant, halal_analysis, ocr, etc.
            $table->string('model', 50)->default('gemini-1.5-flash');
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->float('response_time_ms')->default(0);
            $table->enum('status', ['success', 'error', 'fallback'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['feature', 'created_at']);
            $table->index('status');
        });

        // ═══════════════════════════════════════════════
        // 3. API HEALTH LOGS — External API monitoring
        // ═══════════════════════════════════════════════
        Schema::create('api_health_logs', function (Blueprint $table) {
            $table->id();
            $table->string('api_name', 30); // gemini, fda, bpom, openfoodfacts, fcm
            $table->enum('status', ['up', 'down', 'slow', 'degraded'])->default('up');
            $table->float('latency_ms')->nullable();
            $table->integer('http_status')->nullable();
            $table->text('error_details')->nullable();
            $table->timestamp('checked_at');

            $table->index(['api_name', 'checked_at']);
            $table->index('status');
        });

        // ═══════════════════════════════════════════════
        // 4. NOTIFICATION CAMPAIGNS — Push notification campaigns
        // ═══════════════════════════════════════════════
        Schema::create('notification_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->text('body');
            $table->string('image_url')->nullable();
            $table->string('action_url')->nullable(); // deep link
            $table->json('target_segment')->nullable(); // criteria for targeting
            $table->integer('target_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_at');
        });

        // ═══════════════════════════════════════════════
        // 5. HALAL CERTIFICATES — MUI/LPPOM certificate management
        // ═══════════════════════════════════════════════
        Schema::create('halal_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name');
            $table->string('manufacturer');
            $table->string('issuing_body', 30)->default('MUI'); // MUI, LPPOM, BPJPH
            $table->date('issued_at');
            $table->date('expires_at');
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->string('certificate_file')->nullable(); // PDF path
            $table->json('qr_data')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('status');
            $table->index('expires_at');
        });

        // ═══════════════════════════════════════════════
        // 6. AI PROMPTS — Admin-editable AI prompts
        // ═══════════════════════════════════════════════
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('feature_key', 50)->unique(); // health_assistant, halal_analysis, etc.
            $table->string('feature_name');
            $table->longText('system_prompt');
            $table->longText('user_prompt_template')->nullable();
            $table->float('temperature')->default(0.7);
            $table->integer('max_tokens')->default(2048);
            $table->string('model', 50)->default('gemini-1.5-flash');
            $table->boolean('is_active')->default(true);
            $table->integer('version')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ═══════════════════════════════════════════════
        // 7. USER NOTIFICATION PREFERENCES — Granular notification settings
        // ═══════════════════════════════════════════════
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->boolean('medication_reminders')->default(true);
            $table->boolean('promo_deals')->default(true);
            $table->boolean('weekly_report')->default(true);
            $table->boolean('favorite_updates')->default(true);
            $table->boolean('new_products')->default(true);
            $table->boolean('watchlist_alerts')->default(true);
            $table->boolean('security_alerts')->default(true);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
        Schema::dropIfExists('ai_prompts');
        Schema::dropIfExists('halal_certificates');
        Schema::dropIfExists('notification_campaigns');
        Schema::dropIfExists('api_health_logs');
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('user_points');
    }
};
