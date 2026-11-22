<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SitterAnalyticsService
{
    public function getDashboardData(User $user): array
    {
        $sitterProfile = $user->sitterProfile;

        // ==========================================
        // DESCRIPTIVE ANALYTICS: STATS CARDS
        // ==========================================
        $totalEarnings = Booking::where('sitter_id', $user->id)
            ->where('status', 'completed')
            ->sum('sitter_earnings');

        $pendingPayments = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->sum('sitter_earnings');

        $pendingBookingsCount = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        $upcomingIncome = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->whereBetween('start_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->sum('sitter_earnings');

        $thisMonthEarnings = Booking::where('sitter_id', $user->id)
            ->where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('sitter_earnings');

        $thisMonthCompletedCount = Booking::where('sitter_id', $user->id)
            ->where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // ==========================================
        // DESCRIPTIVE ANALYTICS: MONTHLY EARNINGS (last 6 months)
        // ==========================================
        $monthlyEarnings = [];
        $maxEarning = 0;

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $sum = Booking::where('sitter_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->sum('sitter_earnings');

            $monthlyEarnings[] = [
                'label' => $date->format('M'),
                'value' => (float) $sum,
            ];

            if ($sum > $maxEarning) {
                $maxEarning = $sum;
            }
        }

        // ==========================================
        // DESCRIPTIVE ANALYTICS: EARNINGS BREAKDOWN (by pet type)
        // ==========================================
        $earningsByPetType = Booking::where('bookings.sitter_id', $user->id)
            ->where('bookings.status', 'completed')
            ->join('pets', 'bookings.pet_id', '=', 'pets.id')
            ->join('pet_types', 'pets.pet_type_id', '=', 'pet_types.id')
            ->selectRaw('pet_types.name as pet_type, SUM(bookings.sitter_earnings) as total')
            ->groupBy('pet_types.name')
            ->orderByDesc('total')
            ->get();

        $totalByType = $earningsByPetType->sum('total');

        // ==========================================
        // DESCRIPTIVE ANALYTICS: RECENT PAYMENTS & UPCOMING VISITS
        // ==========================================
        $recentPayments = Booking::with(['owner', 'pet'])
            ->where('sitter_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('updated_at')
            ->limit(4)
            ->get();

        $upcomingVisits = Visit::with(['booking.pet', 'booking.owner'])
            ->whereHas('booking', function ($q) use ($user) {
                $q->where('sitter_id', $user->id)
                  ->whereIn('status', ['accepted', 'completed']);
            })
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('scheduled_datetime', '>=', now())
            ->orderBy('scheduled_datetime')
            ->limit(4)
            ->get();

        // ==========================================
        // DESCRIPTIVE ANALYTICS: PERFORMANCE STATS
        // ==========================================
        $averageRating = (float) ($sitterProfile->average_ratings ?? 0);
        $reviewsCount  = $sitterProfile
            ? Review::where('sitter_id', $sitterProfile->id)->count()
            : 0;

        $totalVisits = Visit::whereHas('booking', fn ($q) => $q->where('sitter_id', $user->id))->count();
        $completedVisits = Visit::whereHas('booking', fn ($q) => $q->where('sitter_id', $user->id))
            ->where('status', 'completed')
            ->count();
        $completionRate = $totalVisits > 0
            ? round(($completedVisits / $totalVisits) * 100)
            : 0;

        $activeBookings = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        $uniqueOwners = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->distinct('owner_id')
            ->count('owner_id');

        $totalVisitsLast30Days = Visit::whereHas('booking', fn ($q) => $q->where('sitter_id', $user->id))
            ->where('scheduled_datetime', '>=', now()->subDays(30))
            ->count();

        // ==========================================
        // PRESCRIPTIVE ANALYTICS: VISIT PRIORITY & RECOMMENDATIONS
        // ==========================================
        $sitterPrescriptive = $this->prioritizeVisitsAndAdvise($user, $sitterProfile, $upcomingVisits);

        return compact(
            'totalEarnings',
            'pendingPayments',
            'pendingBookingsCount',
            'upcomingIncome',
            'thisMonthEarnings',
            'thisMonthCompletedCount',
            'monthlyEarnings',
            'maxEarning',
            'earningsByPetType',
            'totalByType',
            'recentPayments',
            'upcomingVisits',
            'averageRating',
            'reviewsCount',
            'completionRate',
            'completedVisits',
            'totalVisits',
            'activeBookings',
            'uniqueOwners',
            'totalVisitsLast30Days',
            'sitterPrescriptive'
        );
    }

    private function prioritizeVisitsAndAdvise(User $sitter, $sitterProfile, Collection $upcomingVisits): array
    {
        $maxEarning = max(1.0, (float) $upcomingVisits->max(fn ($v) => (float) ($v->booking->sitter_earnings ?? 0)));
        $now = now();

        $prioritizedVisits = $upcomingVisits->map(function ($visit) use ($now, $maxEarning) {
            $sched = $visit->scheduled_datetime ? Carbon::parse($visit->scheduled_datetime) : null;
            $hoursAway = $sched ? max(0.0, $now->diffInMinutes($sched, false) / 60.0) : 168.0;

            $urgencyScore = max(0.0, min(1.0, 1.0 - ($hoursAway / 168.0))) * 70.0;
            $earning = (float) ($visit->booking->sitter_earnings ?? 0);
            $valueScore = ($earning / $maxEarning) * 30.0;

            $visit->priority_score = round($urgencyScore + $valueScore, 1);
            $visit->is_urgent = $hoursAway <= 24.0;

            return $visit;
        })->sortByDesc('priority_score')->values();

        $recommendations = [];

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

        if (!$sitter->isIdVerified()) {
            $recommendations[] = [
                'priority' => 'High',
                'title'    => 'Complete ID Verification',
                'reason'   => 'Verified ID increases owner trust and improves matching eligibility.',
                'action'   => 'Submit or check your ID verification status in your profile.',
            ];
        }

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
