<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop unused fee columns — feature not implemented per panel advice.
     * These columns are NOT NULL in migration but never filled in code,
     * causing booking creation to fail on fresh installs.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'service_fee',
                'protection_fund',
                'net_platform_income',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('service_fee', 10, 2)->nullable();
            $table->decimal('protection_fund', 10, 2)->nullable();
            $table->decimal('net_platform_income', 10, 2)->nullable();
        });
    }
};
