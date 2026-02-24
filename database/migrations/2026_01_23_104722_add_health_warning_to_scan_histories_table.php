<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('scan_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('scan_histories', 'health_warning_triggered')) {
                $table->boolean('health_warning_triggered')->default(false);
            }
            if (!Schema::hasColumn('scan_histories', 'warning_message')) {
                $table->text('warning_message')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('scan_histories', function (Blueprint $table) {
            $table->dropColumn(['health_warning_triggered', 'warning_message']);
        });
    }
};
