<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // UI preferences
            $table->boolean('dark_mode')->default(false);
            $table->string('font_size', 20)->default('default'); // small|default|large|extra_large

            // Notifications
            $table->boolean('push_notifications')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);

            // Privacy & security
            $table->boolean('show_email')->default(false);
            $table->string('profile_visibility', 20)->default('private'); // public|private|hidden
            $table->boolean('two_factor_enabled')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};