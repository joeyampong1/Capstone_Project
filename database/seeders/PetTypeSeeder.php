<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PetType; // Make sure to import the model

class PetTypeSeeder extends Seeder
{
    public function run(): void
    {
        $petTypes = [
            ['name' => 'Dog', 'icon' => '🐶'],
            ['name' => 'Cat', 'icon' => '🐱'],
            ['name' => 'Bird', 'icon' => '🐦'],
            ['name' => 'Rabbit', 'icon' => '🐰'],
            ['name' => 'Hamster', 'icon' => '🐹'],
            ['name' => 'Fish', 'icon' => '🐟'],
            ['name' => 'Reptile', 'icon' => '🦎'],
            ['name' => 'Other', 'icon' => '🐾'],
        ];

        foreach ($petTypes as $type) {
            // Checks if the name exists. If not, it creates it.
            PetType::firstOrCreate(
                ['name' => $type['name']],
                ['icon' => $type['icon']]
            );
        }
    }
}
