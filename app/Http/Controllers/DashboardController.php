<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index()
    {
        $user = auth()->user();

        // Check if user is admin
        if ($user->isAdmin()) {
            return view('dashboard.admin_dashboard', compact('user'));
        }

        // Everyone else → Owner/Sitter dashboard
        return view('dashboard.owner-sitter_dashboard', compact('user'));
    }

    /**
     * Toggle sitter mode for dual-role users.
     */
    public function toggleSitterMode(Request $request)
    {
        $user = auth()->user();

        // Only approved sitters can toggle
        if (!$user->isSitter()) {
            return back()->with('error', 'You are not an approved sitter.');
        }

        // Toggle sitter mode
        session(['sitter_mode' => !session('sitter_mode', false)]);

        $status = session('sitter_mode') ? 'ON' : 'OFF';
        return back()->with('status', "Sitter mode turned {$status}.");
    }

    /**
     * Apply to become a sitter.
     */
    public function applySitter(Request $request)
    {
        $user = auth()->user();

        // Check if user can apply
        if (!$user->canApplyToBeSitter()) {
            return back()->with('error', 'You cannot apply to be a sitter at this time.');
        }

        $user->update([
            'is_sitter' => true,
            'sitter_status' => 'pending',
            'sitter_applied_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('status', 'Your sitter application has been submitted!');
    }
}