<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('message_code')->unique();
            $table->enum('type', ['contact', 'report'])->default('contact');
            $table->string('subject');
            $table->string('category');
            $table->text('message');
            $table->text('steps_to_reproduce')->nullable();
            $table->string('device_info')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');
            $table->text('admin_reply')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_support_messages');
    }
};