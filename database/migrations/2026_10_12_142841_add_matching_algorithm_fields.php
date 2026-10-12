<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->enum('vaccination_status', ['complete', 'partial', 'none'])
                  ->default('none')
                  ->after('medical_conditions');
        });

        Schema::table('sitter_profiles', function (Blueprint $table) {
            $table->json('accepted_health_conditions')->nullable()->after('preferred_pet_sizes');
            $table->json('accepted_temperaments')->nullable()->after('accepted_health_conditions');
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('vaccination_status');
        });
        Schema::table('sitter_profiles', function (Blueprint $table) {
            $table->dropColumn(['accepted_health_conditions', 'accepted_temperaments']);
        });
    }
};
