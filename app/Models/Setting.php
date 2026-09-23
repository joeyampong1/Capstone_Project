<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dark_mode',
        'font_size',
        'push_notifications',
        'email_notifications',
        'sms_notifications',
        'show_email',
        'profile_visibility',
        'two_factor_enabled',
    ];

    protected $casts = [
        'dark_mode'            => 'boolean',
        'push_notifications'   => 'boolean',
        'email_notifications'  => 'boolean',
        'sms_notifications'    => 'boolean',
        'show_email'           => 'boolean',
        'two_factor_enabled'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}