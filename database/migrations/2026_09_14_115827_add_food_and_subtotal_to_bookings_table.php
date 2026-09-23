<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add new columns kung wala pa
            if (!Schema::hasColumn('bookings', 'food_preference')) {
                $table->string('food_preference')->nullable()->after('base_rate');
            }
            if (!Schema::hasColumn('bookings', 'food_budget')) {
                $table->decimal('food_budget', 10, 2)->default(0)->after('food_preference');
            }
            if (!Schema::hasColumn('bookings', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0)->after('food_budget');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['food_preference', 'food_budget', 'subtotal']);
        });
    }
};