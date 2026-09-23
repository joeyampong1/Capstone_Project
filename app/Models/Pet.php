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
        'dietary_restrictions', 
        'photo_path'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'length' => 'decimal:2', 
        'width'  => 'decimal:2', 
        'age'    => 'integer',  
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
}