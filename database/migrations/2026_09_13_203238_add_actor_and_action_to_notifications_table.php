<?php
// database/migrations/xxxx_add_actor_and_action_to_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Actor — who triggered the notification
            $table->foreignId('actor_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Action URL — where to redirect on click
            $table->string('action_url')
                  ->nullable()
                  ->after('message');

            // Composite indexes for performance
            $table->index(['user_id', 'read_at'], 'notif_user_read_idx');
            $table->index(['user_id', 'created_at'], 'notif_user_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['actor_id']);
            $table->dropColumn('actor_id');
            $table->dropColumn('action_url');
            $table->dropIndex('notif_user_read_idx');
            $table->dropIndex('notif_user_created_idx');
        });
    }
};