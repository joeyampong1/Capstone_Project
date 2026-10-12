<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_type_id',
        'name',
        'breed',
        'size',
        'temperament',
        'age',
        'weight',
        'weight_unit',
        'height',
        'height_unit',
        'length',
        'length_unit',
        'width',
        'width_unit',
        'special_needs',
        'medical_conditions',
        'vaccination_status',
        'dietary_restrictions',
        'photo_path',
        'photo_public_id',
    ];

    protected $casts = [
        'weight'             => 'decimal:2',
        'height'             => 'decimal:2',
        'length'             => 'decimal:2',
        'width'              => 'decimal:2',
        'age'                => 'integer',
        'vaccination_status' => 'string',
    ];

    // Relationship to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to PetType
    public function petType(): BelongsTo
    {
        return $this->belongsTo(PetType::class);
    }

    /**
     * Photo URL — auto-detects Cloudinary vs local.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo_path) return null;

        // Cloudinary or external URL
        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        // Local storage fallback
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->photo_path)) {
            return asset('storage/' . $this->photo_path);
        }

        return null;
    }

}
