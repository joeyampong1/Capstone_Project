<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sitter_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('sitter_type')->default('1');
            $table->decimal('base_rate', 10, 2)->default(0);
            $table->string('food_preference')->default('owner_provides');
            $table->decimal('food_budget', 10, 2)->nullable();
            $table->json('preferred_pet_types')->nullable();
            $table->json('preferred_pet_sizes')->nullable();
            $table->integer('min_pets_capacity')->nullable();
            $table->integer('max_pets_capacity')->nullable();
            $table->json('certificates_path')->nullable();
            $table->integer('total_bookings')->default(0);
            $table->decimal('average_ratings', 3, 2)->default(3.0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sitter_profiles');
    }
};
