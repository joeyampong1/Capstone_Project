<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // 1. SUMMARY CARDS — ROW 1
        // ==========================================
        $totalUsers          = User::count();
        $totalOwners         = User::where('role', 'owner')->count();
        $totalSitters        = User::where('is_sitter', true)
                                    ->where('id_validation_status', 'verified')
                                    ->count();

        $pendingVerification = User::where('id_validation_status', 'pending')->count();

        // ==========================================
        // 2. SUMMARY CARDS — ROW 2
        // ==========================================
        $activeBookings      = Booking::where('status', 'accepted')->count();
        $pendingComplaints   = Complaint::whereIn('status', ['pending', 'under_review'])->count();
        $pendingClaims       = 0;   // Wala pay Claims module — i-update later
        $averageRating       = (float) (Review::avg('rating') ?? 0);
        $totalReviews        = Review::count();

        // ==========================================
        // 3. SUMMARY CARDS — ROW 3
        // ==========================================
        $cancelledBookings   = Booking::where('status', 'cancelled')->count();
        $totalBookings       = Booking::count();
        $completedBookings   = Booking::where('status', 'completed')->count();
        $newSittersThisMonth = User::where('is_sitter', true)
                                    ->where('id_validation_status', 'verified')
                                    ->whereMonth('updated_at', now()->month)
                                    ->whereYear('updated_at', now()->year)
                                    ->count();

        // ==========================================
        // 4. BOOKING STATUS DISTRIBUTION
        // ==========================================
        $statusCounts = [
            'completed' => $completedBookings,
            'pending'   => Booking::where('status', 'pending')->count(),
            'cancelled' => $cancelledBookings,
            'accepted'  => $activeBookings,
        ];

        $statusTotal = array_sum($statusCounts);

        $statusPercentages = [
            'completed'   => $statusTotal > 0 ? round(($statusCounts['completed'] / $statusTotal) * 100) : 0,
            'pending'     => $statusTotal > 0 ? round(($statusCounts['pending']   / $statusTotal) * 100) : 0,
            'cancelled'   => $statusTotal > 0 ? round(($statusCounts['cancelled'] / $statusTotal) * 100) : 0,
            'in_progress' => $statusTotal > 0 ? round(($statusCounts['accepted']  / $statusTotal) * 100) : 0,
        ];

        // ==========================================
        // 5. BOOKING TREND — last 7 months
        // ==========================================
        $monthlyTrend = [];
        $maxTrend = 1;

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $count = Booking::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $monthlyTrend[] = [
                'label' => $date->format('M'),
                'count' => $count,
            ];

            if ($count > $maxTrend) {
                $maxTrend = $count;
            }
        }

        // ==========================================
        // 6. RECENT BOOKINGS (last 3)
        // ==========================================
        $recentBookings = Booking::with(['owner', 'sitter', 'pet.petType'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        // ==========================================
        // 7. PENDING VERIFICATIONS (last 2)
        // ==========================================
        $pendingVerifications = User::where('id_validation_status', 'pending')
            ->orderByDesc('updated_at')
            ->limit(2)
            ->get();

        // ==========================================
        // 8. RECENT COMPLAINTS (last 2)
        // ==========================================
        $recentComplaints = Complaint::with(['complainant', 'respondent'])
            ->orderByDesc('created_at')
            ->limit(2)
            ->get();

        // ==========================================
        // 9. RECENT ACTIVITIES — combine multiple sources
        // ==========================================
        $activities = collect();

        // Recent complaints
        Complaint::with(['complainant', 'respondent'])
            ->latest()
            ->limit(2)
            ->get()
            ->each(function ($c) use ($activities) {
                $complainantName = trim(($c->complainant?->f_name ?? '') . ' ' . ($c->complainant?->l_name ?? '')) ?: 'User';
                $respondentName  = trim(($c->respondent?->f_name ?? '') . ' ' . ($c->respondent?->l_name ?? '')) ?: 'User';

                $activities->push([
                    'type'   => 'complaint',
                    'text'   => __('messages.activity_filed_complaint', [
                        'name'   => $complainantName,
                        'target' => $respondentName,
                    ]),
                    'time'   => $c->created_at,
                    'status' => $this->statusLabel($c->status),
                    'color'  => 'amber',
                    'icon'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                ]);
            });

        // Recent verifications
        User::whereNotNull('id_validation_status')
            ->latest('updated_at')
            ->limit(2)
            ->get()
            ->each(function ($u) use ($activities) {
                $name = trim(($u->f_name ?? '') . ' ' . ($u->l_name ?? '')) ?: 'User';

                $activities->push([
                    'type'   => 'verification',
                    'text'   => __('messages.activity_submitted_id', ['name' => $name]),
                    'time'   => $u->updated_at,
                    'status' => $this->statusLabel($u->id_validation_status ?? 'pending'),
                    'color'  => $u->id_validation_status === 'verified' ? 'green' : 'amber',
                    'icon'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                ]);
            });

        // Recent bookings
        Booking::with(['owner', 'pet'])
            ->latest()
            ->limit(2)
            ->get()
            ->each(function ($b) use ($activities) {
                $ownerName = trim(($b->owner?->f_name ?? '') . ' ' . ($b->owner?->l_name ?? '')) ?: 'Owner';
                $petName = $b->pet?->name ?? 'Pet';

                $activities->push([
                    'type'   => 'booking',
                    'text'   => __('messages.activity_booking_created', [
                        'ref'  => $b->booking_reference,
                        'pet'  => $petName,
                        'name' => $ownerName,
                    ]),
                    'time'   => $b->created_at,
                    'status' => $this->statusLabel($b->status),
                    'color'  => 'blue',
                    'icon'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                ]);
            });

        $recentActivities = $activities
            ->sortByDesc('time')
            ->take(4)
            ->values();

        // ==========================================
        // RETURN VIEW
        // ==========================================
        return view('dashboard.admin_dashboard', compact(
            'totalUsers',
            'totalOwners',
            'totalSitters',
            'pendingVerification',
            'activeBookings',
            'pendingComplaints',
            'pendingClaims',
            'averageRating',
            'totalReviews',
            'cancelledBookings',
            'totalBookings',
            'completedBookings',
            'newSittersThisMonth',
            'statusPercentages',
            'monthlyTrend',
            'maxTrend',
            'recentBookings',
            'pendingVerifications',
            'recentComplaints',
            'recentActivities'
        ));
    }

    /**
     * Translate a status keyword into a localized label.
     */
    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending'      => __('messages.status_pending'),
            'accepted'     => __('messages.status_accepted'),
            'confirmed'    => __('messages.status_confirmed'),
            'completed'    => __('messages.status_completed'),
            'cancelled'    => __('messages.status_cancelled'),
            'rejected'     => __('messages.status_rejected'),
            'verified'     => __('messages.status_verified'),
            'under_review' => __('messages.status_under_review'),
            'resolved'     => __('messages.status_resolved'),
            'dismissed'    => __('messages.status_dismissed'),
            'in_progress'  => __('messages.status_in_progress'),
            default        => ucfirst(str_replace('_', ' ', (string) $status)),
        };
    }

    /**
     * Creative Admin Homepage.
     * Bag-ong hub para sa admin — welcome section, KPIs, activity, quick access.
     */
    public function home()
    {
        // ==========================================
        // KPI STATS
        // ==========================================
        $totalUsers          = User::count();
        $totalOwners         = User::where('role', 'owner')->count();
        $totalSitters        = User::where('is_sitter', true)
                                    ->where('id_validation_status', 'verified')
                                    ->count();
        $pendingVerification = User::where('id_validation_status', 'pending')->count();
        $pendingSitterApps   = User::where('sitter_status', 'pending')
                                    ->whereNotNull('sitter_applied_at')
                                    ->count();

        $totalBookings       = Booking::count();
        $activeBookings      = Booking::where('status', 'accepted')->count();

        $pendingComplaints   = Complaint::whereIn('status', ['pending', 'under_review'])->count();

        $openMessages        = class_exists(\App\Models\UserSupportMessage::class)
                                ? \App\Models\UserSupportMessage::where('status', 'open')->count()
                                : 0;

        $stats = [
            'totalUsers'          => $totalUsers,
            'totalOwners'         => $totalOwners,
            'totalSitters'        => $totalSitters,
            'pendingVerification' => $pendingVerification,
            'pendingSitterApps'   => $pendingSitterApps,
            'totalBookings'       => $totalBookings,
            'activeBookings'      => $activeBookings,
            'pendingComplaints'   => $pendingComplaints,
            'openMessages'        => $openMessages,
        ];

        // ==========================================
        // RECENT ACTIVITIES (last 5)
        // ==========================================
        $recentActivities = collect();

        // Recent verifications
        User::whereNotNull('id_validation_status')
            ->latest('updated_at')
            ->limit(2)
            ->get()
            ->each(function ($u) use ($recentActivities) {
                $name = trim(($u->f_name ?? '') . ' ' . ($u->l_name ?? '')) ?: 'User';
                $recentActivities->push([
                    'text'       => __('messages.activity_submitted_id', ['name' => $name]),
                    'time'       => $u->updated_at?->diffForHumans(),
                    'icon'       => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'icon_bg'    => $u->id_validation_status === 'verified' ? 'bg-green-100/30' : 'bg-amber-100/30',
                    'icon_color' => $u->id_validation_status === 'verified' ? 'text-green-600' : 'text-amber-600',
                ]);
            });

        // Recent complaints
        Complaint::with('complainant')
            ->latest()
            ->limit(2)
            ->get()
            ->each(function ($c) use ($recentActivities) {
                $name = trim(($c->complainant?->f_name ?? '') . ' ' . ($c->complainant?->l_name ?? '')) ?: 'User';
                $recentActivities->push([
                    'text'       => __('messages.ah_activity_complaint', ['name' => $name]),
                    'time'       => $c->created_at?->diffForHumans(),
                    'icon'       => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    'icon_bg'    => 'bg-red-100/30',
                    'icon_color' => 'text-red-600',
                ]);
            });

        // Recent bookings
        Booking::with(['owner', 'pet'])
            ->latest()
            ->limit(1)
            ->get()
            ->each(function ($b) use ($recentActivities) {
                $ownerName = trim(($b->owner?->f_name ?? '') . ' ' . ($b->owner?->l_name ?? '')) ?: 'Owner';
                $petName   = $b->pet?->name ?? 'Pet';
                $recentActivities->push([
                    'text'       => __('messages.activity_booking_created', [
                        'ref'  => $b->booking_reference ?? $b->id,
                        'pet'  => $petName,
                        'name' => $ownerName,
                    ]),
                    'time'       => $b->created_at?->diffForHumans(),
                    'icon'       => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'icon_bg'    => 'bg-blue-100/30',
                    'icon_color' => 'text-blue-600',
                ]);
            });

        $recentActivities = $recentActivities->sortByDesc('time')->take(5)->values();

        return view('admin.home', compact('stats', 'recentActivities'));
    }
}