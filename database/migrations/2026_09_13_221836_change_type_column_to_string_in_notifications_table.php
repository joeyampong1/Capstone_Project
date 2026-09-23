<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Change from ENUM to VARCHAR(50)
            $table->string('type', 50)->change();
        });
    }

    public function down(): void
    {
       
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', [
                'booking', 'payment', 'visit', 'review', 'comment',
                'message', 'system', 'claim', 'complaint',
            ])->change();
        });
    }
};