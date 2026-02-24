<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppTables extends Migration
{
    public function up()
    {
        // 1. Kategori
        Schema::create('kategori', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori');
            $table->timestamps();
        });

        // 2. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_product');
            $table->string('nama_product');
            $table->string('barcode')->nullable()->index();
            $table->text('komposisi')->nullable();
            $table->string('status')->nullable(); // halal, haram, etc
            $table->text('info_gizi')->nullable();
            
            // Foreign key to kategori
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->foreign('kategori_id')->references('id_kategori')->on('kategori')->onDelete('set null');
            
            $table->timestamps();
        });

        // 3. Scans
        Schema::create('scans', function (Blueprint $table) {
            $table->id('id_scan');
            
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
            
            $table->string('nama_produk');
            $table->string('barcode')->nullable();
            $table->string('kategori')->nullable();
            $table->string('status_halal');
            $table->string('status_kesehatan');
            $table->date('tanggal_expired')->nullable();
            $table->timestamp('tanggal_scan')->useCurrent();
            
            $table->timestamps();
        });

        // 4. Reports
        Schema::create('reports', function (Blueprint $table) {
            $table->id('id_report');
            
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id_product')->on('products')->onDelete('cascade');
            
            $table->text('laporan');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('scans');
        Schema::dropIfExists('products');
        Schema::dropIfExists('kategori');
    }
}
