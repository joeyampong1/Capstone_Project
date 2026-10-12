<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sitter_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('sitter_profiles', 'certificates_public_ids')) {
                $table->json('certificates_public_ids')->nullable()->after('certificates_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sitter_profiles', function (Blueprint $table) {
            $table->dropColumn('certificates_public_ids');
        });
    }
};
