<?php

namespace App\Http\Controllers\Sitter;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sitterProfile = $user->sitterProfile;

        // ==========================================
        // 1. STATS CARDS
        // ==========================================

        // Total Earnings — completed bookings
        $totalEarnings = Booking::where('sitter_id', $user->id)
            ->where('status', 'completed')
            ->sum('sitter_earnings');

        // Pending Payments — accepted bookings (ongoing)
        $pendingPayments = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->sum('sitter_earnings');

        $pendingBookingsCount = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        // Upcoming Income — accepted bookings starting next 7 days
        $upcomingIncome = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->whereBetween('start_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->sum('sitter_earnings');

        // This Month — completed bookings this month
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
        // 2. MONTHLY EARNINGS (last 6 months)
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
        // 3. EARNINGS BREAKDOWN (by pet type)
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
        // 4. RECENT PAYMENTS (last 4 completed bookings)
        // ==========================================
        $recentPayments = Booking::with(['owner', 'pet'])
            ->where('sitter_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('updated_at')
            ->limit(4)
            ->get();

        // ==========================================
        // 5. UPCOMING VISITS (next 4 visits)
        // ==========================================
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
        // 6. PERFORMANCE STATS
        // ==========================================

        // Average rating
        $averageRating = (float) ($sitterProfile->average_ratings ?? 0);
        $reviewsCount  = $sitterProfile
            ? Review::where('sitter_id', $sitterProfile->id)->count()
            : 0;

        // Completion rate
        $totalVisits = Visit::whereHas('booking', fn($q) => $q->where('sitter_id', $user->id))->count();
        $completedVisits = Visit::whereHas('booking', fn($q) => $q->where('sitter_id', $user->id))
            ->where('status', 'completed')
            ->count();
        $completionRate = $totalVisits > 0
            ? round(($completedVisits / $totalVisits) * 100)
            : 0;

        // Active bookings
        $activeBookings = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        $uniqueOwners = Booking::where('sitter_id', $user->id)
            ->where('status', 'accepted')
            ->distinct('owner_id')
            ->count('owner_id');

        // Total visits last 30 days
        $totalVisitsLast30Days = Visit::whereHas('booking', fn($q) => $q->where('sitter_id', $user->id))
            ->where('scheduled_datetime', '>=', now()->subDays(30))
            ->count();

        // ==========================================
        // PASS TO VIEW
        // ==========================================
        return view('sitters.sitter_dashboard', compact(
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
            'totalVisitsLast30Days'
        ));
    }
}