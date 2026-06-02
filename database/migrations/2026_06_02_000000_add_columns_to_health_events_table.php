<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_events', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('type')->default('online')->after('title');
            $table->date('event_date')->nullable()->after('type');
            $table->text('description')->nullable()->after('event_date');
            $table->string('image')->nullable()->after('description');
            $table->string('location')->nullable()->after('image');
            $table->integer('max_participants')->nullable()->after('location');
            $table->string('zoom_link')->nullable()->after('max_participants');
            $table->string('whatsapp_group')->nullable()->after('zoom_link');
        });

        Schema::table('event_tickets', function (Blueprint $table) {
            $table->foreignId('health_event_id')->nullable()->constrained('health_events')->nullOnDelete()->after('id');
            $table->foreignId('user_id')->nullable()->constrained('users', 'id_user')->nullOnDelete()->after('health_event_id');
            $table->string('ticket_code')->unique()->after('user_id');
            $table->string('qr_data')->nullable()->after('ticket_code');
            $table->string('status')->default('active')->after('qr_data');
        });
    }

    public function down(): void
    {
        Schema::table('event_tickets', function (Blueprint $table) {
            $table->dropForeign(['health_event_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['health_event_id', 'user_id', 'ticket_code', 'qr_data', 'status']);
        });

        Schema::table('health_events', function (Blueprint $table) {
            $table->dropColumn(['title', 'type', 'event_date', 'description', 'image', 'location', 'max_participants', 'zoom_link', 'whatsapp_group']);
        });
    }
};
