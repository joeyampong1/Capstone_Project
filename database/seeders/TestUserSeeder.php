<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================== //
        // ADMIN USER                                 //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'admin@petnanny.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // ========================================== //
        // OWNER USER                                 //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'owner@petnanny.com'],
            [
                'name' => 'Owner User',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        // ========================================== //
        // SITTER USER                                //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'sitter@petnanny.com'],
            [
                'name' => 'Sitter User',
                'password' => Hash::make('password'),
                'role' => 'sitter',
            ]
        );

        // ========================================== //
        // DUAL-ROLE USER (owner + sitter)           //
        // ========================================== //
        User::updateOrCreate(
            ['email' => 'dual@petnanny.com'],
            [
                'name' => 'Dual User',
                'password' => Hash::make('password'),
                'role' => 'owner', 
            ]
        );

        $this->command->info('✅ Test users created successfully!');
        $this->command->info('Admin: admin@petnanny.com / password');
        $this->command->info('Owner: owner@petnanny.com / password');
        $this->command->info('Sitter: sitter@petnanny.com / password');
        $this->command->info('Dual: dual@petnanny.com / password');
    }
}