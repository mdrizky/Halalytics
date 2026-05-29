<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('donation_campaigns')) {
            Schema::create('donation_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('title', 300);
                $table->string('slug', 300)->unique();
                $table->text('description')->nullable();
                $table->string('image', 500)->nullable();
                $table->decimal('target_amount', 15, 2);
                $table->decimal('collected_amount', 15, 2)->default(0);
                $table->unsignedInteger('donor_count')->default(0);
                $table->enum('category', [
                    'bencana', 'kesehatan', 'pendidikan', 'darurat', 'pangan', 'donor_darah', 'pangan_halal', 'stunting', 'platform',
                    'kemanusiaan', 'masjid', 'zakat', 'yatim', 'lainnya'
                ])->default('kesehatan');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_urgent')->default(false);
                $table->timestamp('deadline')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('donations')) {
            Schema::create('donations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreignId('campaign_id')->constrained('donation_campaigns')->cascadeOnDelete();
                $table->decimal('amount', 15, 2);
                $table->string('payment_method', 50)->nullable();
                $table->string('transaction_id', 200)->unique();
                $table->string('midtrans_token', 500)->nullable();
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired', 'refunded'])->default('pending');
                $table->boolean('is_anonymous')->default(false);
                $table->string('donor_name', 200)->nullable();
                $table->text('donor_message')->nullable();
                $table->string('payment_url', 500)->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('expired_at')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('payment_status');
            });
        }

        if (! Schema::hasTable('donation_updates')) {
            Schema::create('donation_updates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campaign_id')->constrained('donation_campaigns')->cascadeOnDelete();
                $table->string('title', 300)->nullable();
                $table->text('content')->nullable();
                $table->string('image', 500)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_updates');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('donation_campaigns');
    }
};
