<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_analysis_results', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->unsignedBigInteger('product_id')->nullable(); // Nullable if analysis is not product-specific (e.g., general chat)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('input_type', ['barcode', 'ocr_text', 'text_query'])->default('text_query');
            $table->text('raw_input'); // Original barcode or OCR text
            $table->string('halal_verdict'); // e.g., HALAL_CERTIFIED, HALAL_UNCERTIFIED, SYUBHAT, HARAM
            $table->json('halal_data')->nullable(); // Detailed halal analysis JSON
            $table->string('health_verdict')->nullable(); // e.g., AMAN, PERHATIAN, HINDARI
            $table->json('health_data')->nullable(); // Detailed health analysis JSON
            $table->string('nutri_score')->nullable(); // e.g., A, B, C, D, E
            $table->boolean('is_verified_by_expert')->default(false);
            $table->unsignedBigInteger('expert_id')->nullable();
            $table->text('expert_notes')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('set null');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('set null');
            $table->foreign('expert_id')->references('id_user')->on('users')->onDelete('set null');
            $table->index(['product_id', 'user_id', 'created_at']);
            $table->index('input_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_analysis_results');
    }
};