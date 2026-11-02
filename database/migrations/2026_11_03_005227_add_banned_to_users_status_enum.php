<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Include 'banned' in the ENUM values for the 'status' column in the 'users' table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'suspended', 'banned', 'deactivated'])
                  ->default('active')
                  ->change();
        });
    }

    public function down(): void
    {
        // back to original ENUM values
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'suspended', 'deactivated'])
                  ->default('active')
                  ->change();
        });
    }
};
