<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SitterProfileController extends Controller
{
    public function show($id)
    {
        // Load only reviews (and reviewer user)
        $sitter = User::with(['reviews.user'])->findOrFail($id);

        if (!$sitter->isSitter()) {
            abort(404, 'This user is not a registered sitter.');
        }

        // Add an empty comments collection so the view works
        $sitter->comments = collect([]);

        // Computed attributes (with fallbacks)
        $sitter->average_rating = $sitter->reviews->avg('rating') ?? 0;
        $sitter->reviews_count = $sitter->reviews->count();
        $sitter->daily_rate = $sitter->rate_per_visit ?? 300;
        $sitter->provides_food = $sitter->can_provide_food ?? false;
        $sitter->food_arrangement = $sitter->food_preference ?? 'Both options available';
        $sitter->available_dates_count = 0; // Replace with actual logic later
        $sitter->accepted_pets = $sitter->pet_types ?? 'dog, cat';
        $sitter->bio = $sitter->bio ?? 'I love caring for pets and ensuring they feel safe and happy.';

        return view('owner.sitter_profile', compact('sitter'));
    }
}