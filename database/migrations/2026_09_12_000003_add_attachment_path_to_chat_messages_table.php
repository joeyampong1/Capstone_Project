<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('chat_messages', 'attachment_path')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->string('attachment_path')->nullable()->after('message_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chat_messages', 'attachment_path')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropColumn('attachment_path');
            });
        }
    }
};
