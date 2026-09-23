<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id', 'ticket_code', 'concern_type', 'priority',
        'subject', 'description', 'status', 'admin_response', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'TKT-' . strtoupper(Str::random(6));
        } while (self::where('ticket_code', $code)->exists());

        return $code;
    }
}