<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Chat Messages
        if (Schema::hasColumn('chat_messages', 'room_id') && !Schema::hasIndex('chat_messages', 'chat_messages_room_id_index')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->index('room_id');
            });
        }

        // 2. Bookings
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'status') && !Schema::hasIndex('bookings', 'bookings_status_index')) $table->index('status');
            if (Schema::hasColumn('bookings', 'start_date') && !Schema::hasIndex('bookings', 'bookings_start_date_index')) $table->index('start_date');
            if (Schema::hasColumn('bookings', 'end_date') && !Schema::hasIndex('bookings', 'bookings_end_date_index')) $table->index('end_date');
        });

        // 3. Visits
        Schema::table('visits', function (Blueprint $table) {
            if (Schema::hasColumn('visits', 'status') && !Schema::hasIndex('visits', 'visits_status_index')) $table->index('status');
            if (Schema::hasColumn('visits', 'scheduled_date') && !Schema::hasIndex('visits', 'visits_scheduled_date_index')) $table->index('scheduled_date');
            if (Schema::hasColumn('visits', 'visit_date') && !Schema::hasIndex('visits', 'visits_visit_date_index')) $table->index('visit_date');
            if (Schema::hasColumn('visits', 'date') && !Schema::hasIndex('visits', 'visits_date_index')) $table->index('date');
            if (Schema::hasColumn('visits', 'scheduled_at') && !Schema::hasIndex('visits', 'visits_scheduled_at_index')) $table->index('scheduled_at');
        });

        // 4. Users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status') && !Schema::hasIndex('users', 'users_status_index')) $table->index('status');
            if (Schema::hasColumn('users', 'role') && !Schema::hasIndex('users', 'users_role_index')) $table->index('role');
            if (Schema::hasColumn('users', 'google_id') && !Schema::hasIndex('users', 'users_google_id_index')) $table->index('google_id');
        });

        // 5. Sitter Profiles
        if (Schema::hasColumn('sitter_profiles', 'user_id') && !Schema::hasIndex('sitter_profiles', 'sitter_profiles_user_id_unique')) {
            Schema::table('sitter_profiles', function (Blueprint $table) {
                $table->unique('user_id');
            });
        }
    }

    public function down(): void
    {
        // Safe drop logic
        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasIndex('chat_messages', 'chat_messages_room_id_index')) $table->dropIndex(['room_id']);
        });
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasIndex('bookings', 'bookings_status_index')) $table->dropIndex(['status']);
            if (Schema::hasIndex('bookings', 'bookings_start_date_index')) $table->dropIndex(['start_date']);
            if (Schema::hasIndex('bookings', 'bookings_end_date_index')) $table->dropIndex(['end_date']);
        });
        Schema::table('visits', function (Blueprint $table) {
            if (Schema::hasIndex('visits', 'visits_status_index')) $table->dropIndex(['status']);
            if (Schema::hasIndex('visits', 'visits_scheduled_date_index')) $table->dropIndex(['scheduled_date']);
            if (Schema::hasIndex('visits', 'visits_visit_date_index')) $table->dropIndex(['visit_date']);
            if (Schema::hasIndex('visits', 'visits_date_index')) $table->dropIndex(['date']);
            if (Schema::hasIndex('visits', 'visits_scheduled_at_index')) $table->dropIndex(['scheduled_at']);
        });
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'users_status_index')) $table->dropIndex(['status']);
            if (Schema::hasIndex('users', 'users_role_index')) $table->dropIndex(['role']);
            if (Schema::hasIndex('users', 'users_google_id_index')) $table->dropIndex(['google_id']);
        });
        Schema::table('sitter_profiles', function (Blueprint $table) {
            if (Schema::hasIndex('sitter_profiles', 'sitter_profiles_user_id_unique')) $table->dropUnique(['user_id']);
        });
    }
};
