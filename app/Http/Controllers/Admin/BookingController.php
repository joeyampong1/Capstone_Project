<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Admin booking list.
     */
    public function index(Request $request)
    {
        $bookings = Booking::with(['owner', 'sitter.sitterProfile', 'pet', 'visits'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total'     => Booking::count(),
            'pending'   => Booking::where('status', 'pending')->count(),
            'active'    => Booking::whereIn('status', ['active', 'accepted', 'ongoing'])->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        // For filter dropdowns
        $owners  = User::where('role', 'owner')
            ->select('id', 'f_name', 'l_name')
            ->orderBy('f_name')
            ->get();

        $sitters = User::where('is_sitter', true)
            ->select('id', 'f_name', 'l_name')
            ->orderBy('f_name')
            ->get();

        return view('admin.bookings.booking_management', compact(
            'bookings', 'stats', 'owners', 'sitters'
        ));
    }

    /**
     * Cancel a booking (admin override).
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if (! in_array($booking->status, ['pending', 'accepted', 'active', 'ongoing'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update([
            'status'       => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Force-complete a booking.
     */
    public function complete(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if (! in_array($booking->status, ['active', 'accepted', 'ongoing'])) {
            return back()->with('error', 'This booking cannot be completed.');
        }

        $booking->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        // Mark all visits as done
        $booking->visits()
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed', 'completed_at' => now()]);

        return back()->with('success', 'Booking force-completed.');
    }

    /**
     * Manual payment tracking (kung wala pang payment gateway).
     */
    public function markPaid(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $isPaid = $request->input('payment_status') === 'paid';

        $booking->update([
            'payment_status' => $isPaid ? 'paid' : 'pending',
            'paid_at'        => $isPaid ? now() : null,
        ]);

        return back()->with('success', 'Payment status updated.');
    }
}