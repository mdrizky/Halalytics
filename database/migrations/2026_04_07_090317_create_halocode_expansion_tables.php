<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // FASE 1: HALOCODE — Konsultasi Pakar
        // ============================================================

        // 1. Experts
        if (!Schema::hasTable('experts')) {
            Schema::create('experts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('specialization');
                $table->text('bio')->nullable();
                $table->string('certificate_path')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_online')->default(false);
                $table->integer('price_per_session')->default(0);
                $table->decimal('rating', 3, 2)->default(0);
                $table->integer('total_reviews')->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->index(['is_verified', 'is_online']);
            });
        }

        // 2. Consultations
        if (!Schema::hasTable('consultations')) {
            Schema::create('consultations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('expert_id');
                $table->enum('status', ['pending', 'paid', 'active', 'ended', 'cancelled'])->default('pending');
                $table->string('payment_token')->nullable();
                $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
                $table->integer('amount')->default(0);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->foreign('expert_id')->references('id')->on('experts')->cascadeOnDelete();
                $table->index(['user_id', 'status']);
                $table->index(['expert_id', 'status']);
            });
        }

        // 3. Messages (Halocode chat)
        if (!Schema::hasTable('halocode_messages')) {
            Schema::create('halocode_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('consultation_id');
                $table->unsignedBigInteger('sender_id');
                $table->text('message');
                $table->string('attachment_path')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();

                $table->foreign('consultation_id')->references('id')->on('consultations')->cascadeOnDelete();
                $table->foreign('sender_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->index(['consultation_id', 'created_at']);
            });
        }

        // 4. Expert Wallets
        if (!Schema::hasTable('expert_wallets')) {
            Schema::create('expert_wallets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('expert_id')->unique();
                $table->bigInteger('balance')->default(0);
                $table->bigInteger('total_earned')->default(0);
                $table->timestamps();

                $table->foreign('expert_id')->references('id')->on('experts')->cascadeOnDelete();
            });
        }

        // 5. Wallet Transactions
        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('expert_wallet_id');
                $table->enum('type', ['credit', 'debit']);
                $table->integer('amount');
                $table->string('description');
                $table->string('reference_id')->nullable();
                $table->timestamps();

                $table->foreign('expert_wallet_id')->references('id')->on('expert_wallets')->cascadeOnDelete();
            });
        }

        // 6. Expert Reviews
        if (!Schema::hasTable('expert_reviews')) {
            Schema::create('expert_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('consultation_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('expert_id');
                $table->tinyInteger('rating'); // 1-5
                $table->text('review')->nullable();
                $table->timestamps();

                $table->foreign('consultation_id')->references('id')->on('consultations')->cascadeOnDelete();
                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->foreign('expert_id')->references('id')->on('experts')->cascadeOnDelete();
                $table->unique(['consultation_id', 'user_id']);
            });
        }

        // 7. Expert Schedules
        if (!Schema::hasTable('expert_schedules')) {
            Schema::create('expert_schedules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('expert_id');
                $table->tinyInteger('day_of_week'); // 0=Sunday..6=Saturday
                $table->time('start_time');
                $table->time('end_time');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('expert_id')->references('id')->on('experts')->cascadeOnDelete();
                $table->index(['expert_id', 'day_of_week']);
            });
        }

        // ============================================================
        // FASE 2: MARKETPLACE + FASKES
        // ============================================================

        // 8. Merchants
        if (!Schema::hasTable('merchants')) {
            Schema::create('merchants', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('type', ['toko_halal', 'klinik', 'apotek', 'rs', 'puskesmas', 'restoran_halal']);
                $table->text('address');
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('phone')->nullable();
                $table->string('website')->nullable();
                $table->string('affiliate_link')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->string('google_place_id')->nullable();
                $table->json('opening_hours')->nullable();
                $table->string('image_path')->nullable();
                $table->timestamps();

                $table->index(['latitude', 'longitude']);
                $table->index('type');
            });
        }

        // 9. Marketplace Products
        if (!Schema::hasTable('marketplace_products')) {
            Schema::create('marketplace_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('merchant_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('price')->default(0);
                $table->string('image_path')->nullable();
                $table->string('category')->nullable();
                $table->boolean('is_halal_certified')->default(false);
                $table->string('halal_cert_number')->nullable();
                $table->integer('stock')->default(0);
                $table->timestamps();

                $table->foreign('merchant_id')->references('id')->on('merchants')->cascadeOnDelete();
                $table->index('category');
            });
        }

        // 10. Product Categories (Marketplace)
        if (!Schema::hasTable('marketplace_categories')) {
            Schema::create('marketplace_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icon')->nullable();
                $table->timestamps();
            });
        }

        // ============================================================
        // FASE 3: COMMUNITY HUB + GAMIFIKASI
        // ============================================================

        // 11. Posts
        if (!Schema::hasTable('community_posts')) {
            Schema::create('community_posts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('title')->nullable();
                $table->text('content');
                $table->string('image_path')->nullable();
                $table->enum('category', ['resep', 'diskusi', 'tips', 'progress', 'tanya'])->default('diskusi');
                $table->json('hashtags')->nullable();
                $table->integer('likes_count')->default(0);
                $table->integer('comments_count')->default(0);
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->index(['category', 'created_at']);
                $table->index('is_pinned');
            });
        }

        // 12. Comments
        if (!Schema::hasTable('community_comments')) {
            Schema::create('community_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->text('content');
                $table->integer('likes_count')->default(0);
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();

                $table->foreign('post_id')->references('id')->on('community_posts')->cascadeOnDelete();
                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->foreign('parent_id')->references('id')->on('community_comments')->nullOnDelete();
            });
        }

        // 13. Post Likes
        if (!Schema::hasTable('community_post_likes')) {
            Schema::create('community_post_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->foreign('post_id')->references('id')->on('community_posts')->cascadeOnDelete();
                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->unique(['post_id', 'user_id']);
            });
        }

        // 14. Comment Likes
        if (!Schema::hasTable('community_comment_likes')) {
            Schema::create('community_comment_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('comment_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->foreign('comment_id')->references('id')->on('community_comments')->cascadeOnDelete();
                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->unique(['comment_id', 'user_id']);
            });
        }

        // 15. Post Reports
        if (!Schema::hasTable('community_post_reports')) {
            Schema::create('community_post_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->unsignedBigInteger('user_id');
                $table->enum('reason', ['spam', 'sara', 'hoax', 'pornografi', 'lainnya']);
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'reviewed', 'resolved'])->default('pending');
                $table->timestamps();

                $table->foreign('post_id')->references('id')->on('community_posts')->cascadeOnDelete();
                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->unique(['post_id', 'user_id']);
            });
        }

        // 16. Badges
        if (!Schema::hasTable('badges')) {
            Schema::create('badges', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description');
                $table->string('icon_path')->nullable();
                $table->string('condition_type'); // total_points, post_count, etc
                $table->integer('condition_value');
                $table->timestamps();
            });
        }

        // 17. User Badges
        if (!Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('badge_id');
                $table->timestamp('earned_at');
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->foreign('badge_id')->references('id')->on('badges')->cascadeOnDelete();
                $table->unique(['user_id', 'badge_id']);
            });
        }

        // 18. User Points (Community-specific aggregate)
        if (!Schema::hasTable('community_user_points')) {
            Schema::create('community_user_points', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->integer('total_points')->default(0);
                $table->string('level')->default('Pemula');
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
            });
        }

        // 19. Point Transactions
        if (!Schema::hasTable('community_point_transactions')) {
            Schema::create('community_point_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->integer('points');
                $table->string('reason');
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('community_point_transactions');
        Schema::dropIfExists('community_user_points');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('community_post_reports');
        Schema::dropIfExists('community_comment_likes');
        Schema::dropIfExists('community_post_likes');
        Schema::dropIfExists('community_comments');
        Schema::dropIfExists('community_posts');
        Schema::dropIfExists('marketplace_categories');
        Schema::dropIfExists('marketplace_products');
        Schema::dropIfExists('merchants');
        Schema::dropIfExists('expert_schedules');
        Schema::dropIfExists('expert_reviews');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('expert_wallets');
        Schema::dropIfExists('halocode_messages');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('experts');
        Schema::enableForeignKeyConstraints();
    }
};
