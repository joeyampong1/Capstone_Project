<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new name columns (nullable para dili maapektuhan ang existing data)
            $table->string('f_name', 50)->nullable()->after('id');
            $table->string('l_name', 50)->nullable()->after('f_name');
            $table->string('m_name', 50)->nullable()->after('l_name');
            
            // DO NOT DROP 'name' COLUMN YET!
            // $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['f_name', 'l_name', 'm_name']);
        });
    }
};