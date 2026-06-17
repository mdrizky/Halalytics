<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reported_products', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->unsignedBigInteger('user_id');
            $table->string('barcode')->nullable();
            $table->string('product_name');
            $table->string('image_front_path')->nullable();
            $table->string('image_back_path')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'status']);
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reported_products');
    }
};