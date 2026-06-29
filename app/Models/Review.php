<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    // The sitter who received this review
    public function sitter()
    {
        return $this->belongsTo(User::class, 'sitter_id');
    }

    // The user who wrote this review
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}