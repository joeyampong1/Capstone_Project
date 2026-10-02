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
        // ADMIN USER — ONLY                          //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'admin@petnanny.com'],
            [
                'f_name' => 'PetNanny',
                'l_name' => 'Admin',
                'm_name' => null,
                'email' => 'admin@petnanny.com',
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

        $this->command->info('✅ Admin user created successfully!');
        $this->command->info('');
        $this->command->info('📋 LOGIN CREDENTIALS:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👑 Admin:  admin@petnanny.com / password');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
        $this->command->info('ℹ️  Register new accounts via /register for owner/sitter testing.');
    }
}
