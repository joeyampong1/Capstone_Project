<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Save the FCM token from the browser here
            $table->string('fcm_token')->nullable()->after('remember_token');

            $table->boolean('push_notifications_enabled')->default(true)->after('fcm_token');
            $table->boolean('email_notifications_enabled')->default(true)->after('push_notifications_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fcm_token',
                'push_notifications_enabled',
                'email_notifications_enabled'
            ]);
        });
    }
};
