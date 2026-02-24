<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('notifications');
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // Target user (null = broadcast to all)
            $table->foreignId('user_id')->nullable()->constrained('users', 'id_user')->onDelete('cascade');
            
            // Notification content
            $table->string('title');
            $table->text('message');
            $table->string('type'); // 'system', 'scan', 'umkm', 'favorite', 'verification'
            
            // Related data
            $table->foreignId('related_product_id')->nullable()->constrained('products', 'id_product')->onDelete('set null');
            $table->foreignId('related_umkm_id')->nullable()->constrained('umkm_products')->onDelete('set null');
            $table->json('extra_data')->nullable(); // Additional context
            
            // Action (optional)
            $table->string('action_type')->nullable(); // 'view_product', 'open_screen'
            $table->string('action_value')->nullable(); // product_id, screen_name
            
            // Status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Firebase sync
            $table->string('firebase_key')->nullable(); // Firebase Realtime DB key
            $table->boolean('is_sent_fcm')->default(false);
            $table->timestamp('sent_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_read']);
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
