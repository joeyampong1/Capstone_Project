<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->string('weight_unit', 10)->nullable()->after('weight');
            $table->decimal('height', 5, 2)->nullable()->after('weight_unit');
            $table->string('height_unit', 10)->nullable()->after('height');
            $table->decimal('length', 5, 2)->nullable()->after('height_unit');
            $table->string('length_unit', 10)->nullable()->after('length');
            $table->decimal('width', 5, 2)->nullable()->after('length_unit');
            $table->string('width_unit', 10)->nullable()->after('width');
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn([
                'weight_unit',
                'height',
                'height_unit',
                'length',
                'length_unit',
                'width',
                'width_unit',
            ]);
        });
    }
};
