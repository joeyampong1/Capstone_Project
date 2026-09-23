<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    // ==========================================
    // TABLE & FILLABLE
    // ==========================================

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',        // stores actor_id + action_url + meta
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data'      => 'array',
        'is_read'   => 'boolean',
        'read_at'   => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================
    // CONSTANTS — Type identifiers
    // ==========================================

    public const TYPE_BOOKING_CREATED   = 'booking_created';
    public const TYPE_BOOKING_CONFIRMED = 'booking_confirmed';
    public const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    public const TYPE_BOOKING_COMPLETED = 'booking_completed';

    public const TYPE_NEW_MESSAGE  = 'new_message';

    public const TYPE_REVIEW_RECEIVED = 'review_received';
    public const TYPE_COMMENT_RECEIVED = 'comment_received';

    public const TYPE_SITTER_APPLIED   = 'sitter_applied';
    public const TYPE_SITTER_APPROVED  = 'sitter_approved';
    public const TYPE_SITTER_REJECTED  = 'sitter_rejected';

    public const TYPE_ID_VERIFIED = 'id_verified';
    public const TYPE_ID_REJECTED = 'id_rejected';

    public const TYPE_PAYMENT_PAID     = 'payment_paid';
    public const TYPE_PAYMENT_RELEASED = 'payment_released';
    public const TYPE_PAYMENT_REFUNDED = 'payment_refunded';

    public const TYPE_COMPLAINT_FILED    = 'complaint_filed';
    public const TYPE_COMPLAINT_RESOLVED = 'complaint_resolved';

    public const TYPE_CLAIM_FILED    = 'claim_filed';
    public const TYPE_CLAIM_APPROVED = 'claim_approved';
    public const TYPE_CLAIM_REJECTED = 'claim_rejected';

    public const TYPE_SYSTEM = 'system';
    
    public const TYPE_SUPPORT_REPLIED    = 'support_replied';
    public const TYPE_SUPPORT_SUBMITTED  = 'support_submitted';

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ Actor relationship removed — gigamit na nato ang data JSON

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeForUser($query, ?int $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false)->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function isUnread(): bool
    {
        return !$this->is_read && is_null($this->read_at);
    }

    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Get actor_id from data JSON
     */
    public function getActorIdAttribute(): ?int
    {
        return $this->data['actor_id'] ?? null;
    }

    /**
     * Get actor User model from data JSON
     */
    public function getActorAttribute(): ?User
    {
        $actorId = $this->data['actor_id'] ?? null;
        if (!$actorId) return null;

        return User::find($actorId);
    }

    /**
     * Actor full name
     */
    public function getActorNameAttribute(): ?string
    {
        $actor = $this->actor;
        if ($actor) {
            return trim($actor->f_name . ' ' . $actor->l_name);
        }

        return $this->data['actor_name'] ?? null;
    }

    /**
     * Actor initial for avatar
     */
    public function getActorInitialAttribute(): ?string
    {
        $actor = $this->actor;
        if ($actor) {
            return strtoupper(substr($actor->f_name ?? 'U', 0, 1));
        }

        return $this->data['actor_initial'] ?? null;
    }

    /**
     * Resolve action URL from data JSON
     */
    public function getResolvedActionUrlAttribute(): ?string
    {
        return $this->data['action_url'] ?? null;
    }

    // ==========================================
    // ICON + COLOR MAP — by type
    // ==========================================

    public function getIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_BOOKING_CONFIRMED,
            self::TYPE_BOOKING_COMPLETED,
            self::TYPE_SITTER_APPROVED,
            self::TYPE_ID_VERIFIED,
            self::TYPE_PAYMENT_RELEASED,
            self::TYPE_COMPLAINT_RESOLVED,
            self::TYPE_CLAIM_APPROVED     => 'check-circle',

            self::TYPE_BOOKING_CREATED,
            self::TYPE_PAYMENT_PAID       => 'calendar-plus',

            self::TYPE_BOOKING_CANCELLED,
            self::TYPE_SITTER_REJECTED,
            self::TYPE_ID_REJECTED,
            self::TYPE_PAYMENT_REFUNDED,
            self::TYPE_CLAIM_REJECTED     => 'x-circle',

            self::TYPE_NEW_MESSAGE        => 'chat',
            self::TYPE_REVIEW_RECEIVED    => 'star',
            self::TYPE_COMMENT_RECEIVED   => 'chat-alt',
            self::TYPE_SITTER_APPLIED     => 'user-add',
            self::TYPE_COMPLAINT_FILED    => 'exclamation-triangle',
            self::TYPE_CLAIM_FILED        => 'document-text',

            default                       => 'bell',
        };
    }

    public function getColorAttribute(): array
    {
        return match($this->type) {
            self::TYPE_BOOKING_CONFIRMED,
            self::TYPE_BOOKING_COMPLETED,
            self::TYPE_SITTER_APPROVED,
            self::TYPE_ID_VERIFIED,
            self::TYPE_PAYMENT_RELEASED,
            self::TYPE_COMPLAINT_RESOLVED,
            self::TYPE_CLAIM_APPROVED     => [
                'bg'   => 'bg-green-100 dark:bg-green-900/30',
                'text' => 'text-green-600 dark:text-green-400',
            ],

            self::TYPE_BOOKING_CREATED,
            self::TYPE_NEW_MESSAGE,
            self::TYPE_PAYMENT_PAID       => [
                'bg'   => 'bg-blue-100 dark:bg-blue-900/30',
                'text' => 'text-blue-600 dark:text-blue-400',
            ],

            self::TYPE_BOOKING_CANCELLED,
            self::TYPE_SITTER_REJECTED,
            self::TYPE_ID_REJECTED,
            self::TYPE_PAYMENT_REFUNDED,
            self::TYPE_CLAIM_REJECTED     => [
                'bg'   => 'bg-red-100 dark:bg-red-900/30',
                'text' => 'text-red-600 dark:text-red-400',
            ],

            self::TYPE_REVIEW_RECEIVED    => [
                'bg'   => 'bg-yellow-100 dark:bg-yellow-900/30',
                'text' => 'text-yellow-600 dark:text-yellow-400',
            ],

            self::TYPE_COMMENT_RECEIVED,
            self::TYPE_CLAIM_FILED        => [
                'bg'   => 'bg-purple-100 dark:bg-purple-900/30',
                'text' => 'text-purple-600 dark:text-purple-400',
            ],

            self::TYPE_COMPLAINT_FILED    => [
                'bg'   => 'bg-amber-100 dark:bg-amber-900/30',
                'text' => 'text-amber-600 dark:text-amber-400',
            ],

            default                       => [
                'bg'   => 'bg-primary/10',
                'text' => 'text-primary',
            ],
        };
    }

    // ==========================================
    // SERIALIZE — for JSON polling
    // ==========================================

    public function toApiArray(): array
    {
        $actor = $this->actor;   // Gets from data JSON

        return [
            'id'         => $this->id,
            'type'       => $this->type,
            'title'      => $this->title,
            'message'    => $this->message,
            'action_url' => $this->resolved_action_url,
            'is_unread'  => $this->isUnread(),
            'time_ago'   => $this->created_at->diffForHumans(),
            'actor'      => $actor ? [
                'id'      => $actor->id,
                'name'    => trim($actor->f_name . ' ' . $actor->l_name),
                'initial' => strtoupper(substr($actor->f_name ?? 'U', 0, 1)),
            ] : null,
            'icon'       => $this->icon,
            'color'      => $this->color,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}