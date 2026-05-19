<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('food_additives')) {
            Schema::create('food_additives', function (Blueprint $table) {
                $table->id();
                $table->string('code', 20)->unique();
                $table->string('name', 200)->nullable();
                $table->string('function', 100)->nullable();
                $table->enum('halal_status', ['halal', 'syubhat', 'haram', 'unknown'])->default('unknown');
                $table->enum('health_risk', ['low', 'moderate', 'high', 'very_high'])->default('low');
                $table->text('description')->nullable();
                $table->string('source', 300)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('nutrition_rules')) {
            Schema::create('nutrition_rules', function (Blueprint $table) {
                $table->id();
                $table->string('rule_key', 80)->unique();
                $table->string('label');
                $table->decimal('threshold_value', 12, 4);
                $table->string('unit', 20)->default('g');
                $table->string('comparison', 10)->default('>'); // >, >=, <
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_food_behavior')) {
            Schema::create('user_food_behavior', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('product_category', 100)->nullable();
                $table->unsignedInteger('scan_count')->default(1);
                $table->unsignedInteger('high_sugar_count')->default(0);
                $table->unsignedInteger('high_sodium_count')->default(0);
                $table->unsignedInteger('junk_food_count')->default(0);
                $table->unsignedInteger('ultra_processed_count')->default(0);
                $table->date('week_start')->nullable();
                $table->timestamp('last_scan')->nullable();
                $table->enum('consumption_risk', ['low', 'moderate', 'high', 'critical'])->default('low');
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->index(['user_id', 'week_start']);
            });
        }

        if (! Schema::hasTable('ai_logs')) {
            Schema::create('ai_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('prompt_type', 100);
                $table->text('input_data')->nullable();
                $table->longText('ai_response')->nullable();
                $table->unsignedInteger('response_time_ms')->nullable();
                $table->boolean('is_accurate')->nullable();
                $table->text('feedback_text')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index('prompt_type');
            });
        }

        if (! Schema::hasTable('ai_feedbacks')) {
            Schema::create('ai_feedbacks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreignId('ai_log_id')->nullable()->constrained('ai_logs')->nullOnDelete();
                $table->boolean('is_accurate');
                $table->text('feedback_text')->nullable();
                $table->timestamps();
            });
        }

        // Extend scan_histories for AI personalization columns if missing
        if (Schema::hasTable('scan_histories')) {
            Schema::table('scan_histories', function (Blueprint $table) {
                if (! Schema::hasColumn('scan_histories', 'product_category')) {
                    $table->string('product_category', 100)->nullable()->after('halal_status');
                }
                if (! Schema::hasColumn('scan_histories', 'halal_score')) {
                    $table->unsignedTinyInteger('halal_score')->nullable()->after('product_category');
                }
                if (! Schema::hasColumn('scan_histories', 'health_score')) {
                    $table->unsignedTinyInteger('health_score')->nullable()->after('halal_score');
                }
                if (! Schema::hasColumn('scan_histories', 'ai_analysis')) {
                    $table->text('ai_analysis')->nullable()->after('health_score');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_feedbacks');
        Schema::dropIfExists('ai_logs');
        Schema::dropIfExists('user_food_behavior');
        Schema::dropIfExists('nutrition_rules');
        Schema::dropIfExists('food_additives');
    }
};
