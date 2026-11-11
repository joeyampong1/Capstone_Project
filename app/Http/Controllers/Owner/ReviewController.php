<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    // ==========================================
    // STORE — Submit a review for a completed booking
    // ==========================================

    public function store(Request $request, Booking $booking)
    {
        // ==========================================
        // SECURITY CHECKS
        // ==========================================

        abort_if(
            $booking->owner_id !== Auth::id(),
            403,
            'Unauthorized — this is not your booking.'
        );

        abort_if(
            $booking->status !== 'completed',
            400,
            'You can only review completed bookings.'
        );

        abort_if(
            $booking->review()->exists(),
            400,
            'You have already reviewed this booking.'
        );

        $booking->loadMissing('sitter.sitterProfile');
        $sitterProfile = $booking->sitter?->sitterProfile;

        abort_if(
            !$sitterProfile,
            400,
            'Sitter has no profile — cannot submit review.'
        );

        // ==========================================
        // VALIDATION
        // ==========================================

        $validated = $request->validate([
            'rating'   => 'required|numeric|min:1|max:5|multiple_of:0.5',
            'comments' => 'required|string|max:2000',
        ]);

        // ==========================================
        // SAVE REVIEW
        // ==========================================

        DB::beginTransaction();
        try {
            $review = Review::create([
                'sitter_id'  => $sitterProfile->id,
                'user_id'    => Auth::id(),
                'booking_id' => $booking->id,
                'rating'     => $validated['rating'],
                'comments'   => $validated['comments'],
            ]);

            // Recalculate sitter's average rating
            $this->recalculateAverageRating($sitterProfile);

            // ==========================================
            // NOTIFY SITTER
            // ==========================================

            NotificationService::reviewReceived(
                sitter:   $booking->sitter,
                reviewer: Auth::user(),
                rating:   (float) $validated['rating'],
                comment:  $validated['comments']
            );

            DB::commit();

            return back()->with('status', 'Thank you! Your review has been submitted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Review Store Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    // ==========================================
    // DESTROY — Delete a review
    // ==========================================

    public function destroy(Review $review)
    {
        abort_if(
            $review->user_id !== Auth::id(),
            403,
            'Unauthorized — this is not your review.'
        );

        DB::beginTransaction();
        try {
            $sitterProfile = $review->sitterProfile;

            $review->delete();

            // Recalculate after deletion
            if ($sitterProfile) {
                $this->recalculateAverageRating($sitterProfile);
            }

            DB::commit();

            return back()->with('status', 'Review deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Review Delete Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    /**
     * Recalculate the sitter's average rating based on all reviews.
     */
    protected function recalculateAverageRating($sitterProfile): void
    {
        $average = Review::where('sitter_id', $sitterProfile->id)
            ->avg('rating');

        $sitterProfile->update([
            'average_ratings' => round($average ?? 0, 1),
        ]);
    }
}
