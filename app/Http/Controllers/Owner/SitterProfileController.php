<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PetType;
use App\Models\Availability;
use Illuminate\Support\Facades\Auth;

class SitterProfileController extends Controller
{
    public function show($id)
    {
        $sitter = User::with([
                'sitterProfile',
                'pets',
                'comments.user',           // top-level comments with user
                'comments.replies.user',   // nested replies with user
            ])
            ->findOrFail($id);

        if (!$sitter->sitterProfile) {
            abort(404, 'This user does not have a sitter profile.');
        }

        $profile = $sitter->sitterProfile;

        // Pet types
        $petTypeIds = $profile->preferred_pet_types ?? [];
        $petTypeNames = \App\Models\PetType::whereIn('id', $petTypeIds)->pluck('name')->toArray();

        // Available dates
        $availableDatesCount = \App\Models\Availability::where('sitter_id', $sitter->id)
            ->where('date', '>=', now()->toDateString())
            ->where('is_available', true)
            ->count();

        // Attach computed values
        $sitter->display_name = trim($sitter->f_name . ' ' . $sitter->l_name) ?: 'Sitter';
        $sitter->average_rating = $profile->average_ratings ?? 0;
        $sitter->reviews_count = $profile->total_bookings ?? 0;
        $sitter->daily_rate = $profile->base_rate ?? 0;
        $sitter->level = (int) ($profile->sitter_type ?? 1);
        $sitter->available_dates_count = $availableDatesCount;
        $sitter->pet_types_list = !empty($petTypeNames) ? strtolower(implode(', ', $petTypeNames)) : 'Not specified';
        $sitter->bio_text = $profile->bio ?? 'This sitter hasn\'t added a bio yet.';

        $sitter->food_arrangement = match ($profile->food_preference ?? '') {
            'owner_provides' => 'Owner provides food',
            'sitter_provides' => 'Sitter provides food',
            'flexible' => 'Flexible (both options)',
            default => 'Not set',
        };

        // Empty reviews for now (kay wala pa kay Review model)
        $sitter->setRelation('reviews', collect([]));

        return view('owner.sitter_profile', compact('sitter', 'profile'));
    }
    
}