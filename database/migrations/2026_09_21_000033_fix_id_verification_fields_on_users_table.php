<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ================================================================
        // 1. Fix the enum — add 'unverified' and make it the default
        // ================================================================
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("
                ALTER TABLE users
                MODIFY COLUMN id_validation_status
                ENUM('unverified', 'pending', 'verified', 'rejected')
                NOT NULL DEFAULT 'unverified'
            ");
        }

        // Reset existing rows that were never submitted but are 'pending'
        DB::table('users')
            ->whereNull('gov_id_path')
            ->where('id_validation_status', 'pending')
            ->update(['id_validation_status' => 'unverified']);

        // ================================================================
        // 2. Add admin review columns
        // ================================================================
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('id_validation_status');
            }
            if (! Schema::hasColumn('users', 'id_reviewed_by')) {
                $table->unsignedBigInteger('id_reviewed_by')->nullable()->after('admin_notes');
            }
            if (! Schema::hasColumn('users', 'id_reviewed_at')) {
                $table->timestamp('id_reviewed_at')->nullable()->after('id_reviewed_by');
            }
        });

        // ================================================================
        // 3. Add API result columns (optional but recommended)
        // ================================================================
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'face_match_score')) {
                $table->decimal('face_match_score', 5, 2)->nullable()->after('id_reviewed_at');
            }
            if (! Schema::hasColumn('users', 'document_authenticity')) {
                $table->string('document_authenticity')->nullable()->after('face_match_score');
            }
            if (! Schema::hasColumn('users', 'liveness_detection')) {
                $table->string('liveness_detection')->nullable()->after('document_authenticity');
            }
            if (! Schema::hasColumn('users', 'id_expired')) {
                $table->boolean('id_expired')->nullable()->after('liveness_detection');
            }
            if (! Schema::hasColumn('users', 'name_match')) {
                $table->string('name_match')->nullable()->after('id_expired');
            }
            if (! Schema::hasColumn('users', 'birthdate_match')) {
                $table->string('birthdate_match')->nullable()->after('name_match');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'admin_notes', 'id_reviewed_by', 'id_reviewed_at',
                'face_match_score', 'document_authenticity', 'liveness_detection',
                'id_expired', 'name_match', 'birthdate_match',
            ]);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("
                ALTER TABLE users
                MODIFY COLUMN id_validation_status
                ENUM('pending', 'verified', 'rejected')
                NOT NULL DEFAULT 'pending'
            ");
        }
    }
};