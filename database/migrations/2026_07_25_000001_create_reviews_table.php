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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();                                      // PK
            $table->foreignId('sitter_id')                     // FK to sitter_profiles
                  ->constrained('sitter_profiles')
                  ->onDelete('cascade');
            $table->foreignId('user_id')                       // FK to users (reviewer)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('booking_id')                    // FK to bookings
                  ->constrained('bookings')
                  ->onDelete('cascade');
            $table->decimal('rating', 2, 1);                   // Not Null (e.g., 4.5)
            $table->text('comments');                          // Not Null
            $table->timestamps();                              // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};