<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('ocr_result')->nullable()->after('id_validation_status');
            $table->boolean('face_detected_on_id')->nullable()->after('ocr_result');
            $table->boolean('face_detected_on_selfie')->nullable()->after('face_detected_on_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ocr_result',
                'face_detected_on_id',
                'face_detected_on_selfie',
            ]);
        });
    }
};