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
        'f_name',         
        'm_name',          
        'l_name',   
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
        'admin_notes',
        'id_reviewed_by',
        'id_reviewed_at',
        'face_match_score',
        'ocr_result',
        'face_detected_on_id',
        'face_detected_on_selfie',
        'document_authenticity',
        'liveness_detection',
        'id_expired',
        'name_match',
        'birthdate_match',
        'locale',
        
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
            'id_reviewed_at'    => 'datetime',
            'id_expired'        => 'boolean',
            'face_match_score'  => 'float',
            'ocr_result' => 'array',
            'face_detected_on_id' => 'boolean',
            'face_detected_on_selfie' => 'boolean',
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
        return $this->is_sitter && $this->id_validation_status === 'verified';
    }

    public function hasPendingSitterApplication(): bool
    {
        return $this->is_sitter && $this->id_validation_status === 'pending';
    }

    public function isSitterRejected(): bool
    {
        return $this->is_sitter && $this->id_validation_status === 'rejected';
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

       /**
     * Full name without suffix — for admin tables and modal
     */
    public function getFullNameAttribute(): string
    {
        return trim(($this->f_name ?? '') . ' ' . ($this->l_name ?? ''));
    }

    /**
     * Role label — 'owner' or 'sitter'
     */
    public function getIdRoleAttribute(): string
    {
        return $this->is_sitter ? 'sitter' : 'owner';
    }

    /**
     * Government ID image URL for admin modal
     */
    public function getGovIdUrlAttribute(): ?string
    {
        return $this->gov_id_path
            ? \Illuminate\Support\Facades\Storage::url($this->gov_id_path)
            : null;
    }

    /**
     * Selfie image URL for admin modal
     */
    public function getSelfieUrlAttribute(): ?string
    {
        return $this->selfie_photo
            ? \Illuminate\Support\Facades\Storage::url($this->selfie_photo)
            : null;
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
        
        $nameToUse = $this->f_name ?? $this->email;
        return 'https://ui-avatars.com/api/?name=' . urlencode($nameToUse) . '&background=f07a3a&color=fff&size=100';
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
        $fullName = trim($this->f_name . ' ' . $this->l_name);
        if ($fullName === '') {
            $fullName = $this->email; // fallback to email if no names
        }
        
        $suffix = '';
        if ($this->isAdmin()) {
            $suffix = ' (Admin)';
        } elseif ($this->isSitter()) {
            $suffix = ' ★' . $this->getSitterLevelBadge();
        }
        
        return $fullName . $suffix;
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
     * Get the user's sitter profile (if user is a sitter)
     */
    public function sitterProfile()
    {
        return $this->hasOne(SitterProfile::class, 'user_id');
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
     * Comments written by this user
     */
    public function commentsWritten()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * Comments received by this user (as a sitter)
     */
    public function commentsReceived()
    {
        return $this->hasMany(Comment::class, 'sitter_id');
    }

    /**
     * Alias for commentsReceived (para sa sitter profile page)
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'sitter_id')
                    ->whereNull('parent_id')
                    ->with('replies.user', 'user')
                    ->orderBy('created_at', 'desc');
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

    // app/Models/User.php

    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    /**
     * Get setting with automatic fallback (creates if missing)
     */
    public function getSettingAttribute()
    {
        return $this->relationLoaded('setting')
            ? $this->getRelation('setting')
            : ($this->setting()->firstOrCreate([])); // lazy create
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->setting()->create([]);
        });
    }

}