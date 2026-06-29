<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable();
            $table->decimal('rate_per_visit', 10, 2)->nullable();
            $table->string('pet_types')->nullable();
            $table->string('food_preference')->nullable();
            $table->boolean('can_provide_food')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'rate_per_visit', 'pet_types', 'food_preference', 'can_provide_food']);
        });
    }
};
