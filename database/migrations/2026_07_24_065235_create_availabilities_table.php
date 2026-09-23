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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();                                  // PK
            $table->foreignId('sitter_id')                 // FK to sitter_profiles
                  ->constrained('sitter_profiles')
                  ->onDelete('cascade');
            $table->date('date');                          // Not Null
            $table->time('start_time');                   // Not Null
            $table->time('end_time');                     // Not Null
            $table->boolean('is_available')->default(true); // Not Null, default true (available)
            $table->timestamps();                          // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};