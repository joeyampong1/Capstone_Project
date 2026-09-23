<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserSupportMessage extends Model
{
    protected $fillable = [
        'user_id', 'message_code', 'type', 'subject', 'category',
        'message', 'steps_to_reproduce', 'device_info', 'attachment_path',
        'status', 'admin_reply', 'replied_by', 'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /* ------------------------------------------------------------------
     | Relations
     |------------------------------------------------------------------*/

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /* ------------------------------------------------------------------
     | Helpers
     |------------------------------------------------------------------*/

    public static function generateCode(): string
    {
        do {
            $code = 'MSG-' . strtoupper(Str::random(6));
        } while (self::where('message_code', $code)->exists());

        return $code;
    }

        public static function openCount(): int
    {
        // Only count NEW messages that admin hasn't opened yet.
        // Once admin views a message, it becomes 'in_progress' and no longer shows in the badge.
        return self::where('status', 'open')->count();
    }

    /* ------------------------------------------------------------------
     | Scopes
     |------------------------------------------------------------------*/

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeContact($query)
    {
        return $query->where('type', 'contact');
    }

    public function scopeReport($query)
    {
        return $query->where('type', 'report');
    }
}