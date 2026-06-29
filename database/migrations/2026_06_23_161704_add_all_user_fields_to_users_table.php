<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ========================================== //
            // ROLE & BASIC INFO                          //
            // ========================================== //
            
            // Main role: admin or owner (sitter is a toggle)
            $table->enum('role', ['admin', 'owner'])->default('owner')->after('password');
            
            // ========================================== //
            // SITTER-SPECIFIC FIELDS                     //
            // ========================================== //
            
            // Is this user a sitter? (toggle ON/OFF)
            $table->boolean('is_sitter')->default(false)->after('role');
            
            // Sitter level: 1 (Basic), 2 (Intermediate), 3 (Advanced)
            $table->tinyInteger('sitter_level')->default(1)->after('is_sitter');
            
            // Sitter application status
            $table->enum('sitter_status', ['pending', 'approved', 'rejected'])->default('pending')->after('sitter_level');
            
            // When user applied to become a sitter
            $table->timestamp('sitter_applied_at')->nullable()->after('sitter_status');
            
            // When sitter application was approved
            $table->timestamp('sitter_approved_at')->nullable()->after('sitter_applied_at');
            
            // ========================================== //
            // OWNER-SPECIFIC FIELDS                     //
            // ========================================== //
            
            // User's location (for matching algorithm)
            $table->string('location')->nullable()->after('sitter_approved_at');
            
            // Latitude for precise distance calculation
            $table->decimal('latitude', 10, 8)->nullable()->after('location');
            
            // Longitude for precise distance calculation
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            
            // ========================================== //
            // PROFILE & VERIFICATION                    //
            // ========================================== //
            
            // User's full name (already have name field)
            // Profile photo/avatar
            $table->string('profile_photo')->nullable()->after('longitude');
            
            // Government ID for validation
            $table->string('gov_id_path')->nullable()->after('profile_photo');
            
            // ID validation status
            $table->enum('id_validation_status', ['pending', 'verified', 'rejected'])->default('pending')->after('gov_id_path');
            
            // ========================================== //
            // PET OWNER FIELDS                          //
            // ========================================== //
            
            // How many pets does the user have (for display)
            $table->tinyInteger('pet_count')->default(0)->after('id_validation_status');
            
            // ========================================== //
            // SYSTEM & STATUS                          //
            // ========================================== //
            
            // Account status
            $table->enum('status', ['active', 'suspended', 'deactivated'])->default('active')->after('pet_count');
            
            // Last active timestamp
            $table->timestamp('last_active_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'is_sitter',
                'sitter_level',
                'sitter_status',
                'sitter_applied_at',
                'sitter_approved_at',
                'location',
                'latitude',
                'longitude',
                'profile_photo',
                'gov_id_path',
                'id_validation_status',
                'pet_count',
                'status',
                'last_active_at',
            ]);
        });
    }
};