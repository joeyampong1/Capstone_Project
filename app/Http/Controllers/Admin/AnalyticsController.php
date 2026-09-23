<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->query('range', 'month');

        [$startDate, $endDate, $prevStart, $prevEnd] = $this->resolveRange($range);

        // -------------------------------------------------------------
        // KPIs
        // -------------------------------------------------------------
        $totalUsers     = User::count();
        $totalBookings  = Booking::count();
        $activeSitters  = User::where('is_sitter', true)->where('sitter_status', 'approved')->count();
        $verifiedSitter = User::where('is_sitter', true)->where('sitter_status', 'approved')->where('id_validation_status', 'verified')->count();

        $completedBookings  = Booking::where('status', 'completed')->count();
        $cancelledBookings  = Booking::where('status', 'cancelled')->count();
        $pendingBookings    = Booking::where('status', 'pending')->count();

        $totalComplaints    = Complaint::count();
        $pendingComplaints  = Complaint::where('status', 'pending')->count();

        // Growth
        $userGrowth = User::whereBetween('created_at', [$startDate, $endDate])->count()
                    - User::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $currentBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $prevBookings    = Booking::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $bookingGrowth   = $prevBookings > 0
            ? round((($currentBookings - $prevBookings) / $prevBookings) * 100, 1)
            : 0;

        $kpis = [
            'total_users'         => $totalUsers,
            'user_growth'         => $userGrowth,
            'active_sitters'      => $activeSitters,
            'verified_sitter_pct' => $activeSitters > 0 ? round(($verifiedSitter / $activeSitters) * 100) : 0,
            'total_bookings'      => $totalBookings,
            'booking_growth'      => $bookingGrowth,
            'completed_bookings'  => $completedBookings,
            'completion_rate'     => $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 1) : 0,
            'cancelled_bookings'  => $cancelledBookings,
            'cancellation_rate'   => $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 1) : 0,
            'total_complaints'    => $totalComplaints,
            'pending_complaints'  => $pendingComplaints,
        ];

        // -------------------------------------------------------------
        // Booking Trend — last 7 months
        // -------------------------------------------------------------
        $bookingTrend = collect(range(6, 0))->map(function ($i) {
            $month = Carbon::now()->subMonths($i);
            return [
                'month' => $month->format('M'),
                'count' => Booking::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        })->all();

        // -------------------------------------------------------------
        // Booking Status
        // -------------------------------------------------------------
        $bookingStatus = [
            'completed' => $completedBookings,
            'pending'   => $pendingBookings,
            'cancelled' => $cancelledBookings,
        ];

        // -------------------------------------------------------------
        // User Growth
        // -------------------------------------------------------------
        $owners  = User::where('role', 'owner')->where('is_sitter', false)->count();
        $sitters = User::where('is_sitter', true)->count();
        $admins  = User::where('role', 'admin')->count();

        $userGrowthData = [
            'owners'  => $owners,
            'sitters' => $sitters,
            'admins'  => $admins,
        ];

        // -------------------------------------------------------------
        // Verification
        // -------------------------------------------------------------
        $verifiedCount = User::where('id_validation_status', 'verified')->count();
        $pendingCount  = User::where('id_validation_status', 'pending')->count();
        $verifTotal    = max(1, $verifiedCount + $pendingCount);

        $verification = [
            'verified'     => $verifiedCount,
            'pending'      => $pendingCount,
            'verified_pct' => round(($verifiedCount / $verifTotal) * 100, 1),
            'pending_pct'  => round(($pendingCount / $verifTotal) * 100, 1),
        ];

        // -------------------------------------------------------------
        // Pet Type (assumes pets.type exists — adjust if needed)
        // -------------------------------------------------------------
        $petType = ['dogs' => 0, 'cats' => 0, 'others' => 0];

        if (Schema::hasColumn('pets', 'type')) {
            $petCounts = \DB::table('pets')
                ->selectRaw('LOWER(type) as t, COUNT(*) as c')
                ->groupBy('t')
                ->pluck('c', 't');

            foreach ($petCounts as $type => $count) {
                if (str_contains($type, 'dog'))       $petType['dogs']   += $count;
                elseif (str_contains($type, 'cat'))   $petType['cats']   += $count;
                else                                  $petType['others'] += $count;
            }
        }

        // -------------------------------------------------------------
        // Schedule (from bookings.visit_time or schedule_times — best effort)
        // -------------------------------------------------------------
        $schedule = ['morning' => 0, 'afternoon' => 0, 'evening' => 0];

        if (Schema::hasColumn('bookings', 'visit_time')) {
            $schedule['morning']   = Booking::whereTime('visit_time', '<', '12:00:00')->count();
            $schedule['afternoon'] = Booking::whereTime('visit_time', '>=', '12:00:00')
                                            ->whereTime('visit_time', '<', '18:00:00')->count();
            $schedule['evening']   = Booking::whereTime('visit_time', '>=', '18:00:00')->count();
        }

        // -------------------------------------------------------------
        // Complaint Distribution
        // -------------------------------------------------------------
        $complaintDistribution = [];

        if (Schema::hasColumn('complaints', 'type')) {
            $typeLabels = [
                'missed_visit'  => ['label' => 'Missed Visit',  'color' => 'bg-amber-500'],
                'poor_service'  => ['label' => 'Poor Service',  'color' => 'bg-red-500'],
                'no_proof'      => ['label' => 'No Proof',      'color' => 'bg-blue-500'],
                'rude_behavior' => ['label' => 'Rude Behavior', 'color' => 'bg-purple-500'],
                'others'        => ['label' => 'Others',        'color' => 'bg-neutral-400'],
            ];

            $totalComplaintsForDist = max(1, Complaint::count());

            $complaintDistribution = Complaint::selectRaw('type, COUNT(*) as total')
                ->groupBy('type')
                ->orderByDesc('total')
                ->get()
                ->map(function ($row) use ($typeLabels, $totalComplaintsForDist) {
                    $meta = $typeLabels[$row->type] ?? ['label' => ucfirst($row->type), 'color' => 'bg-neutral-400'];
                    return [
                        'label' => $meta['label'],
                        'color' => $meta['color'],
                        'pct'   => round(($row->total / $totalComplaintsForDist) * 100, 1),
                    ];
                })
                ->all();
        }

        // -------------------------------------------------------------
        // Complaint Status
        // -------------------------------------------------------------
        $complaintStatus = [
            'resolved'  => Complaint::where('status', 'resolved')->count(),
            'pending'   => Complaint::where('status', 'pending')->count(),
            'dismissed' => Complaint::where('status', 'dismissed')->count(),
        ];

        // -------------------------------------------------------------
        // Top Sitters
        // -------------------------------------------------------------
        $topSitters = Booking::selectRaw('sitter_id, COUNT(*) as completed_count')
            ->where('status', 'completed')
            ->whereNotNull('sitter_id')
            ->groupBy('sitter_id')
            ->orderByDesc('completed_count')
            ->take(3)
            ->get()
            ->map(function ($row) {
                $user = User::find($row->sitter_id);
                if (! $user) return null;
                $user->completed_count = $row->completed_count;
                $user->average_rating  = $user->sitterProfile->average_ratings ?? 0;
                return $user;
            })
            ->filter()
            ->values();

        // -------------------------------------------------------------
        // Top Owners
        // -------------------------------------------------------------
        $topOwners = Booking::selectRaw('owner_id, COUNT(*) as booking_count')
            ->whereNotNull('owner_id')
            ->groupBy('owner_id')
            ->orderByDesc('booking_count')
            ->take(3)
            ->get()
            ->map(function ($row) {
                $user = User::find($row->owner_id);
                if (! $user) return null;
                $user->booking_count   = $row->booking_count;
                $user->completed_count = Booking::where('owner_id', $row->owner_id)
                    ->where('status', 'completed')->count();
                return $user;
            })
            ->filter()
            ->values();

        // -------------------------------------------------------------
        // Recent Activities (latest 4 cross-source)
        // -------------------------------------------------------------
        $recentActivities = collect();

        // Latest bookings
        Booking::latest()->take(2)->get()->each(function ($b) use ($recentActivities) {
            $recentActivities->push([
                'title'        => 'New Booking #' . $b->id,
                'time'         => optional($b->created_at)->diffForHumans(),
                'badge'        => ucfirst($b->status),
                'badge_color'  => 'text-primary',
                'icon_bg'      => 'bg-primary/10',
                'icon_color'   => 'text-primary',
                'icon'         => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                'sort_at'      => $b->created_at,
            ]);
        });

        // Latest complaints
        Complaint::latest()->take(2)->get()->each(function ($c) use ($recentActivities) {
            $recentActivities->push([
                'title'        => 'Complaint #' . $c->id . ' submitted',
                'time'         => optional($c->created_at)->diffForHumans(),
                'badge'        => ucfirst($c->status),
                'badge_color'  => 'text-amber-600 dark:text-amber-400',
                'icon_bg'      => 'bg-amber-100/20',
                'icon_color'   => 'text-amber-600',
                'icon'         => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
                'sort_at'      => $c->created_at,
            ]);
        });

        $recentActivities = $recentActivities
            ->sortByDesc('sort_at')
            ->take(4)
            ->values()
            ->all();

        // -------------------------------------------------------------
        // Platform Health
        // -------------------------------------------------------------
        $bookingSuccessRate = $totalBookings > 0
            ? round(($completedBookings / $totalBookings) * 100, 1)
            : 0;

        $health = [
            [
                'label'   => 'Booking Success',
                'display' => $bookingSuccessRate . '%',
                'pct'     => $bookingSuccessRate,
                'color'   => 'bg-primary',
                'text'    => 'text-[#1B3B36] dark:text-white',
            ],
            [
                'label'   => 'Verification Rate',
                'display' => $verification['verified_pct'] . '%',
                'pct'     => $verification['verified_pct'],
                'color'   => 'bg-blue-500',
                'text'    => 'text-[#1B3B36] dark:text-white',
            ],
            [
                'label'   => 'Complaint Rate',
                'display' => ($totalBookings > 0 ? round(($totalComplaints / $totalBookings) * 100, 1) : 0) . '%',
                'pct'     => min(100, $totalBookings > 0 ? round(($totalComplaints / $totalBookings) * 100, 1) : 0),
                'color'   => 'bg-amber-500',
                'text'    => 'text-amber-600 dark:text-amber-400',
            ],
            [
                'label'   => 'Cancellation Rate',
                'display' => $kpis['cancellation_rate'] . '%',
                'pct'     => $kpis['cancellation_rate'],
                'color'   => 'bg-red-500',
                'text'    => 'text-red-600 dark:text-red-400',
            ],
        ];

        // -------------------------------------------------------------
        // Insights
        // -------------------------------------------------------------
        $topPetLabel = 'Dogs';
        $topPetCount = $petType['dogs'];
        foreach (['Cats' => $petType['cats'], 'Others' => $petType['others']] as $lbl => $cnt) {
            if ($cnt > $topPetCount) { $topPetLabel = $lbl; $topPetCount = $cnt; }
        }
        $topPetPct = array_sum($petType) > 0 ? round(($topPetCount / array_sum($petType)) * 100) : 0;

        $insights = [
            'Bookings ' . ($bookingGrowth >= 0 ? 'increased' : 'decreased') . ' by <span class="font-bold ' . ($bookingGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400') . '">' . abs($bookingGrowth) . '%</span> this ' . $range . '.',
            $topPetLabel . ' bookings are the highest at <span class="font-bold text-[#1B3B36] dark:text-white">' . $topPetPct . '%</span>.',
            '<span class="font-bold text-[#1B3B36] dark:text-white">' . $verification['verified_pct'] . '%</span> of users have verified accounts.',
            'Complaint rate is at <span class="font-bold text-[#1B3B36] dark:text-white">' . ($totalBookings > 0 ? round(($totalComplaints / $totalBookings) * 100, 1) : 0) . '%</span> of all bookings.',
            'Active sitters: <span class="font-bold text-[#1B3B36] dark:text-white">' . $activeSitters . '</span> (' . $kpis['verified_sitter_pct'] . '% verified).',
        ];

        return view('admin.analytics.analytics', compact(
            'range',
            'kpis',
            'bookingTrend',
            'bookingStatus',
            'userGrowthData',
            'verification',
            'petType',
            'schedule',
            'complaintDistribution',
            'complaintStatus',
            'topSitters',
            'topOwners',
            'recentActivities',
            'health',
            'insights',
        ));
    }

    private function resolveRange(string $range): array
    {
        $now = Carbon::now();

        return match ($range) {
            'today' => [
                $now->copy()->startOfDay(), $now->copy()->endOfDay(),
                $now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay(),
            ],
            'week' => [
                $now->copy()->startOfWeek(), $now->copy()->endOfWeek(),
                $now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek(),
            ],
            'year' => [
                $now->copy()->startOfYear(), $now->copy()->endOfYear(),
                $now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear(),
            ],
            default => [ // month
                $now->copy()->startOfMonth(), $now->copy()->endOfMonth(),
                $now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth(),
            ],
        };
    }
}