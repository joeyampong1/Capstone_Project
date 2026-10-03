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
                'comments.user',
                'comments.replies.user',
            ])
            ->findOrFail($id);

        if (!$sitter->sitterProfile) {
            abort(404, 'This user does not have a sitter profile.');
        }

        $profile = $sitter->sitterProfile;

        // Pet types
        $petTypeIds = $profile->preferred_pet_types ?? [];
        if (is_string($petTypeIds)) {
            $petTypeIds = json_decode($petTypeIds, true) ?? [];
        }
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

        // Sitter type — category-based
        $sitter->level = match($profile->sitter_type) {
            'small_pets'  => 1,
            'large_pets'  => 2,
            'exotic_pets' => 3,
            'all_pets'    => 4,
            default       => 1,
        };

        $sitter->sitter_type_label = match($profile->sitter_type) {
            'small_pets'  => 'Small Pets',
            'large_pets'  => 'Large Pets',
            'exotic_pets' => 'Exotic Pets',
            'all_pets'    => 'All Pets',
            default       => 'Small Pets',
        };

        $sitter->sitter_type_icon = match($profile->sitter_type) {
            'small_pets'  => '🐱',
            'large_pets'  => '🐕',
            'exotic_pets' => '🦜',
            'all_pets'    => '🐾',
            default       => '🐱',
        };

        $sitter->available_dates_count = $availableDatesCount;
        $sitter->pet_types_list = !empty($petTypeNames) ? strtolower(implode(', ', $petTypeNames)) : 'Not specified';
        $sitter->bio_text = $profile->bio ?? 'This sitter hasn\'t added a bio yet.';

        // Food arrangement — support both old and new enum values
        $sitter->food_arrangement = match ($profile->food_preference ?? '') {
            'owner_provides', 'owner_provided'   => 'Owner provides food',
            'sitter_provides', 'sitter_provided' => 'Sitter provides food',
            'flexible'                            => 'Flexible (both options)',
            default                               => 'Not set',
        };

        // Empty reviews for now
        $sitter->setRelation('reviews', collect([]));

        return view('owner.sitter_profile', compact('sitter', 'profile'));
    }
}
