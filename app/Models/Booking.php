<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Booking extends Model
{
    protected $table = 'bookings';

    // ==========================================
    // FILLABLE
    // ==========================================
    protected $fillable = [
        'booking_reference',
        'owner_id',
        'sitter_id',
        'pet_id',
        'start_date',
        'end_date',
        'visit_per_day',
        'total_visits',
        'schedule_times',           
        'tasks',
        'instructions',
        'base_rate',
        'food_preference',
        'food_budget',
        'subtotal',
        'total_amount',
        'sitter_earnings',
        'status',
        'cancellation_reason',
        'cancelled_by',
    ];

    // ==========================================
    // CASTS
    // ==========================================
    protected $casts = [
        'tasks'           => 'array',
        'schedule_times'  => 'array',        // ← BAG-O
        'start_date'      => 'date',
        'end_date'        => 'date',
        'base_rate'       => 'decimal:2',
        'food_budget'     => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'sitter_earnings' => 'decimal:2',
        'visit_per_day'   => 'integer',
        'total_visits'    => 'integer',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function sitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sitter_id');
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    /**
     * Auto-generated visit records (after booking is accepted)
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class)->orderBy('scheduled_datetime');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForOwner($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeForSitter($query, int $sitterId)
    {
        return $query->where('sitter_id', $sitterId);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    // ==========================================
    // STATIC HELPERS
    // ==========================================

    /**
     * Generate unique booking reference
     * Format: BK-YYYYMMDD-XXXX (e.g., BK-20260915-0001)
     */
    public static function generateReference(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "BK-{$date}-";

        // Get the last booking reference for today
        $lastBooking = static::where('booking_reference', 'like', $prefix . '%')
            ->orderByDesc('booking_reference')
            ->first();

        if ($lastBooking) {
            // Extract the number and increment
            $lastNumber = (int) substr($lastBooking->booking_reference, -4);
            $newNumber  = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Pad sa 4 digits
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate pricing based sa booking details
     *
     * @return array{
     *     days: int,
     *     total_visits: int,
     *     base_rate: float,
     *     food_budget: float,
     *     food_cost: float,
     *     subtotal: float,
     *     total_amount: float,
     *     sitter_earnings: float,
     * }
     */
    public static function calculatePricing(
        string $startDate,
        string $endDate,
        int $visitsPerDay,
        float $baseRate,
        string $foodPreference = 'owner_provides',
        float $foodBudget = 0
    ): array {
        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);

        // Days count (inclusive)
        $days = $start->diffInDays($end) + 1;

        // Total visits = days × visits per day
        $totalVisits = $days * $visitsPerDay;

        // Subtotal = base rate × total visits
        $subtotal = $baseRate * $totalVisits;

        // Food cost (only if sitter provides food)
        $foodCost = 0;
        if ($foodPreference === 'sitter_provides' && $foodBudget > 0) {
            $foodCost = $foodBudget * $totalVisits;
        }

        // Total amount owner pays
        $totalAmount = $subtotal + $foodCost;

        // Sitter earnings (same as total, unless naay platform fee in the future)
        $sitterEarnings = $totalAmount;

        return [
            'days'            => $days,
            'total_visits'    => $totalVisits,
            'base_rate'       => $baseRate,
            'food_budget'     => $foodCost > 0 ? $foodBudget : 0,
            'food_cost'       => $foodCost,
            'subtotal'        => $subtotal,
            'total_amount'    => $totalAmount,
            'sitter_earnings' => $sitterEarnings,
        ];
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Food cost — computed from food_budget × total_visits
     */
    public function getFoodCostAttribute(): float
    {
        if ($this->food_preference !== 'sitter_provides') {
            return 0;
        }
        return (float) ($this->food_budget * $this->total_visits);
    }

    /**
     * Visits label — "6 visits" / "1 visit"
     */
    public function getVisitsLabelAttribute(): string
    {
        return $this->total_visits . ' visit' . ($this->total_visits > 1 ? 's' : '');
    }

    /**
     * Days count — derived
     */
    public function getDaysCountAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Status label — human readable
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'     => 'Pending',
            'accepted'    => 'Accepted',
            'rejected'    => 'Rejected',
            'cancelled'   => 'Cancelled',
            'completed'   => 'Completed',
            'in_progress' => 'In Progress',
            default       => ucfirst($this->status),
        };
    }

    /**
     * Status color classes
     */
    public function getStatusColorAttribute(): array
    {
        return match($this->status) {
            'pending'     => ['bg' => 'bg-amber-100 dark:bg-amber-900/30',   'text' => 'text-amber-700 dark:text-amber-400'],
            'accepted'    => ['bg' => 'bg-green-100 dark:bg-green-900/30',   'text' => 'text-green-700 dark:text-green-400'],
            'rejected'    => ['bg' => 'bg-red-100 dark:bg-red-900/30',       'text' => 'text-red-700 dark:text-red-400'],
            'cancelled'   => ['bg' => 'bg-red-100 dark:bg-red-900/30',       'text' => 'text-red-700 dark:text-red-400'],
            'completed'   => ['bg' => 'bg-blue-100 dark:bg-blue-900/30',     'text' => 'text-blue-700 dark:text-blue-400'],
            'in_progress' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30',     'text' => 'text-blue-700 dark:text-blue-400'],
            default       => ['bg' => 'bg-neutral-100 dark:bg-neutral-800',  'text' => 'text-neutral-600 dark:text-neutral-300'],
        };
    }

    /**
     * Food label — human readable
     */
    public function getFoodLabelAttribute(): string
    {
        return match($this->food_preference ?? '') {
            'owner_provides'  => 'Owner provides food',
            'sitter_provides' => 'Sitter provides food',
            'flexible'        => 'Flexible',
            default           => 'Not set',
        };
    }

    /**
     * Schedule times label — "8:00 AM, 6:00 PM"
     */
    public function getScheduleLabelAttribute(): string
    {
        $times = $this->schedule_times ?? [];

        if (empty($times)) {
            return 'No schedule set';
        }

        return collect($times)
            ->map(fn($time) => Carbon::parse($time)->format('g:i A'))
            ->implode(', ');
    }

    /**
     * Check kung tanan visits completed na
     */
    public function getIsFullyCompletedAttribute(): bool
    {
        if ($this->visits()->count() === 0) {
            return false;
        }

        return $this->visits()->where('status', '!=', 'completed')->count() === 0;
    }

    public function review()
    {
        return $this->hasOne(\App\Models\Review::class);
    }

    public function hasReview(): bool
    {
        return $this->review()->exists();
    }

    
}