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
        Schema::create('sitter_profiles', function (Blueprint $table) {
            $table->id();                              // PK, auto-increment
            $table->foreignId('user_id')               // FK to users table
                  ->constrained()
                  ->onDelete('cascade');              // Adjust onDelete as needed
            $table->text('bio')->nullable();           // Nullable text
            $table->integer('experience_years');       // Not Null (default not set, but we can add ->default(0) if needed)
            $table->enum('sitter_type', ['ST1', 'ST2', 'ST3', 'ST4']); // Not Null
            $table->decimal('base_rate', 10, 2);       // Not Null
            $table->enum('food_preference', ['owner_provided', 'sitter_provided']); // Not Null
            $table->decimal('food_budget', 10, 2)->nullable(); // Nullable
            $table->integer('total_bookings')->default(0); // Not Null with default 0
            $table->decimal('average_ratings', 3, 2)->default(3.0); // Not Null with default 3.0
            $table->boolean('is_active')->default(true); // Not Null, default true (1)
            $table->timestamps();                      // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sitter_profiles');
    }
};