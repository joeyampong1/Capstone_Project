<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       // Change the column type of 'sitter_type' in 'sitter_profiles' table from integer to string
        Schema::table('sitter_profiles', function (Blueprint $table) {
            $table->string('sitter_type')->default('small_pets')->change();
            $table->string('food_preference')->default('owner_provides')->change();
        });
    }

    public function down(): void
    {
        Schema::table('sitter_profiles', function (Blueprint $table) {
            $table->string('sitter_type')->default('1')->change();
            $table->string('food_preference')->default('owner_provides')->change();
        });
    }
};
