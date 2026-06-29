<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // ========================================== //
        // PROFILE INFORMATION FIELDS                 //
        // ========================================== //
        'name',
        'email',
        'date_of_birth',
        'gender',
        'contact_number',
        'address',
        'password',
        // ========================================== //
        // ROLE & BASIC INFO                          //
        // ========================================== //
        'role',
        
        // ========================================== //
        // SITTER-SPECIFIC FIELDS                     //
        // ========================================== //
        'is_sitter',
        'sitter_level',
        'sitter_status',
        'sitter_applied_at',
        'sitter_approved_at',
        
        // ========================================== //
        // OWNER-SPECIFIC FIELDS                     //
        // ========================================== //
        'location',
        'latitude',
        'longitude',
        
        // ========================================== //
        // PROFILE & VERIFICATION                    //
        // ========================================== //
        'profile_photo',
        'gov_id_path',
        'id_validation_status',
        'id_type',          
        'selfie_photo',
        
        // ========================================== //
        // SYSTEM & STATUS                          //
        // ========================================== //
        'pet_count',
        'status',
        'last_active_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_sitter' => 'boolean',
            'sitter_applied_at' => 'datetime',
            'sitter_approved_at' => 'datetime',
            'last_active_at' => 'datetime',
            'id_validation_status' => 'string',
            'date_of_birth' => 'date',
        ];
    }


    // ========================================== //
    // ROLE CHECK METHODS                         //
    // ========================================== //

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an owner (default role)
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check if user has sitter mode enabled and is approved
     */
    public function isSitter(): bool
    {
        return $this->is_sitter && $this->sitter_status === 'approved';
    }

    /**
     * Check if user has a pending sitter application
     */
    public function hasPendingSitterApplication(): bool
    {
        return $this->is_sitter && $this->sitter_status === 'pending';
    }

    /**
     * Check if user's sitter application was rejected
     */
    public function isSitterRejected(): bool
    {
        return $this->is_sitter && $this->sitter_status === 'rejected';
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user account is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if user's ID is verified
     */
    public function isIdVerified(): bool
    {
        return $this->id_validation_status === 'verified';
    }

    // ========================================== //
    // SITTER LEVEL METHODS                       //
    // ========================================== //

    /**
     * Get sitter level badge name
     */
    public function getSitterLevelBadge(): string
    {
        return match ($this->sitter_level) {
            1 => 'Basic',
            2 => 'Intermediate',
            3 => 'Advanced',
            default => 'Basic',
        };
    }

    /**
     * Get sitter level color for badges
     */
    public function getSitterLevelColor(): string
    {
        return match ($this->sitter_level) {
            1 => 'gray',
            2 => 'accent',
            3 => 'primary',
            default => 'gray',
        };
    }

    /**
     * Get sitter level rate multiplier
     */
    public function getSitterRateMultiplier(): float
    {
        return match ($this->sitter_level) {
            1 => 1.0,   // Base rate
            2 => 1.2,   // +20%
            3 => 1.5,   // +50%
            default => 1.0,
        };
    }

    // ========================================== //
    // ACCESSOR METHODS                           //
    // ========================================== //

    /**
     * Get profile photo URL or avatar fallback
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=f07a3a&color=fff&size=100';
    }

    /**
     * Get user's location display
     */
    public function getLocationDisplayAttribute(): string
    {
        return $this->location ?? 'Location not set';
    }

    /**
     * Get user's full display name with role
     */
    public function getDisplayNameAttribute(): string
    {
        $suffix = '';
        if ($this->isAdmin()) {
            $suffix = ' (Admin)';
        } elseif ($this->isSitter()) {
            $suffix = ' ★' . $this->getSitterLevelBadge();
        }
        
        return $this->name . $suffix;
    }

    /**
     * Check if user can switch to sitter mode
     */
    public function canSwitchToSitter(): bool
    {
        return $this->isSitter() && $this->isActive();
    }

    /**
     * Check if user can apply to be a sitter
     */
    public function canApplyToBeSitter(): bool
    {
        return $this->isOwner() && 
               !$this->is_sitter && 
               $this->isActive() && 
               $this->isIdVerified();
    }

    // ========================================== //
    // RELATIONSHIPS                              //
    // ========================================== //

    /**
     * Get all pets owned by this user
     */
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * Get all bookings where user is the owner
     */
    public function bookingsAsOwner()
    {
        return $this->hasMany(Booking::class, 'owner_id');
    }

    /**
     * Get all bookings where user is the sitter
     */
    public function bookingsAsSitter()
    {
        return $this->hasMany(Booking::class, 'sitter_id');
    }

    /**
     * Get all reviews received by this user (as sitter)
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'sitter_id');
    }

    /**
     * Get all comments about this user (as sitter)
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'sitter_id');
    }

    /**
     * Get average rating of this user (as sitter)
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->avg('rating') ?? 3.0;
    }

    /**
     * Get total number of reviews
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->reviews()->count();
    }

    // ========================================== //
    // SCOPE METHODS                              //
    // ========================================== //

    /**
     * Scope query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope query to only include sitters (approved)
     */
    public function scopeSitters($query)
    {
        return $query->where('is_sitter', true)
                     ->where('sitter_status', 'approved');
    }

    /**
     * Scope query to only include owners
     */
    public function scopeOwners($query)
    {
        return $query->where('role', 'owner');
    }

    /**
     * Scope query to only include admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }
}