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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();                                      // PK
            $table->foreignId('sitter_id')                     // FK to sitter_profiles
                  ->constrained('sitter_profiles')
                  ->onDelete('cascade');
            $table->foreignId('user_id')                       // FK to users (comment author)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->text('body');                              // Not Null
            $table->foreignId('parent_id')                     // FK to self (parent comment)
                  ->nullable()
                  ->constrained('comments')
                  ->onDelete('cascade');
            $table->timestamps();                              // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};