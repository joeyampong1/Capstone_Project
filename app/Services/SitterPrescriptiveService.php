<?php

namespace App\Services;

use App\Models\SitterProfile;
use App\Models\User;
use Illuminate\Support\Collection;

class SitterPrescriptiveService
{
    /**
     * Ranks upcoming visits by priority and generates actionable recommendations for the sitter.
     *
     * Priority Score (0 - 100):
     * Urgency (closer scheduled_datetime = higher score, max 70 pts)
     * + Value weight (higher sitter_earnings relative to max in list, max 30 pts)
     */
    public function prioritizeVisitsAndAdvise(User $sitter, ?SitterProfile $sitterProfile, Collection $upcomingVisits): array
    {
        $maxEarning = max(1.0, (float) $upcomingVisits->max(fn ($v) => (float) ($v->booking->sitter_earnings ?? 0)));
        $now = now();

        $prioritizedVisits = $upcomingVisits->map(function ($visit) use ($now, $maxEarning) {
            $sched = $visit->scheduled_datetime ? \Carbon\Carbon::parse($visit->scheduled_datetime) : null;
            $hoursAway = $sched ? max(0.0, $now->diffInMinutes($sched, false) / 60.0) : 168.0;

            // Urgency: within 24h gets near-max urgency, decays up to 168h (7 days)
            $urgencyScore = max(0.0, min(1.0, 1.0 - ($hoursAway / 168.0))) * 70.0;

            $earning = (float) ($visit->booking->sitter_earnings ?? 0);
            $valueScore = ($earning / $maxEarning) * 30.0;

            $priorityScore = round($urgencyScore + $valueScore, 1);

            $visit->priority_score = $priorityScore;
            $visit->is_urgent = $hoursAway <= 24.0;

            return $visit;
        })->sortByDesc('priority_score')->values();

        $recommendations = [];

        // 1. Urgent visit alert
        $urgentVisit = $prioritizedVisits->first(fn ($v) => $v->is_urgent);
        if ($urgentVisit) {
            $petName = $urgentVisit->booking->pet->name ?? 'Pet';
            $recommendations[] = [
                'priority' => 'High',
                'title'    => 'Prepare for Upcoming Visit (' . $petName . ')',
                'reason'   => 'Scheduled within the next 24 hours (Priority Score: ' . $urgentVisit->priority_score . ').',
                'action'   => 'Review pet care notes, schedule time, and prepare photo proof checklist.',
            ];
        }

        // 2. Profile / Verification completeness advice
        if (!$sitter->isIdVerified()) {
            $recommendations[] = [
                'priority' => 'High',
                'title'    => 'Complete ID Verification',
                'reason'   => 'Verified ID increases owner trust and improves matching eligibility.',
                'action'   => 'Submit or check your ID verification status in your profile.',
            ];
        }

        // 3. Sitter profile details check
        if (!$sitterProfile || empty($sitterProfile->bio) || (float) ($sitterProfile->base_rate ?? 0) <= 0) {
            $recommendations[] = [
                'priority' => 'Medium',
                'title'    => 'Complete Sitter Profile & Base Rate',
                'reason'   => 'Complete profile details and clear rates help owners choose your service.',
                'action'   => 'Update your bio, preferences, and base rate in Sitter Profile.',
            ];
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'priority' => 'Low',
                'title'    => 'Keep Your Availability Updated',
                'reason'   => 'Your profile is set up and no immediate alerts were detected.',
                'action'   => 'Update your availability calendar regularly to attract more bookings.',
            ];
        }

        return [
            'prioritized_visits' => $prioritizedVisits,
            'recommendations'    => $recommendations,
        ];
    }
}
