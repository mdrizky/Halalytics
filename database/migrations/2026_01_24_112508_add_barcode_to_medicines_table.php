<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBarcodeToMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicines', function (Blueprint $table) {
            // Hanya tambahkan kolom yang belum ada
            if (!Schema::hasColumn('medicines', 'barcode')) {
                $table->string('barcode')->unique()->nullable();
            }
            if (!Schema::hasColumn('medicines', 'generic_name')) {
                $table->string('generic_name')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'brand_name')) {
                $table->string('brand_name')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'dosage_info')) {
                $table->string('dosage_info')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'frequency_per_day')) {
                $table->integer('frequency_per_day')->default(1);
            }
            if (!Schema::hasColumn('medicines', 'max_daily_dose')) {
                $table->string('max_daily_dose')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'dosage_form')) {
                $table->string('dosage_form')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'route')) {
                $table->string('route')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'contraindications')) {
                $table->text('contraindications')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'side_effects')) {
                $table->text('side_effects')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'manufacturer')) {
                $table->string('manufacturer')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'country_origin')) {
                $table->string('country_origin')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'is_prescription_required')) {
                $table->boolean('is_prescription_required')->default(false);
            }
            if (!Schema::hasColumn('medicines', 'source')) {
                $table->string('source')->default('local');
            }
            if (!Schema::hasColumn('medicines', 'active')) {
                $table->boolean('active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medicines', function (Blueprint $table) {
            //
        });
    }
}
