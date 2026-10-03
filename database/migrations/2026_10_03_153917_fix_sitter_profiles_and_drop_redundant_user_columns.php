<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // 1. ADD MISSING COLUMNS  sitter_profiles
        // ==========================================
        Schema::table('sitter_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('sitter_profiles', 'preferred_pet_types')) {
                $table->json('preferred_pet_types')->nullable()->after('food_budget');
            }
            if (!Schema::hasColumn('sitter_profiles', 'preferred_pet_sizes')) {
                $table->json('preferred_pet_sizes')->nullable()->after('preferred_pet_types');
            }
            if (!Schema::hasColumn('sitter_profiles', 'min_pets_capacity')) {
                $table->integer('min_pets_capacity')->nullable()->after('preferred_pet_sizes');
            }
            if (!Schema::hasColumn('sitter_profiles', 'max_pets_capacity')) {
                $table->integer('max_pets_capacity')->nullable()->after('min_pets_capacity');
            }
            if (!Schema::hasColumn('sitter_profiles', 'certificates_path')) {
                $table->json('certificates_path')->nullable()->after('max_pets_capacity');
            }
        });

        // Extend sitter_type ENUM
        DB::statement("ALTER TABLE sitter_profiles MODIFY COLUMN sitter_type ENUM('1','2','3','ST1','ST2','ST3','ST4') NOT NULL DEFAULT '1'");

        // Extend food_preference ENUM
        DB::statement("ALTER TABLE sitter_profiles MODIFY COLUMN food_preference ENUM('owner_provides','sitter_provides','flexible','owner_provided','sitter_provided') NOT NULL DEFAULT 'owner_provides'");

        // ==========================================
        // 2. DROP REDUNDANT COLUMNS  users TABLE
        // ==========================================
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('users', 'bio')) {
                $columnsToDrop[] = 'bio';
            }
            if (Schema::hasColumn('users', 'rate_per_visit')) {
                $columnsToDrop[] = 'rate_per_visit';
            }
            if (Schema::hasColumn('users', 'pet_types')) {
                $columnsToDrop[] = 'pet_types';
            }
            if (Schema::hasColumn('users', 'food_preference')) {
                $columnsToDrop[] = 'food_preference';
            }
            if (Schema::hasColumn('users', 'can_provide_food')) {
                $columnsToDrop[] = 'can_provide_food';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    public function down(): void
    {
        // ==========================================
        // 1. RESTORE REDUNDANT COLUMNS  users
        // ==========================================
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable();
            }
            if (!Schema::hasColumn('users', 'rate_per_visit')) {
                $table->decimal('rate_per_visit', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'pet_types')) {
                $table->string('pet_types')->nullable();
            }
            if (!Schema::hasColumn('users', 'food_preference')) {
                $table->string('food_preference')->nullable();
            }
            if (!Schema::hasColumn('users', 'can_provide_food')) {
                $table->boolean('can_provide_food')->default(false);
            }
        });

        // ==========================================
        // 2. DROP ADDED COLUMNS for sitter_profiles
        // ==========================================
        Schema::table('sitter_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_pet_types',
                'preferred_pet_sizes',
                'min_pets_capacity',
                'max_pets_capacity',
                'certificates_path',
            ]);
        });

        // Revert ENUMs
        DB::statement("ALTER TABLE sitter_profiles MODIFY COLUMN sitter_type ENUM('ST1','ST2','ST3','ST4') NOT NULL");
        DB::statement("ALTER TABLE sitter_profiles MODIFY COLUMN food_preference ENUM('owner_provided','sitter_provided') NOT NULL");
    }
};
