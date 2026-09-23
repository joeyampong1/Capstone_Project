<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatReport extends Model
{
    protected $fillable = [
        'reporter_id', 'reported_id', 'room_id', 'reason', 'description', 'status'
    ];
}