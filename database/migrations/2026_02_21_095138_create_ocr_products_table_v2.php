<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcrProductsTableV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ocr_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('product_name')->nullable();
            $table->string('brand')->nullable();
            $table->string('country')->nullable();
            $table->text('ingredients_raw')->nullable();
            $table->json('ingredients_parsed')->nullable();
            $table->string('halal_status')->nullable();
            $table->decimal('confidence_level', 5, 2)->nullable();
            $table->string('source')->default('ocr');
            $table->string('status')->default('pending_admin_review');
            $table->string('ocr_text_hash')->nullable();
            $table->string('front_image_path')->nullable();
            $table->string('back_image_path')->nullable();
            $table->string('language')->default('en');
            $table->json('ai_analysis')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('verified_by')->references('id_user')->on('users')->onDelete('set null');
            $table->index('ocr_text_hash');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ocr_products');
    }
}
