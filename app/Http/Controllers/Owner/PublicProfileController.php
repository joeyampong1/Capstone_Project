<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;

class PublicProfileController extends Controller
{
    public function show($id)
    {
        // Fetch user with relationships
        $user = User::with(['sitterProfile', 'pets.petType'])
            ->findOrFail($id);

        // Reviews count (placeholder)
        $reviewsCount = 0;

        // Average rating (default 3.0 if walay reviews)
        $averageRating = $user->average_rating ?? 3.0;

        return view('profile.public', compact('user', 'reviewsCount', 'averageRating'));
    }
}