<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->enum('action_type', ['approve_product', 'reject_product', 'add_product', 'edit_product', 'delete_product']);
            // Product ID is nullable because product might be deleted, or action is general
            // We assume products table PK is id or id_product, using unsignedBigInteger generic
            $table->unsignedBigInteger('product_id')->nullable(); 
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_activities');
    }
};
