<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'sitter_id',
        'user_id',
        'booking_id',
        'rating',
        'comments',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Ang sitter profile (DILI user) — kay ang reviews.sitter_id
     * nag-reference sa sitter_profiles table.
     */
    public function sitterProfile(): BelongsTo
    {
        return $this->belongsTo(SitterProfile::class, 'sitter_id');
    }

    /**
     * Shortcut — access sa user via sitter profile.
     */
    public function getSitterUserAttribute()
    {
        return $this->sitterProfile?->user;
    }

    /**
     * Ang reviewer (owner).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}