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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();                                      // PK
            $table->string('booking_reference', 20)->unique(); // Not Null, Unique
            $table->foreignId('owner_id')                      // FK to users (pet owner)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('sitter_id')                     // FK to sitter_profiles
                  ->constrained('sitter_profiles')
                  ->onDelete('cascade');
            $table->foreignId('pet_id')                        // FK to pets
                  ->constrained('pets')
                  ->onDelete('cascade');
            $table->date('start_date');                        // Not Null
            $table->date('end_date');                          // Not Null
            $table->tinyInteger('visit_per_day')->unsigned();  // Not Null, max 255 but length 2
            $table->integer('total_visits');                   // Not Null
            $table->json('tasks')->nullable();                 // Nullable JSON
            $table->text('instructions')->nullable();          // Nullable
            $table->decimal('base_rate', 10, 2);               // Not Null
            $table->decimal('total_amount', 10, 2);            // Not Null
            $table->decimal('service_fee', 10, 2);             // Not Null (10%)
            $table->decimal('protection_fund', 10, 2);         // Not Null (2%)
            $table->decimal('net_platform_income', 10, 2);     // Not Null (8%)
            $table->decimal('sitter_earnings', 10, 2);         // Not Null
            $table->enum('status', [
                'pending', 'accepted', 'rejected', 
                'cancelled', 'completed', 'in_progress'
            ])->default('pending');                            // Not Null
            $table->text('cancellation_reason')->nullable();    // Nullable
            $table->enum('cancelled_by', ['owner', 'sitter', 'system', 'admin'])
                  ->nullable();                                 // Nullable
            $table->timestamps();                              // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};