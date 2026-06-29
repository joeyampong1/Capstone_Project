<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ========================================== //
        // 1. ADMIN USER                              //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'admin@petnanny.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_sitter' => false,
                'sitter_level' => 1,
                'sitter_status' => 'pending',
                'sitter_applied_at' => null,
                'sitter_approved_at' => null,
                'location' => null,
                'latitude' => null,
                'longitude' => null,
                'profile_photo' => null,
                'gov_id_path' => null,
                'id_validation_status' => 'verified',
                'pet_count' => 0,
                'status' => 'active',
                'last_active_at' => now(),
            ]
        );

        // ========================================== //
        // 2. OWNER ONLY                              //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'owner@petnanny.com'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'is_sitter' => false,
                'sitter_level' => 1,
                'sitter_status' => 'pending',
                'sitter_applied_at' => null,
                'sitter_approved_at' => null,
                'location' => 'Quezon City',
                'latitude' => 14.6760,
                'longitude' => 121.0437,
                'profile_photo' => null,
                'gov_id_path' => null,
                'id_validation_status' => 'verified',
                'pet_count' => 2,
                'status' => 'active',
                'last_active_at' => now(),
            ]
        );

        // ========================================== //
        // 3. SITTER ONLY (approved)                  //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'sitter@petnanny.com'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'is_sitter' => true,
                'sitter_level' => 2,
                'sitter_status' => 'approved',
                'sitter_applied_at' => now()->subDays(30),
                'sitter_approved_at' => now()->subDays(25),
                'location' => 'Makati City',
                'latitude' => 14.5547,
                'longitude' => 121.0244,
                'profile_photo' => null,
                'gov_id_path' => null,
                'id_validation_status' => 'verified',
                'pet_count' => 1,
                'status' => 'active',
                'last_active_at' => now(),
            ]
        );

        // ========================================== //
        // 4. DUAL-ROLE (owner + sitter)              //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'dual@petnanny.com'],
            [
                'name' => 'Kristine Mendoza',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'is_sitter' => true,
                'sitter_level' => 3,
                'sitter_status' => 'approved',
                'sitter_applied_at' => now()->subDays(60),
                'sitter_approved_at' => now()->subDays(55),
                'location' => 'Pasig City',
                'latitude' => 14.5764,
                'longitude' => 121.0851,
                'profile_photo' => null,
                'gov_id_path' => null,
                'id_validation_status' => 'verified',
                'pet_count' => 3,
                'status' => 'active',
                'last_active_at' => now(),
            ]
        );

        // ========================================== //
        // 5. PENDING SITTER APPLICATION             //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'pending@petnanny.com'],
            [
                'name' => 'Pedro Reyes',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'is_sitter' => true,
                'sitter_level' => 1,
                'sitter_status' => 'pending',
                'sitter_applied_at' => now()->subDays(2),
                'sitter_approved_at' => null,
                'location' => 'Mandaluyong City',
                'latitude' => 14.5804,
                'longitude' => 121.0335,
                'profile_photo' => null,
                'gov_id_path' => null,
                'id_validation_status' => 'pending',
                'pet_count' => 1,
                'status' => 'active',
                'last_active_at' => now(),
            ]
        );

        // ========================================== //
        // 6. INACTIVE / SUSPENDED USER              //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'suspended@petnanny.com'],
            [
                'name' => 'Suspended User',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'is_sitter' => false,
                'sitter_level' => 1,
                'sitter_status' => 'pending',
                'sitter_applied_at' => null,
                'sitter_approved_at' => null,
                'location' => 'Manila City',
                'latitude' => 14.5995,
                'longitude' => 120.9842,
                'profile_photo' => null,
                'gov_id_path' => null,
                'id_validation_status' => 'rejected',
                'pet_count' => 0,
                'status' => 'suspended',
                'last_active_at' => now()->subDays(15),
            ]
        );

        $this->command->info('✅ Test users created successfully!');
        $this->command->info('');
        $this->command->info('📋 LOGIN CREDENTIALS:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👑 Admin:          admin@petnanny.com / password');
        $this->command->info('🐾 Owner:          owner@petnanny.com / password');
        $this->command->info('🏠 Sitter:         sitter@petnanny.com / password');
        $this->command->info('🔄 Dual-Role:      dual@petnanny.com / password');
        $this->command->info('⏳ Pending:        pending@petnanny.com / password');
        $this->command->info('🚫 Suspended:      suspended@petnanny.com / password');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}