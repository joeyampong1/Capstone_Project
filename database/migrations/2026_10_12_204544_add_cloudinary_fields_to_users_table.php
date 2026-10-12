<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cover photo (bag-o — wala pa sa DB)
            if (!Schema::hasColumn('users', 'cover_photo')) {
                $table->string('cover_photo')->nullable()->after('profile_photo');
            }

            // Cloudinary public IDs
            if (!Schema::hasColumn('users', 'profile_photo_public_id')) {
                $table->string('profile_photo_public_id')->nullable()->after('profile_photo');
            }
            if (!Schema::hasColumn('users', 'cover_photo_public_id')) {
                $table->string('cover_photo_public_id')->nullable()->after('cover_photo');
            }
            if (!Schema::hasColumn('users', 'gov_id_public_id')) {
                $table->string('gov_id_public_id')->nullable()->after('gov_id_path');
            }
            if (!Schema::hasColumn('users', 'selfie_photo_public_id')) {
                $table->string('selfie_photo_public_id')->nullable()->after('selfie_photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cover_photo',
                'profile_photo_public_id',
                'cover_photo_public_id',
                'gov_id_public_id',
                'selfie_photo_public_id',
            ]);
        });
    }
};
