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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();                                      // PK
            $table->string('room_id', 100);                    // Not Null (e.g., booking_{booking_id})
            $table->foreignId('sender_id')                     // FK to users (sender)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('receiver_id')                   // FK to users (receiver)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->text('message');                           // Not Null
            $table->enum('message_type', ['text', 'image', 'system'])
                  ->default('text');                           // Not Null
            $table->boolean('is_read')->default(false);        // Not Null, default false
            $table->timestamp('read_at')->nullable();          // Nullable
            $table->timestamps();                              // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};