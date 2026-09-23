<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // 1. Drop ang sayop nga foreign key (sitter_profiles)
            $table->dropForeign(['sitter_id']);

            // 2. Re-create as reference sa users table (correct)
            $table->foreign('sitter_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['sitter_id']);

           
            $table->foreign('sitter_id')
                  ->references('id')
                  ->on('sitter_profiles')
                  ->onDelete('cascade');
        });
    }
};