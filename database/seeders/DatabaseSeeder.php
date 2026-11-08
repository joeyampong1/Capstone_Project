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
        // ✅ 1. CALL OTHER SEEDERS (Fixes Issue #15)
        $this->call([
            PetTypeSeeder::class,
        ]);

        // ========================================== //
        // SECURE ADMIN USER CREATION                 //
        // ========================================== //

        // Get credentials from .env, with STRONG fallback
        $adminEmail    = env('ADMIN_EMAIL', 'j.ampong.482578@umindanao.edu.ph');
        $adminPassword = env('ADMIN_PASSWORD', 'aqcjun123098');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'f_name' => 'PetNanny',
                'l_name' => 'Admin',
                'm_name' => null,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword), // ✅ Securely hashed from .env
                'role' => 'admin',
                'is_sitter' => false,
                'sitter_level' => 1,
                'sitter_status' => null, // Set to null for admin
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

        $this->command->info('✅ Admin user created/updated securely from .env!');
        $this->command->info('');
        $this->command->info('📋 LOGIN CREDENTIALS:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info("👑 Admin:  {$adminEmail}");
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
