<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('chat_messages', 'reply_to_id')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->foreignId('reply_to_id')->nullable()->after('message_type')->constrained('chat_messages')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chat_messages', 'reply_to_id')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropConstrainedForeignId('reply_to_id');
            });
        }
    }
};
