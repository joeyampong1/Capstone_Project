<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $table = 'visits';

    // ==========================================
    // FILLABLE
    // ==========================================
    protected $fillable = [
        'booking_id',
        'visit_number',
        'scheduled_datetime',
        'check_in',
        'check_out',
        'duration_minutes',
        'status',
        'lateness_minutes',
        'late_deduction_percentage',
        'completed_tasks',
        'notes',
        'photo_proof_path',
        'photo_proof_paths',
        'photo_proof_public_ids',
        'photo_timestamp',
    ];

    // ==========================================
    // CASTS
    // ==========================================
    protected $casts = [
        'scheduled_datetime'        => 'datetime',
        'check_in'                  => 'datetime',
        'check_out'                 => 'datetime',
        'photo_timestamp'           => 'datetime',
        'completed_tasks'           => 'array',
        'photo_proof_paths'         => 'array',
        'photo_proof_public_ids'    => 'array',
        'lateness_minutes'          => 'integer',
        'duration_minutes'          => 'integer',
        'late_deduction_percentage' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // ==========================================
    // HELPERS
    // ==========================================
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isMissed(): bool
    {
        return $this->status === 'missed';
    }

    // ==========================================
    // SCOPES
    // ==========================================
    public function scopeForSitter($query, int $sitterId)
    {
        return $query->whereHas('booking', fn($q) => $q->where('sitter_id', $sitterId));
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_datetime', today());
    }

    // ==========================================
    // ACCESSOR — All raw photo paths
    // ==========================================
    public function getAllPhotosAttribute(): array
    {
        $paths = $this->photo_proof_paths ?? [];

        if (!is_array($paths)) {
            $paths = [];
        }

        // Include legacy single photo if it exists
        if ($this->photo_proof_path && !in_array($this->photo_proof_path, $paths)) {
            array_unshift($paths, $this->photo_proof_path);
        }

        return $paths;
    }

    public function getHasPhotosAttribute(): bool
    {
        return count($this->all_photos) > 0;
    }

    // ==========================================
    // ACCESSOR — Resolved photo URLs for display
    // ==========================================
    public function getPhotoProofUrlsAttribute(): array
    {
        return array_values(array_filter(array_map(function ($path) {
            if (!$path) return null;

            // Cloudinary or external URL
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            // Local storage fallback
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }

            return null;
        }, $this->all_photos)));
    }

    // ==========================================
    // ACCESSOR — Late Deduction & Earnings (Issue #29)
    // ==========================================

    /**
     * Calculate the specific deduction amount for this visit.
     */
    public function getDeductionAmountAttribute(): float
    {
        // Check if there's a booking and if the percentage is greater than 0
        if (!$this->booking || (float) $this->late_deduction_percentage <= 0) {
            return 0.00;
        }

        // Base earnings per visit
        $perVisitEarnings = $this->booking->total_amount / max(1, $this->booking->total_visits);

        // Calculate deduction (cast to float to prevent type errors)
        return (float) $perVisitEarnings * ((float) $this->late_deduction_percentage / 100);
    }

    /**
     * Calculate the final earnings for this specific visit (Base - Deduction).
     */
    public function getEarningsAttribute(): float
    {
        if (!$this->booking) {
            return 0.00;
        }

        $perVisitEarnings = $this->booking->total_amount / max(1, $this->booking->total_visits);

        return (float) $perVisitEarnings - $this->deduction_amount;
    }
}
