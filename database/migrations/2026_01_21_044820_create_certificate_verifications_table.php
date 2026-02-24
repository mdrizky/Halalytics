<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('certificate_number');
            $table->string('product_name')->nullable();
            $table->string('manufacturer')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('status')->default('valid'); // valid, expired, invalid
            $table->string('issuer')->default('BPJPH');
            $table->json('raw_data')->nullable(); // Store original QR data
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_verifications');
    }
}
