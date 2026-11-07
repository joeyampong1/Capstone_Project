<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Force change the default value using raw SQL for MySQL
        DB::statement("ALTER TABLE settings MODIFY profile_visibility VARCHAR(20) NOT NULL DEFAULT 'public'");

        // Update all existing records to public for testing
        DB::table('settings')->update(['profile_visibility' => 'public']);
    }

    public function down(): void
    {
        // Revert back to private
        DB::statement("ALTER TABLE settings MODIFY profile_visibility VARCHAR(20) NOT NULL DEFAULT 'private'");
    }
};
