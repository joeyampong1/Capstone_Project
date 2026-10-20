<?php

namespace App\Http\Controllers\Sitter;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Visit;
use App\Services\CloudinaryService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function __construct(
        private CloudinaryService $cloudinary
    ) {}

    // ==========================================
    // INDEX — List tanan visits sa sitter
    // ==========================================

    public function index(Request $request)
    {
        $user   = Auth::user();
        $filter = $request->query('filter', 'all');

        $bookingsQuery = Booking::with(['owner', 'pet.petType', 'visits'])
            ->where('sitter_id', $user->id)
            ->whereIn('status', ['accepted', 'completed']);

        if ($filter === 'pending') {
            $bookingsQuery->whereHas('visits', fn($q) => $q->where('status', 'pending'));
        } elseif ($filter === 'in_progress') {
            $bookingsQuery->whereHas('visits', fn($q) => $q->where('status', 'in_progress'));
        } elseif ($filter === 'completed') {
            $bookingsQuery->whereDoesntHave('visits', fn($q) =>
                $q->whereIn('status', ['pending', 'in_progress'])
            );
        } elseif ($filter === 'missed') {
            $bookingsQuery->whereHas('visits', fn($q) => $q->where('status', 'missed'));
        }

        $bookings = $bookingsQuery->orderByDesc('created_at')->get();

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
    // SHOW — View single booking details
    // ==========================================

    public function show(Booking $booking)
    {
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

        $scheduled = Carbon::parse($visit->scheduled_datetime);
        $now       = now();
        $lateness  = 0;

        $graceMinutes = 15;
        if ($now->gt($scheduled->copy()->addMinutes($graceMinutes))) {
            $lateness = $now->diffInMinutes($scheduled);
        }

        $deductionPercent = min($lateness * 1, 50);

        $visit->update([
            'check_in'                  => $now,
            'status'                    => 'in_progress',
            'lateness_minutes'          => $lateness,
            'late_deduction_percentage' => $deductionPercent,
        ]);

        NotificationService::visitCheckIn(
            sitter: Auth::user(),
            owner: $visit->booking->owner,
            visit: $visit
        );

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
        ]);

        NotificationService::visitCheckOut(
            sitter: Auth::user(),
            owner: $visit->booking->owner,
            visit: $visit
        );

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

        $visit->update(['status' => 'completed']);

        // NotificationService::send(
        //     userId:    $visit->booking->owner_id,
        //     type:      'visit_completed',
        //     title:     'Visit completed',
        //     message:   "Visit #{$visit->visit_number} for {$visit->booking->pet->name} has been completed.",
        //     actionUrl: route('task.monitor', $visit->booking_id),
        //     actorId:   Auth::id(),
        //     meta:      ['visit_id' => $visit->id, 'booking_id' => $visit->booking_id],

        // );

        NotificationService::visitCompleted(
            sitter: Auth::user(),
            owner: $visit->booking->owner,
            visit: $visit

        );

        $this->checkBookingCompletion($visit->booking);

        return back()->with('status', 'Visit marked as completed!');
    }

    // ==========================================
    // UPDATE TASKS
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
    // UPLOAD PHOTO — Proof of visit (Cloudinary)
    // ==========================================

    public function uploadPhoto(Request $request, Visit $visit)
    {
        $this->authorizeVisit($visit);

        $request->validate([
            'photos'   => 'required|array|min:1|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $existingPaths     = $visit->photo_proof_paths ?? [];
        $existingPublicIds = $visit->photo_proof_public_ids ?? [];

        if (!is_array($existingPaths))     $existingPaths = [];
        if (!is_array($existingPublicIds)) $existingPublicIds = [];

        // Upload new photos to Cloudinary
        $newPaths     = [];
        $newPublicIds = [];

        foreach ($request->file('photos') as $file) {
            $upload = $this->cloudinary->upload($file, 'visit-photos', [
                'transformation' => [
                    'width'  => 1200,
                    'height' => 1200,
                    'crop'   => 'limit',
                ],
            ]);

            if ($upload['success']) {
                $newPaths[]     = $upload['url'];
                $newPublicIds[] = $upload['public_id'];
            } else {
                \Log::error('Visit photo upload failed', [
                    'visit_id' => $visit->id,
                    'error'    => $upload['error'],
                ]);
            }
        }

        if (empty($newPaths)) {
            return back()->withErrors(['photos' => 'Upload failed. Please try again.']);
        }

        $visit->update([
            'photo_proof_paths'      => array_merge($existingPaths, $newPaths),
            'photo_proof_public_ids' => array_merge($existingPublicIds, $newPublicIds),
            'photo_timestamp'        => now(),
        ]);

        $count = count($newPaths);

        return back()->with('status', "{$count} photo(s) uploaded successfully!");
    }

    // ==========================================
    // ADD NOTE
    // ==========================================

    public function addNote(Request $request, Visit $visit)
    {
        $this->authorizeVisit($visit);

        $validated = $request->validate([
            'notes' => 'required|string|max:2000',
        ]);

        $visit->update(['notes' => $validated['notes']]);

        return back()->with('status', 'Note added.');
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

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
            'index' => 'required|integer|min:0',
        ]);

        $index = (int) $validated['index'];

        $existingPaths     = $visit->photo_proof_paths ?? [];
        $existingPublicIds = $visit->photo_proof_public_ids ?? [];

        if (!is_array($existingPaths))     $existingPaths = [];
        if (!is_array($existingPublicIds)) $existingPublicIds = [];

        if (!isset($existingPaths[$index])) {
            return back()->with('error', 'Photo not found.');
        }

        $path     = $existingPaths[$index];
        $publicId = $existingPublicIds[$index] ?? null;

        // Delete from Cloudinary (o local)
        if (str_starts_with($path, 'http') && $publicId) {
            $this->cloudinary->delete($publicId);
        } elseif (!str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // Remove from arrays
        unset($existingPaths[$index]);
        unset($existingPublicIds[$index]);

        $visit->update([
            'photo_proof_paths'      => array_values($existingPaths),
            'photo_proof_public_ids' => array_values($existingPublicIds),
        ]);

        return back()->with('status', 'Photo deleted successfully.');
    }
}
