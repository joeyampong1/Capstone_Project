<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Use standard Laravel Schema builder instead of raw SQL for SQLite compatibility
        Schema::table('settings', function (Blueprint $table) {
            $table->string('profile_visibility', 20)->default('public')->change();
        });

        // Update all existing records to public for testing
        DB::table('settings')->update(['profile_visibility' => 'public']);
    }

    public function down(): void
    {
        // Revert back to private
        Schema::table('settings', function (Blueprint $table) {
            $table->string('profile_visibility', 20)->default('private')->change();
        });
    }
};
