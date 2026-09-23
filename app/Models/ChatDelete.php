<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatDelete extends Model
{
    protected $fillable = ['user_id', 'room_id'];
}