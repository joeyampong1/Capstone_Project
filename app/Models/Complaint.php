<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    protected $table = 'complaints';

    // ==========================================
    // FILLABLE
    // ==========================================
    protected $fillable = [
        'booking_id',
        'complainant_id',
        'respondent_id',
        'complaint_type',
        'description',
        'evidence_paths',
        'status',
        'admin_notes',
        'resolution',
        'resolved_at',
    ];

    // ==========================================
    // CASTS
    // ==========================================
    protected $casts = [
        'evidence_paths' => 'array',
        'resolved_at'    => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function complainant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'complainant_id');
    }

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondent_id');
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isDismissed(): bool
    {
        return $this->status === 'dismissed';
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('complainant_id', $userId)
              ->orWhere('respondent_id', $userId);
        });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getTypeLabelAttribute(): string
    {
        return match($this->complaint_type) {
            'missed_visit'  => 'Missed Visit',
            'poor_service'  => 'Poor Service',
            'no_proof'      => 'No Proof',
            'rude_behavior' => 'Rude Behavior',
            'others'        => 'Others',
            default         => ucfirst($this->complaint_type),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'      => 'Pending',
            'under_review' => 'Under Review',
            'resolved'     => 'Resolved',
            'dismissed'    => 'Dismissed',
            default        => ucfirst($this->status),
        };
    }
}