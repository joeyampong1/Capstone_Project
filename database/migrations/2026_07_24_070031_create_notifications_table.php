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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();                                      // PK
            $table->foreignId('user_id')                       // FK to users (recipient)
                  ->constrained()
                  ->onDelete('cascade');
            $table->enum('type', [
                'booking', 
                'payment', 
                'visit', 
                'review', 
                'complaint', 
                'claim', 
                'system'
            ]);                                                // Not Null
            $table->string('title', 255);                      // Not Null
            $table->text('message');                           // Not Null
            $table->json('data')->nullable();                  // Nullable JSON
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
        Schema::dropIfExists('notifications');
    }
};