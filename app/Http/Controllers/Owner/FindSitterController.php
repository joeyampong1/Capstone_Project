<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;

class FindSitterController extends Controller
{
    public function index()
    {
        // ✅ PARA SA TESTING: I-include ang tanan sitters bisan 'pending' pa
        // TODO: I-enable ang ->where('sitter_status', 'approved') kung naa nay admin panel
        
        $sitters = User::with('sitterProfile')
            ->where('is_sitter', true)
            // ->where('sitter_status', 'approved')  // 🔒 I-uncomment kung naa nay admin
            ->whereHas('sitterProfile')  // Siguroha nga naay sitter_profile record
            ->get();

        // Debug: Check kung pila ka sitter ang nakuha
        // Uncomment ang line sa ubos kung gusto nimo i-verify
        // dd($sitters->count(), $sitters->pluck('sitter_status'));

        // Compute match percentage for each sitter
        $sitters = $sitters->map(function ($sitter) {
            $sitter->match_percentage = $this->computeMatch($sitter);
            return $sitter;
        })->sortByDesc('match_percentage')->values();

        return view('owner.find_sitter', compact('sitters'));
    }

    /**
     * Match algorithm – base sa rating, ID verified, experience, level
     * Pwede nimo i-enhance later base sa location, food preference, etc.
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

        // 4. Sitter Level (0-15 points)
        $level = (int) ($profile->sitter_type ?? 1);
        $score += ($level / 3) * 15;

        // 5. Total Bookings (0-5 points, capped at 50 bookings)
        $bookings = min($profile->total_bookings ?? 0, 50);
        $score += ($bookings / 50) * 5;

        return (int) round($score);
    }
}