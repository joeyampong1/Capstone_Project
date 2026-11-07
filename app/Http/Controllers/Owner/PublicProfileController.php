<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    // ==========================================
    // SEARCH ACCOUNTS (Live Search & Full Page)
    // ==========================================
    public function search(Request $request)
    {
        $query = $request->input('q');

        $users = User::query()
            ->with('setting')
            ->whereHas('setting', function ($q) {
                // Exclude hidden profiles from search results
                $q->where('profile_visibility', '!=', 'hidden');
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('f_name', 'like', "%{$query}%")
                        ->orWhere('l_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->limit(10)
            ->get();

        // Return JSON response for AJAX requests (Live Search Modal)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($users->map(function($user) {
                return [
                    'id'         => $user->id,
                    'name'       => $user->full_name,
                    'photo'      => $user->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($user->full_name ?? 'U'),
                    'is_sitter'  => $user->is_sitter,
                    'visibility' => $user->setting->profile_visibility ?? 'public',
                    'url'        => route('public.profile', $user->id),
                ];
            }));
        }

        // Return normal view for full page results
        $users = User::query()
            ->with('setting')
            ->whereHas('setting', function ($q) {
                $q->where('profile_visibility', '!=', 'hidden');
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('f_name', 'like', "%{$query}%")
                        ->orWhere('l_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->paginate(12);

        return view('profile.search', compact('users', 'query'));
    }

    // ==========================================
    // SHOW PROFILE
    // ==========================================
    public function show($id)
    {
        // Eager load relationships to check visibility
        $user = User::with(['sitterProfile', 'pets.petType', 'setting'])
            ->findOrFail($id);

        // Get the profile visibility status
        $visibility = $user->setting->profile_visibility ?? 'public';
        $viewer = auth()->user();

        // ✅ FIX: If the viewer is the OWNER or an ADMIN, allow full access
        if ($viewer && ($viewer->id === $user->id || $viewer->isAdmin())) {
            $visibility = 'public'; // Force public so the owner/admin can see their own details
        }
        // Logic for OTHER logged-in users
        elseif ($viewer) {
            // Block access completely if the profile is HIDDEN
            if ($visibility === 'hidden') {
                abort(403, 'This profile is hidden by the user.');
            }

            // If PRIVATE, check if the viewer is a past client
            if ($visibility === 'private') {
                $isPastClient = \App\Models\Booking::where('sitter_id', $user->id)
                    ->where('owner_id', $viewer->id)
                    ->whereIn('status', ['accepted', 'completed'])
                    ->exists();

                if (!$isPastClient) {
                    // Set to locked so the Blade file shows the lock message
                    $visibility = 'private_locked';
                }
            }
        }
        // Logic for GUESTS (not logged in)
        else {
            if ($visibility !== 'public') {
                $visibility = 'private_locked';
            }
        }

        // Reviews count (placeholder)
        $reviewsCount = 0;

        // Average rating (default 3.0 if no reviews)
        $averageRating = $user->average_rating ?? 3.0;

        // Pass the visibility status to the view
        return view('profile.public', compact('user', 'reviewsCount', 'averageRating', 'visibility'));
    }
}
