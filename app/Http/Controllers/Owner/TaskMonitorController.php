<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Visit;

class TaskMonitorController extends Controller
{
    // ==========================================
    // INDEX — List owner's bookings with visit progress
    // ==========================================

    public function index(Request $request)
    {
        $user = Auth::user();

        // Bookings nga accepted or completed ra — kay kadtong pending wala pay visits
        $bookings = Booking::with([
                'sitter.sitterProfile',
                'pet.petType',
                'visits',
            ])
            ->where('owner_id', $user->id)
            ->whereIn('status', ['accepted', 'completed'])
            ->orderByDesc('created_at')
            ->get();

        return view('tasks.owner_task_index', compact('bookings'));
    }

    // ==========================================
    // SHOW — Single booking with full visit history
    // ==========================================

    public function show(Booking $booking)
    {
        // Security: owner ra sa booking ang makakita
        abort_if(
            $booking->owner_id !== Auth::id(),
            403,
            'Unauthorized — this is not your booking.'
        );

        $booking->load([
            'sitter.sitterProfile',
            'pet.petType',
            'visits' => fn($q) => $q->orderByDesc('scheduled_datetime'),
        ]);

        return view('tasks.task_monitoring', compact('booking'));
    }

    public function visitDetail(Visit $visit)
    {
        // Security: owner ra sa booking ang makakita
        abort_if(
            $visit->booking->owner_id !== Auth::id(),
            403,
            'Unauthorized — this is not your booking.'
        );

        $visit->load(['booking.owner', 'booking.pet.petType', 'booking.sitter']);

        return view('tasks.task_monitor_visit', compact('visit'));
    }

}