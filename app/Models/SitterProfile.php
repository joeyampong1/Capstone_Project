<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SitterProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'experience_years',
        'sitter_type',
        'base_rate',
        'food_preference',
        'food_budget',
        'total_bookings',
        'average_ratings',
        'is_active',
        'preferred_pet_types',
        'preferred_pet_sizes',
        'min_pets_capacity',
        'max_pets_capacity',
        'certificates_path',
    ];

    protected $casts = [
        'preferred_pet_types' => 'array',
        'preferred_pet_sizes' => 'array',
        'certificates_path' => 'array',
        'experience_years' => 'integer',
        'base_rate' => 'decimal:2',
        'food_budget' => 'decimal:2',
        'total_bookings' => 'integer',
        'average_ratings' => 'decimal:2',
        'is_active' => 'boolean',
        'min_pets_capacity' => 'integer',
        'max_pets_capacity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}