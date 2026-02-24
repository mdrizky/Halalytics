<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bpom_data', function (Blueprint $table) {
            $table->id();

            // Identitas Utama
            $table->string('nomor_reg')->unique()->nullable();
            $table->string('kategori')->default('umum'); // obat, kosmetik, pangan, suplemen, obat_tradisional, obat_kuasi, umum
            $table->string('nama_produk');
            $table->string('merk')->nullable();

            // Detail Perusahaan
            $table->string('pendaftar')->nullable();
            $table->text('alamat_produsen')->nullable();

            // Detail Teknis
            $table->string('kemasan')->nullable();
            $table->string('bentuk_sediaan')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('masa_berlaku')->nullable();

            // Analisis AI
            $table->text('ingredients_text')->nullable(); // Teks bahan mentah dari OCR
            $table->text('analisis_halal')->nullable();   // JSON hasil analisis halal dari AI
            $table->text('analisis_kandungan')->nullable(); // JSON penjelasan bahan kimia
            $table->string('status_keamanan')->default('aman'); // aman, waspada, bahaya
            $table->integer('skor_keamanan')->nullable(); // 1-100
            $table->string('status_halal')->default('belum_diverifikasi'); // halal, haram, syubhat, belum_diverifikasi

            // Sumber Data
            $table->string('sumber_data')->default('ai'); // ai, bpom, open_food_facts, open_beauty_facts, openfda, user_contribution
            $table->string('image_url')->nullable();
            $table->string('barcode')->nullable()->index();

            // User yang submit (jika dari kontribusi)
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->string('verification_status')->default('verified'); // verified, pending, rejected
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['kategori', 'status_keamanan']);
            $table->index('nama_produk');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bpom_data');
    }
};
