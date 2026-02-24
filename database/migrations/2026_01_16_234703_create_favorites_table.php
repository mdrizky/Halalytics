<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('favorites');
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');
            
            // Polymorphic relation
            $table->string('favoritable_type'); // Product, UmkmProduct, StreetFood
            $table->unsignedBigInteger('favoritable_id');
            
            // Denormalized data for quick access
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->string('halal_status');
            $table->string('category')->nullable();
            
            // Monitoring for changes
            $table->string('last_known_status'); // Track jika status berubah
            $table->boolean('has_status_changed')->default(false);
            $table->timestamp('status_changed_at')->nullable();
            
            // Notes
            $table->text('user_notes')->nullable();
            
            // Firebase sync
            $table->string('firebase_key')->nullable();
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['user_id', 'favoritable_type', 'favoritable_id']);
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['favoritable_type', 'favoritable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorites');
    }
};
