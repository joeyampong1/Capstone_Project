<?php

namespace App\Http\Controllers\Sitter;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Visit;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TaskController extends Controller
{
    // ==========================================
    // INDEX — List tanan visits sa sitter
    // ==========================================

    public function index(Request $request)
    {
        $user   = Auth::user();
        $filter = $request->query('filter', 'all');

        // Get BOOKINGS (not visits) — with visits loaded
        $bookingsQuery = Booking::with(['owner', 'pet.petType', 'visits'])
            ->where('sitter_id', $user->id)
            ->whereIn('status', ['accepted', 'completed']);

        // Filter by booking status
        if ($filter === 'pending') {
            // Bookings nga naay pending visits
            $bookingsQuery->whereHas('visits', fn($q) => $q->where('status', 'pending'));
        } elseif ($filter === 'in_progress') {
            $bookingsQuery->whereHas('visits', fn($q) => $q->where('status', 'in_progress'));
        } elseif ($filter === 'completed') {
            // Bookings nga tanan visits completed na
            $bookingsQuery->whereDoesntHave('visits', fn($q) =>
                $q->whereIn('status', ['pending', 'in_progress'])
            );
        } elseif ($filter === 'missed') {
            $bookingsQuery->whereHas('visits', fn($q) => $q->where('status', 'missed'));
        }

        $bookings = $bookingsQuery->orderByDesc('created_at')->get();

        // Stats (base sa visits)
        $allVisits = Visit::whereHas('booking', function ($q) use ($user) {
            $q->where('sitter_id', $user->id)
            ->whereIn('status', ['accepted', 'completed']);
        })->get();

        $stats = [
            'total'       => $allVisits->count(),
            'pending'     => $allVisits->where('status', 'pending')->count(),
            'in_progress' => $allVisits->where('status', 'in_progress')->count(),
            'completed'   => $allVisits->where('status', 'completed')->count(),
            'missed'      => $allVisits->where('status', 'missed')->count(),
        ];

        return view('sitters.sitter_task_index', compact('bookings', 'filter', 'stats'));
    }

    // ==========================================
    // SHOW — View single visit details
    // ==========================================

    public function show(Booking $booking)
    {
        // Verify sitter
        abort_if(
            $booking->sitter_id !== Auth::id(),
            403,
            'Unauthorized — this is not your booking.'
        );

        abort_if(
            !in_array($booking->status, ['accepted', 'completed']),
            400,
            'This booking is not active.'
        );

        $booking->load([
            'owner',
            'pet.petType',
            'visits' => fn($q) => $q->orderBy('scheduled_datetime'),
        ]);

        return view('sitters.sitter_booking_visits', compact('booking'));
    }

    // ==========================================
    // CHECK IN — Sitter arrives sa location
    // ==========================================

    public function checkIn(Visit $visit)
    {
        $this->authorizeVisit($visit);

        abort_if($visit->status !== 'pending', 400, 'This visit is not pending.');
        abort_if($visit->check_in !== null, 400, 'You have already checked in.');

        // Compute lateness
        $scheduled = Carbon::parse($visit->scheduled_datetime);
        $now       = now();
        $lateness  = 0;

        // Consider "on time" kung within 15 minutes before/after scheduled
        $graceMinutes = 15;
        if ($now->gt($scheduled->copy()->addMinutes($graceMinutes))) {
            $lateness = $now->diffInMinutes($scheduled);
        }

        // Compute deduction (e.g., 1% per late minute, max 50%)
        $deductionPercent = min($lateness * 1, 50);

        $visit->update([
            'check_in'                  => $now,
            'status'                    => 'in_progress',
            'lateness_minutes'          => $lateness,
            'late_deduction_percentage' => $deductionPercent,
        ]);

        return back()->with('status', 'Checked in successfully. Have a great visit!');
    }

    // ==========================================
    // CHECK OUT — Sitter leaves
    // ==========================================

    public function checkOut(Visit $visit)
    {
        $this->authorizeVisit($visit);

        abort_if($visit->status !== 'in_progress', 400, 'You must check in first.');
        abort_if($visit->check_out !== null, 400, 'You have already checked out.');

        $checkIn  = Carbon::parse($visit->check_in);
        $checkOut = now();
        $duration = $checkIn->diffInMinutes($checkOut);

        $visit->update([
            'check_out'        => $checkOut,
            'duration_minutes' => $duration,
            // NOTE: dili pa i-mark as completed — sitter mo-click pa og "Complete"
        ]);

        return back()->with('status', 'Checked out. Review your tasks then click Complete.');
    }

    // ==========================================
    // COMPLETE — Mark visit as completed
    // ==========================================

    public function complete(Visit $visit)
    {
        $this->authorizeVisit($visit);

        abort_if(
            !in_array($visit->status, ['in_progress', 'pending']),
            400,
            'Only pending or in-progress visits can be completed.'
        );

        abort_if($visit->check_out === null && $visit->status === 'in_progress',
            400,
            'You must check out first before completing.'
        );

        $visit->update([
            'status' => 'completed',
        ]);

        // Notify owner
        NotificationService::send(
            userId:    $visit->booking->owner_id,
            type:      'visit_completed',
            title:     'Visit completed',
            message:   "Visit #{$visit->visit_number} for {$visit->booking->pet->name} has been completed.",
            actionUrl: route('task.monitor', $visit->booking_id),
            actorId:   Auth::id(),
            meta:      ['visit_id' => $visit->id, 'booking_id' => $visit->booking_id],
        );

        // Auto-complete booking kung tanan visits done na
        $this->checkBookingCompletion($visit->booking);

        return back()->with('status', 'Visit marked as completed!');
    }

    // ==========================================
    // UPDATE TASKS — Mark tasks as completed
    // ==========================================

    public function updateTasks(Request $request, Visit $visit)
    {
        $this->authorizeVisit($visit);

        $completedTasks = $request->input('completed_tasks', []);

        if (!is_array($completedTasks)) {
            $completedTasks = [];
        }

        $visit->update([
            'completed_tasks' => array_values($completedTasks),
        ]);

        return back()->with('status', 'Tasks updated.');
    }

    // ==========================================
    // UPLOAD PHOTO — Proof of visit
    // ==========================================

    public function uploadPhoto(Request $request, Visit $visit)
    {
        $this->authorizeVisit($visit);

        $request->validate([
            'photos'   => 'required|array|min:1|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $existingPaths = $visit->photo_proof_paths ?? [];

        if (!is_array($existingPaths)) {
            $existingPaths = [];
        }

        $newPaths = [];
        foreach ($request->file('photos') as $photo) {
            $newPaths[] = $photo->store('visits/photos', 'public');
        }

        $allPaths = array_merge($existingPaths, $newPaths);

        $visit->update([
            'photo_proof_paths' => $allPaths,
            'photo_timestamp'   => now(),
        ]);

        $count = count($newPaths);

        return back()->with('status', "{$count} photo(s) uploaded successfully!");
    }

    // ==========================================
    // ADD NOTE — Sitter notes during visit
    // ==========================================

    public function addNote(Request $request, Visit $visit)
    {
        $this->authorizeVisit($visit);

        $validated = $request->validate([
            'notes' => 'required|string|max:2000',
        ]);

        $visit->update([
            'notes' => $validated['notes'],
        ]);

        return back()->with('status', 'Note added.');
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    /**
     * Siguraduon nga ang visit kay iya sa currently logged-in sitter.
     */
    protected function authorizeVisit(Visit $visit): void
    {
        $visit->loadMissing('booking');

        abort_if(
            $visit->booking->sitter_id !== Auth::id(),
            403,
            'Unauthorized — this is not your visit.'
        );

        abort_if(
            !in_array($visit->booking->status, ['accepted', 'completed']),
            400,
            'This booking is not active.'
        );
    }

    /**
     * Kung tanan visits sa booking kay completed na,
     * i-mark as 'completed' ang booking automatically.
     */
    protected function checkBookingCompletion(Booking $booking): void
    {
        $remaining = $booking->visits()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        if ($remaining === 0 && $booking->status === 'accepted') {
            $booking->update(['status' => 'completed']);

            NotificationService::bookingCompleted(
                sitter:  Auth::user(),
                owner:   $booking->owner,
                booking: $booking
            );
        }
    }

    public function visitDetail(Visit $visit)
    {
        $this->authorizeVisit($visit);

        $visit->load(['booking.owner', 'booking.pet.petType', 'booking.sitter']);

        return view('sitters.task_to-do', compact('visit'));
    }

    // ==========================================
    // DELETE PHOTO — Remove single photo
    // ==========================================

    public function deletePhoto(Request $request, Visit $visit)
    {
        $this->authorizeVisit($visit);

        $validated = $request->validate([
            'path' => 'required|string',
        ]);

        $existingPaths = $visit->photo_proof_paths ?? [];

        if (!is_array($existingPaths)) {
            $existingPaths = [];
        }

        if (!in_array($validated['path'], $existingPaths)) {
            return back()->with('error', 'Photo not found.');
        }

        Storage::disk('public')->delete($validated['path']);

        $updatedPaths = array_values(
            array_filter($existingPaths, fn($p) => $p !== $validated['path'])
        );

        $visit->update([
            'photo_proof_paths' => $updatedPaths,
        ]);

        return back()->with('status', 'Photo deleted successfully.');
    }

}