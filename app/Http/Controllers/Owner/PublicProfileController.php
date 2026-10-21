<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;

class PublicProfileController extends Controller
{
    public function show($id)
    {
        // Eager load the 'setting' relationship to check visibility
        $user = User::with(['sitterProfile', 'pets.petType', 'setting'])
            ->findOrFail($id);

        // PRIVACY CHECK
        if (auth()->id() !== $user->id && !auth()->user()?->isAdmin()) {
            // Block access completely if HIDDEN
            if ($user->setting && $user->setting->profile_visibility === 'hidden') {
                abort(403, 'This profile is hidden by the user.');
            }

            // 'Private' profiles are allowed here because it's a direct link access
        }

        // Reviews count (placeholder)
        $reviewsCount = 0;

        // Average rating (default 3.0 if no reviews)
        $averageRating = $user->average_rating ?? 3.0;

        return view('profile.public', compact('user', 'reviewsCount', 'averageRating'));
    }
}
