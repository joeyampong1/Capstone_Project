<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'sitter_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
    ];

    public function sitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sitter_id');
    }

    // Scope: Filter by sitter
    public function scopeForSitter($query, $sitterId)
    {
        return $query->where('sitter_id', $sitterId);
    }

    // Scope: Only available slots
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    // Accessor: Formatted time range
    public function getTimeRangeAttribute(): string
    {
        return \Carbon\Carbon::parse($this->start_time)->format('g:i A')
             . ' — '
             . \Carbon\Carbon::parse($this->end_time)->format('g:i A');
    }
}