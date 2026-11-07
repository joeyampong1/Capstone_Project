<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Allow null for sitter_status since fresh users haven't applied yet
        Schema::table('users', function (Blueprint $table) {
            $table->string('sitter_status')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sitter_status')->nullable(false)->change();
        });
    }
};
