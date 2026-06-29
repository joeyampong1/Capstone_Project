<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //  Remove this line – `id_type` already exists
            // $table->string('id_type')->nullable()->after('gov_id_path');
            
            //  Only add `selfie_photo`
            $table->string('selfie_photo')->nullable()->after('id_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //  Only drop `selfie_photo` because `id_type` is dropped by the first migration
            $table->dropColumn('selfie_photo');
        });
    }
};