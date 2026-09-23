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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();                                      // PK
            $table->foreignId('booking_id')                    // FK to bookings
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('complainant_id')                // FK to users (complainant)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('respondent_id')                 // FK to users (respondent)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->enum('complaint_type', [
                'missed_visit', 
                'poor_service', 
                'no_proof', 
                'rude_behavior', 
                'others'
            ]);                                                // Not Null
            $table->text('description');                       // Not Null
            $table->json('evidence_paths')->nullable();        // Nullable JSON
            $table->enum('status', [
                'pending', 
                'under_review', 
                'resolved', 
                'dismissed'
            ])->default('pending');                            // Not Null
            $table->text('admin_notes')->nullable();           // Nullable
            $table->text('resolution')->nullable();            // Nullable
            $table->timestamp('resolved_at')->nullable();      // Nullable
            $table->timestamps();                              // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};