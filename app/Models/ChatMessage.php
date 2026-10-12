<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'room_id',
        'sender_id',
        'receiver_id',
        'message',
        'message_type',
        'reply_to_id',
        'attachment_path',
        'attachment_public_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // NEW: Reply-to relationship
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }

    // NEW: Reactions
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class, 'message_id');
    }

    public static function makeRoomId($userA, $userB): string
    {
        $ids = [(int) $userA, (int) $userB];
        sort($ids);
        return 'room_' . $ids[0] . '_' . $ids[1];
    }

    /**
     * Attachment URL — auto-detects Cloudinary vs local.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) return null;

        // Cloudinary or external URL
        if (str_starts_with($this->attachment_path, 'http://') || str_starts_with($this->attachment_path, 'https://')) {
            return $this->attachment_path;
        }

        // Local storage fallback
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->attachment_path)) {
            return asset('storage/' . $this->attachment_path);
        }

        return null;
    }

    public function isImage(): bool
    {
        return $this->message_type === 'image';
    }

    public function getReactionsSummaryAttribute(): array
    {
        return $this->reactions()
            ->select('emoji', \DB::raw('count(*) as count'))
            ->groupBy('emoji')
            ->get()
            ->toArray();
    }


}
