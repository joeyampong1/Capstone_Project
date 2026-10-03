<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;

class FindSitterController extends Controller
{
    public function index()
    {
        // For testing: include all sitters even 'pending'
        // TODO: Enable ->where('sitter_status', 'approved') once admin panel is ready

        $sitters = User::with('sitterProfile')
            ->where('is_sitter', true)
            // ->where('sitter_status', 'approved')
            ->whereHas('sitterProfile')
            ->get();

        // Compute match percentage for each sitter
        $sitters = $sitters->map(function ($sitter) {
            $sitter->match_percentage = $this->computeMatch($sitter);
            return $sitter;
        })->sortByDesc('match_percentage')->values();

        return view('owner.find_sitter', compact('sitters'));
    }

    /**
     * Match algorithm — based on rating, ID verification, experience, and sitter type.
     * Can be enhanced later with location, food preference, etc.
     */
    private function computeMatch($sitter)
    {
        $profile = $sitter->sitterProfile;
        if (!$profile) return 0;

        $score = 0;

        // 1. Rating (0-40 points)
        $rating = $profile->average_ratings ?? 0;
        $score += ($rating / 5) * 40;

        // 2. ID Verified (0-20 points)
        if ($sitter->id_validation_status === 'verified') {
            $score += 20;
        }

        // 3. Experience (0-20 points, max at 5 years)
        $years = min($profile->experience_years ?? 0, 5);
        $score += ($years / 5) * 20;

        // 4. Sitter Type (0-15 points) — category-based
        $typeScore = match($profile->sitter_type) {
            'small_pets'  => 8,    // Basic — small pets only
            'large_pets'  => 10,   // Medium — large pets
            'exotic_pets' => 12,   // Advanced — exotic pets
            'all_pets'    => 15,   // Expert — all pets
            default       => 5,    // Fallback
        };
        $score += $typeScore;

        // 5. Total Bookings (0-5 points, capped at 50 bookings)
        $bookings = min($profile->total_bookings ?? 0, 50);
        $score += ($bookings / 50) * 5;

        return (int) round($score);
    }
}
