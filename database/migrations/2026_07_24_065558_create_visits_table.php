<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();                                      // PK
            $table->foreignId('booking_id')                    // FK to bookings
                  ->constrained()
                  ->onDelete('cascade');
            $table->integer('visit_number');                   // Not Null (max 5 digits, but integer is fine)
            $table->dateTime('scheduled_datetime');            // Not Null
            $table->dateTime('check_in')->nullable();          // Nullable
            $table->dateTime('check_out')->nullable();         // Nullable
            $table->integer('duration_minutes')->nullable();   // Nullable
            $table->enum('status', ['pending', 'in_progress', 'completed', 'missed'])
                  ->default('pending');                        // Not Null
            $table->integer('lateness_minutes')->default(0);   // Not Null, default 0
            $table->decimal('late_deduction_percentage', 5, 2)->default(0.00); // Not Null
            $table->json('completed_tasks')->nullable();       // Nullable JSON
            $table->text('notes')->nullable();                 // Nullable
            $table->json('photo_proof_path')->nullable();      // Nullable JSON (array of paths)
            $table->timestamp('photo_timestamp')->nullable();  // Nullable
            $table->timestamps();                              // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};