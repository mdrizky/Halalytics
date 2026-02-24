<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('scan_histories');
        Schema::create('scan_histories', function (Blueprint $table) {
            $table->id();
            
            // User info
            $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');
            
            // Product info (polymorphic - bisa product biasa atau UMKM)
            $table->string('scannable_type'); // Product, UmkmProduct, StreetFood
            $table->unsignedBigInteger('scannable_id');
            
            // Quick access data (denormalized for performance)
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->string('barcode')->nullable();
            $table->string('halal_status');
            
            // Scan metadata
            $table->enum('scan_method', ['barcode', 'qr_code', 'text_search', 'photo'])->default('barcode');
            $table->enum('source', ['local', 'open_food_facts', 'umkm', 'street_food']);
            
            // Location (optional)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            // Analytics
            $table->integer('confidence_score')->nullable(); // AI confidence
            $table->json('nutrition_snapshot')->nullable(); // Snapshot nutrisi saat scan
            
            // Firebase sync
            $table->string('firebase_key')->nullable();
            $table->boolean('is_synced')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['scannable_type', 'scannable_id']);
            $table->index('source');
        });
    }

    public function down()
    {
        Schema::dropIfExists('scan_histories');
    }
};
