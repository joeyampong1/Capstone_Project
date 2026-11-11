<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Change the default value to 'public' for new users
        Schema::table('settings', function (Blueprint $table) {
            $table->string('profile_visibility', 20)->default('public')->change();
        });

        // 2. Update existing users with non-public visibility so they appear in matching
        // We include 'private', 'hidden', and 'hide' to cover all possible string variations in the DB.
        DB::table('settings')
            ->whereIn('profile_visibility', ['private', 'hidden', 'hide'])
            ->update(['profile_visibility' => 'public']);
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('profile_visibility', 20)->default('private')->change();
        });
    }
};
