<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Push Notifications Table
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('type'); // 'ingredient_alert', 'product_reminder', 'general'
            $table->string('target_type'); // 'all', 'specific_users', 'user_group'
            $table->json('target_data')->nullable();
            $table->foreignId('related_ingredient_id')->nullable();
            $table->foreignId('related_product_id')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
            $table->timestamps();
        });

        // UMKM Products Table
        Schema::create('umkm_products', function (Blueprint $table) {
            $table->id();
            $table->string('umkm_name');
            $table->string('umkm_owner');
            $table->string('umkm_phone')->nullable();
            $table->text('umkm_address')->nullable();
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->string('product_category');
            $table->enum('halal_status', ['halal_mui', 'self_declared', 'in_process']);
            $table->string('halal_cert_number')->nullable();
            $table->date('halal_cert_expiry')->nullable();
            $table->string('halal_cert_image')->nullable();
            $table->json('nutrition_info')->nullable();
            $table->json('ingredients')->nullable();
            $table->string('qr_code_unique_id')->unique();
            $table->string('qr_code_image_path')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users', 'id_user');
            $table->integer('scan_count')->default(0);
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('umkm_products');
        Schema::dropIfExists('push_notifications');
    }
};
