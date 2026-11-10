<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Required for data cleanup

return new class extends Migration
{
    public function up(): void
    {
        // 1. Safely drop the existing foreign key (if it exists)
        Schema::table('bookings', function (Blueprint $table) {
            try {
                $table->dropForeign(['sitter_id']);
            } catch (\Exception $e) {
                // Skip if the foreign key doesn't exist yet
            }
        });

        // 2. Ensure the column is nullable to accept NULL values
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('sitter_id')->nullable()->change();
        });

        // 3. FIX ISSUE #47: Clean up existing data before applying foreign key
        // Set any sitter_id that no longer exists in the users table to NULL
        DB::table('bookings')
            ->whereNotNull('sitter_id')
            ->whereNotIn('sitter_id', function ($query) {
                $query->select('id')->from('users');
            })
            ->update(['sitter_id' => null]);

        // 4. Apply the new and safe foreign key referencing the users table
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('sitter_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Safely drop the new foreign key
            try {
                $table->dropForeign(['sitter_id']);
            } catch (\Exception $e) {}

            // Revert to the old constraint
            // Ensure the sitter_profiles table still exists
            $table->foreign('sitter_id')
                  ->references('id')
                  ->on('sitter_profiles')
                  ->onDelete('cascade');
        });
    }
};
