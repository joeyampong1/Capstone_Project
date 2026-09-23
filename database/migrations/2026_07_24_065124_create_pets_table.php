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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();                                      // PK
            $table->foreignId('user_id')                       // FK to users (owner)
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('pet_type_id')                   // FK to pet_types
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('name', 100);                       // Not Null
            $table->string('breed', 100)->nullable();          // Nullable
            $table->enum('size', ['small', 'medium', 'large', 'giant']); // Not Null
            $table->enum('temperament', ['friendly', 'shy', 'aggressive', 'playful', 'anxious']); // Not Null
            $table->tinyInteger('age')->unsigned()->nullable(); // Nullable, max 255 but you said length 2
            $table->decimal('weight', 5, 2)->nullable();       // Nullable, 5 digits total, 2 decimal
            $table->text('special_needs')->nullable();         // Nullable
            $table->text('medical_conditions')->nullable();    // Nullable
            $table->enum('dietary_restrictions', ['none', 'dry_food', 'wet_food', 'raw', 'prescription'])->nullable(); // Nullable
            $table->string('photo_path', 255)->nullable();     // Nullable
            $table->timestamps();                              // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};