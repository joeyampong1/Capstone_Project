<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PetTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pet_types')->insert([
            ['name' => 'Dog', 'icon' => '🐶', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cat', 'icon' => '🐱', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bird', 'icon' => '🐦', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rabbit', 'icon' => '🐰', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hamster', 'icon' => '🐹', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fish', 'icon' => '🐟', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Reptile', 'icon' => '🦎', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other', 'icon' => '🐾', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}