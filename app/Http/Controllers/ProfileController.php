<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; 
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // ========================================== //
    // 🔥 NEW: Update Profile Photo               //
    // ========================================== //
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048', // Max 2MB
        ]);

        $user = $request->user();

        // Delete old photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->profile_photo = $path;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $user = $request->user();
        $user->location = $request->location;
        $user->latitude = $request->latitude;
        $user->longitude = $request->longitude;
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'location-updated');
    }

    public function updateId(Request $request)
    {
        $user = $request->user();
        $action = $request->input('action');

        // Step 1: Save ID type
        if ($action === 'save_id_type') {
            $request->validate(['id_type' => 'required|string|max:50']);
            $user->id_type = $request->id_type;
            $user->save();
            return redirect()->route('profile.edit')->with('status', 'id_type_saved');
        }

        // Step 2: Upload ID image
        if ($action === 'upload_id') {
            $request->validate(['gov_id_path' => 'required|file|mimes:jpg,jpeg,png|max:5120']);
            if ($user->gov_id_path) Storage::disk('public')->delete($user->gov_id_path);
            $path = $request->file('gov_id_path')->store('ids', 'public');
            $user->gov_id_path = $path;
            $user->save();
            return redirect()->route('profile.edit')->with('status', 'id-updated');
        }

        // Step 3: Upload selfie
        if ($action === 'upload_selfie') {
            $request->validate(['selfie_photo' => 'required|file|mimes:jpg,jpeg,png|max:5120']);
            if ($user->selfie_photo) Storage::disk('public')->delete($user->selfie_photo);
            $path = $request->file('selfie_photo')->store('selfies', 'public');
            $user->selfie_photo = $path;
            $user->id_validation_status = 'pending'; // Set to pending
            $user->save();
            return redirect()->route('profile.edit')->with('status', 'id-updated');
        }

        // Step 4: Submit for verification
        if ($action === 'submit_verification') {
            // Already pending, just show the pending status
            return redirect()->route('profile.edit')->with('status', 'submitted');
        }

        return back()->withErrors(['error' => 'Invalid action.']);
    }
}