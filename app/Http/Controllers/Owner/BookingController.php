<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pet;
use App\Models\User;
use App\Models\Visit;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ==========================================
    // INDEX — My Bookings (Owner + Sitter view)
    // ==========================================

    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'all');

        $canSwitchToSitter = $user->is_sitter
            && $user->id_validation_status === 'verified'
            && $user->sitterProfile !== null;

        $baseQuery = Booking::with(['sitter.sitterProfile', 'owner', 'pet.petType'])
            ->where(function ($q) use ($user, $canSwitchToSitter) {
                $q->where('owner_id', $user->id);
                if ($canSwitchToSitter) {
                    $q->orWhere('sitter_id', $user->id);
                }
            })
            ->orderByDesc('created_at');

        $allBookings = (clone $baseQuery)->get();

        $bookings = $filter === 'all'
            ? $allBookings
            : $allBookings->where('status', $filter);

        $ownerBookings  = $allBookings->where('owner_id', $user->id);
        $sitterBookings = $allBookings->where('sitter_id', $user->id);

        $stats = [
            'owner_total'     => $ownerBookings->count(),
            'owner_pending'   => $ownerBookings->where('status', 'pending')->count(),
            'owner_accepted'  => $ownerBookings->where('status', 'accepted')->count(),
            'owner_completed' => $ownerBookings->where('status', 'completed')->count(),
            'sitter_pending'  => $sitterBookings->where('status', 'pending')->count(),
            'sitter_total'    => $sitterBookings->count(),
        ];

        return view('mybookings.mybookings', compact(
            'bookings',
            'filter',
            'stats',
            'canSwitchToSitter'
        ));
    }

    // ==========================================
    // CREATE — Show booking form
    // ==========================================

    public function create(Request $request)
    {
        $sitterId = (int) $request->query('sitter_id');

        if (!$sitterId) {
            return redirect()->route('find.sitter')
                ->with('error', 'Please select a sitter first.');
        }

        $sitter = User::with('sitterProfile')->findOrFail($sitterId);

        // ✅ FIX ISSUE #21: Check both is_sitter and sitter_status
        if (!$sitter->is_sitter || $sitter->sitter_status !== 'approved') {
            return redirect()->route('find.sitter')
                ->with('error', 'Selected user is not an approved pet sitter.');
        }

        if ($sitter->id === Auth::id()) {
            return redirect()->route('find.sitter')
                ->with('error', 'You cannot book yourself.');
        }

        if ($sitter->id_validation_status !== 'verified') {
            return redirect()->route('owner.sitter.profile', ['id' => $sitter->id])
                ->with('error', 'This sitter has not been verified by the admin yet.');
        }

        $pets = Pet::where('user_id', Auth::id())->with('petType')->get();

        if ($pets->isEmpty()) {
            return redirect()->route('mypets.create')
                ->with('error', 'Please add a pet first before booking.');
        }

        return view('mybookings.create_booking', compact('sitter', 'pets'));
    }

    // ==========================================
    // STORE — Save new booking
    // ==========================================

    public function store(Request $request)
    {

        $validated = $request->validate([
            'sitter_id'        => 'required|exists:users,id',
            'pet_id'           => 'required|exists:pets,id',
            'start_date'       => 'required|date|after_or_equal:today',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'visit_per_day'    => 'required|integer|min:1|max:20',
            'schedule_times'   => 'required|array|min:1',
            'schedule_times.*' => 'required|date_format:H:i',
            'food_preference'  => 'required|in:owner_provides,sitter_provides,flexible',
            'food_budget'      => 'nullable|numeric|min:0|max:10000',
            'tasks'            => 'nullable|array',
            'tasks.*'          => 'string|max:255',
            'instructions'     => 'nullable|string|max:2000',
        ]);

        // ✅ FIX ISSUE #27: Limit booking duration to a maximum of 90 days
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate   = \Carbon\Carbon::parse($validated['end_date']);

        if ($startDate->diffInDays($endDate) > 90) {
            return back()
                ->withErrors(['end_date' => 'The booking duration cannot exceed 90 days.'])
                ->withInput();
        }

        // Validate: count sa schedule_times dapat equal sa visit_per_day
        if (count($validated['schedule_times']) !== (int) $validated['visit_per_day']) {
            return back()
                ->withErrors(['schedule_times' => 'Number of times must match visits per day.'])
                ->withInput();
        }

        $owner  = Auth::user();
        $sitter = User::with('sitterProfile')->findOrFail($validated['sitter_id']);

        // ✅ FIX ISSUE #21: Check both is_sitter and sitter_status
        if (!$sitter->is_sitter || $sitter->sitter_status !== 'approved') {
            return back()->withErrors(['sitter_id' => 'Selected user is not an approved pet sitter.'])->withInput();
        }

        if ($sitter->id === $owner->id) {
            return back()->withErrors(['sitter_id' => 'You cannot book yourself.'])->withInput();
        }

        if ($sitter->id_validation_status !== 'verified') {
            return back()->withErrors(['sitter_id' => 'This sitter has not been verified by the admin yet.'])->withInput();
        }

        // Verify pet belongs to owner
        $pet = Pet::where('id', $validated['pet_id'])
            ->where('user_id', $owner->id)
            ->firstOrFail();

        // Sitter rate
        $profile = $sitter->sitterProfile;
        if (!$profile) {
            return back()->withErrors(['sitter_id' => 'Sitter has no profile.'])->withInput();
        }

        // ==========================================
        // ✅ FIX ISSUE #28: Check Sitter Availability & Time Overlap
        // ==========================================

        // 1. Check if the sitter has marked themselves as unavailable on these dates
        $hasUnavailableDates = \App\Models\Availability::where('sitter_id', $sitter->id)
            ->where('is_available', 0)
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
            ->exists();

        if ($hasUnavailableDates) {
            return back()
                ->withErrors(['start_date' => 'The sitter is not available on the selected date(s). Please choose different dates.'])
                ->withInput();
        }

        // 2. Check for exact Time Overlaps
        $existingBookings = Booking::where('sitter_id', $sitter->id)
            ->where('status', 'accepted')
            ->where(function ($q) use ($validated) {
                // Check if the date ranges overlap first
                $q->where('start_date', '<=', $validated['end_date'])
                  ->where('end_date', '>=', $validated['start_date']);
            })
            ->get();

        $hasTimeConflict = false;
        $newTimes = $validated['schedule_times'];

        foreach ($existingBookings as $existing) {
            $existingTimes = $existing->schedule_times ?? [];

            foreach ($newTimes as $newTime) {
                $newTimeStart = Carbon::parse($newTime);
                $newTimeEnd = $newTimeStart->copy()->addHour(); // Assuming 1-hour duration per visit

                foreach ($existingTimes as $exTime) {
                    $exTimeStart = Carbon::parse($exTime);
                    $exTimeEnd = $exTimeStart->copy()->addHour();

                    // ✅ STRICT OVERLAP: 9-10 and 10-11 will NOT overlap
                    if ($newTimeStart->lt($exTimeEnd) && $newTimeEnd->gt($exTimeStart)) {
                        $hasTimeConflict = true;
                        break 3; // Break out of all loops
                    }
                }
            }
        }

        if ($hasTimeConflict) {
            return back()
                ->withErrors(['start_date' => 'This sitter already has a confirmed booking at this exact time. Please choose a different schedule.'])
                ->withInput();
        }

        // ==========================================
        // FOOD PREFERENCE (from owner input, not profile)
        // ==========================================
        $baseRate       = (float) ($profile->base_rate ?? 350);
        $foodPreference = $validated['food_preference'];
        $foodBudget     = $foodPreference === 'sitter_provides'
            ? (float) ($validated['food_budget'] ?? 0)
            : 0;

        // Calculate pricing
        $pricing = Booking::calculatePricing(
            startDate:       $validated['start_date'],
            endDate:         $validated['end_date'],
            visitsPerDay:    (int) $validated['visit_per_day'],
            baseRate:        $baseRate,
            foodPreference:  $foodPreference,
            foodBudget:      $foodBudget,
        );

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'booking_reference' => Booking::generateReference(),
                'owner_id'          => $owner->id,
                'sitter_id'         => $sitter->id,
                'pet_id'            => $pet->id,
                'start_date'        => $validated['start_date'],
                'end_date'          => $validated['end_date'],
                'visit_per_day'     => $validated['visit_per_day'],
                'total_visits'      => $pricing['total_visits'],
                'schedule_times'    => $validated['schedule_times'],
                'tasks'             => $validated['tasks'] ?? [],
                'instructions'      => $validated['instructions'] ?? null,
                'base_rate'         => $baseRate,
                'food_preference'   => $foodPreference,
                'food_budget'       => $pricing['food_budget'],
                'subtotal'          => $pricing['subtotal'],
                'total_amount'      => $pricing['total_amount'],
                'sitter_earnings'   => $pricing['sitter_earnings'],
                'status'            => 'pending',
            ]);

            NotificationService::bookingCreated($owner, $sitter, $booking);

            DB::commit();

            return redirect()->route('mybookings.index')
                ->with('status', 'Booking submitted! Waiting for sitter confirmation.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ==========================================
    // SHOW — View booking details
    // ==========================================

    public function show(Booking $booking)
    {
        $userId = Auth::id();
        abort_if(
            $booking->owner_id !== $userId && $booking->sitter_id !== $userId,
            403,
            'Unauthorized'
        );

        $booking->load(['owner', 'sitter.sitterProfile', 'pet.petType', 'visits']);

        return view('mybookings.show_booking', compact('booking'));
    }

    // ==========================================
    // ACCEPT — Sitter accepts booking + auto-generate visits
    // ==========================================

    public function accept(Booking $booking)
    {
        abort_if($booking->sitter_id !== Auth::id(), 403, 'Only the assigned sitter can accept this booking.');

        // ✅ FIX ISSUE #30: Prevent accepting expired bookings
        abort_if(
            now()->gte($booking->start_date),
            400,
            'This booking can no longer be accepted because the start date has already passed.'
        );

        abort_if(!$booking->isPending(), 400, 'Only pending bookings can be accepted.');

        abort_if(
            Auth::user()->id_validation_status !== 'verified',
            403,
            'You must be a verified sitter before you can accept bookings.'
        );

        // Re-check overlap before accept
        $overlap = Booking::where('sitter_id', Auth::id())
            ->where('status', 'accepted')
            ->where('id', '!=', $booking->id)
            ->where(function ($q) use ($booking) {
                $q->whereBetween('start_date', [$booking->start_date, $booking->end_date])
                  ->orWhereBetween('end_date', [$booking->start_date, $booking->end_date])
                  ->orWhere(function ($q2) use ($booking) {
                      $q2->where('start_date', '<=', $booking->start_date)
                         ->where('end_date', '>=', $booking->end_date);
                  });
            })
            ->exists();

        abort_if($overlap, 400, 'There is a conflict with an existing accepted booking.');

        DB::beginTransaction();
        try {
            // ✅ FIX ISSUE #30: Lock the row to prevent double click (Race Condition)
            $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();

            // Double-check status AFTER locking
            if ($lockedBooking->status !== 'pending') {
                DB::rollBack();
                return back()->with('error', 'This booking has already been processed by another request.');
            }

            $lockedBooking->update(['status' => 'accepted']);

            // Auto-generate visit records
            $this->generateVisits($lockedBooking);

            NotificationService::bookingConfirmed(
                sitter:  Auth::user(),
                owner:   $lockedBooking->owner,
                booking: $lockedBooking
            );

            DB::commit();

            return back()->with('status', 'Booking accepted! Visits have been scheduled.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to accept booking: ' . $e->getMessage());
        }
    }

    // ==========================================
    // REJECT — Sitter rejects booking
    // ==========================================

    public function reject(Request $request, Booking $booking)
    {
        abort_if($booking->sitter_id !== Auth::id(), 403, 'Only the assigned sitter can reject this booking.');
        abort_if(!$booking->isPending(), 400, 'Only pending bookings can be rejected.');

        $reason = $request->input('reason', 'Sitter is unavailable for the selected dates.');

        $booking->update([
            'status'              => 'rejected',
            'cancellation_reason' => $reason,
            'cancelled_by'        => 'sitter',
        ]);

        NotificationService::bookingCancelled(
            canceller: Auth::user(),
            recipient: $booking->owner,
            booking:   $booking
        );

        return back()->with('status', 'Booking rejected. The owner has been notified.');
    }

    // ==========================================
    // CANCEL — Owner cancels booking
    // ==========================================

    public function cancel(Request $request, Booking $booking)
    {
        $user = Auth::user();
        $isOwner = $booking->owner_id === $user->id;
        $isSitter = $booking->sitter_id === $user->id;

        // ✅ FIX ISSUE #30: Allow both Owner and Sitter to cancel
        abort_if(!$isOwner && !$isSitter, 403, 'Only the assigned owner or sitter can cancel this booking.');

        abort_if($booking->isCancelled(), 400, 'Booking is already cancelled.');
        abort_if($booking->isCompleted(), 400, 'Completed bookings cannot be cancelled.');
        abort_if($booking->status === 'rejected', 400, 'This booking has already been rejected.');

        // Owner rule: Cannot cancel if less than 24 hours before start
        if ($isOwner) {
            abort_if(
                now()->addDay()->gte($booking->start_date),
                400,
                'This booking can no longer be cancelled — less than 24 hours remain before the start date.'
            );
        }

        $reason = $request->input('reason', 'Cancelled by ' . ($isOwner ? 'owner' : 'sitter') . '.');
        $cancelledBy = $isOwner ? 'owner' : 'sitter';

        DB::beginTransaction();
        try {
            $booking->update([
                'status'              => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_by'        => $cancelledBy,
            ]);

            // Delete pending visits
            $booking->visits()
                ->where('status', 'pending')
                ->delete();

            // Notify the other party
            $recipient = $isOwner ? $booking->sitter : $booking->owner;

            NotificationService::bookingCancelled(
                canceller: $user,
                recipient: $recipient,
                booking:   $booking
            );

            DB::commit();

            return back()->with('status', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel: ' . $e->getMessage());
        }
    }

    // ==========================================
    // COMPLETE — Mark as completed (Sitter)
    // ==========================================

    public function complete(Booking $booking)
    {
        abort_if($booking->sitter_id !== Auth::id(), 403, 'Only the assigned sitter can complete this booking.');
        abort_if(!$booking->isAccepted(), 400, 'Only accepted bookings can be completed.');

        abort_if(
            now()->lt($booking->end_date),
            400,
            'This booking cannot be completed yet — the booking period has not ended.'
        );

        // Check if all visits are completed
        $pendingVisits = $booking->visits()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        if ($pendingVisits > 0) {
            return back()->with('error', "There are still {$pendingVisits} unfinished visit(s). Please complete all visits before marking the booking as completed.");
        }

        $booking->update(['status' => 'completed']);

        NotificationService::bookingCompleted(
            sitter:  Auth::user(),
            owner:   $booking->owner,
            booking: $booking
        );

        return back()->with('status', 'Booking marked as completed!');
    }

    // ==========================================
    // HELPER — Generate visit records
    // ==========================================

    /**
     * Auto-generate visit records inig-accept sa booking.
     *
     * Creates one Visit record per day per schedule time.
     * Example: Sept 15-17, 2 visits/day = 6 visit records.
     */
    protected function generateVisits(Booking $booking): void
    {
        // ✅ FIX ISSUE #27: Safety check to prevent memory exhaustion
        // Calculate total days based on start and end date
        $daysCount = \Carbon\Carbon::parse($booking->start_date)->diffInDays(\Carbon\Carbon::parse($booking->end_date));

        // If duration exceeds 90 days, abort immediately
        if ($daysCount > 90) {
            throw new \Exception('Cannot generate visits: Booking duration exceeds the maximum limit of 90 days.');
        }

        // Skip if there are already existing visits (to prevent duplicates)
        if ($booking->visits()->exists()) {
            return;
        }

        $start = Carbon::parse($booking->start_date);
        $end   = Carbon::parse($booking->end_date);
        $times = $booking->schedule_times ?? [];

        if (empty($times)) {
            throw new \Exception('No schedule times found — cannot generate visits.');
        }

        $visitNumber = 0;

        // Loop through each day
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            // Loop through each time slot
            foreach ($times as $time) {
                $visitNumber++;

                $scheduledAt = Carbon::parse(
                    $date->format('Y-m-d') . ' ' . $time
                );

                Visit::create([
                    'booking_id'                => $booking->id,
                    'visit_number'              => $visitNumber,
                    'scheduled_datetime'        => $scheduledAt,
                    'status'                    => 'pending',
                    'lateness_minutes'          => 0,
                    'late_deduction_percentage' => 0,
                ]);
            }
        }
    }

    // ==========================================
    // MARK AS PAID — Owner claims they paid the sitter
    // ==========================================
    public function markPaid(Request $request, Booking $booking)
    {
        // Ensure the logged-in user is the owner of this booking
        abort_if($booking->owner_id !== Auth::id(), 403, 'Unauthorized.');

        // Only allow marking as paid if the booking is completed
        if ($booking->status !== 'completed') {
            return back()->with('error', 'You can only mark a completed booking as paid.');
        }

        // Ensure it's not already paid or pending confirmation
        if (in_array($booking->payment_status, ['paid', 'pending'])) {
            return back()->with('error', 'Payment status is already updated.');
        }

        // ✅ Set to 'pending' to wait for Sitter's confirmation
        $booking->update([
            'payment_status' => 'pending',
        ]);

        return back()->with('status', 'Payment claimed. Waiting for the sitter to confirm receipt.');
    }
}
