<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();

        // ==========================================
        // ROLE CHECK
        // ==========================================
        $canSwitchToSitter = $user->is_sitter
            && $user->id_validation_status === 'verified'
            && $user->sitterProfile !== null;

        // ==========================================
        // OWNER BOOKINGS — last 3
        // ==========================================
        $ownerBookings = Booking::with(['sitter', 'pet.petType'])
            ->where('owner_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $ownerActiveCount = Booking::where('owner_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->count();

        // ==========================================
        // SITTER BOOKINGS — last 3
        // ==========================================
        $sitterBookings = collect();

        if ($canSwitchToSitter) {
            $sitterBookings = Booking::with(['owner', 'pet.petType'])
                ->where('sitter_id', $user->id)
                ->whereIn('status', ['pending', 'accepted'])
                ->orderByDesc('created_at')
                ->limit(3)
                ->get();
        }

        $sitterPendingCount = $canSwitchToSitter
            ? Booking::where('sitter_id', $user->id)->where('status', 'pending')->count()
            : 0;

        return view('dashboard.owner-sitter_dashboard', compact(
            'canSwitchToSitter',
            'ownerBookings',
            'ownerActiveCount',
            'sitterBookings',
            'sitterPendingCount',
        ));
    }

    /**
     * Toggle sitter mode for dual-role users.
     */
    public function toggleSitterMode()
    {
        $user = auth()->user();

        if (!$user->isSitter()) {
            return back()->with('error', 'You are not an approved sitter.');
        }

        session(['sitter_mode' => !session('sitter_mode', false)]);

        $status = session('sitter_mode') ? 'ON' : 'OFF';
        return back()->with('status', "Sitter mode turned {$status}.");
    }

    /**
     * Apply to become a sitter.
     */
    public function applySitter()
    {
        $user = auth()->user();

        if (!$user->canApplyToBeSitter()) {
            return back()->with('error', 'You cannot apply to be a sitter at this time.');
        }

        $user->update([
            'is_sitter'            => true,
            'id_validation_status' => 'pending',
        ]);

        return redirect()->route('dashboard')
            ->with('status', 'Your sitter application has been submitted!');
    }
}