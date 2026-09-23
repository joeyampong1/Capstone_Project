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
    // Update Profile Photo                       //
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
            'location'  => 'required|string|max:255',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $user = $request->user();
        $user->location  = $request->location;
        $user->latitude  = $request->latitude;
        $user->longitude = $request->longitude;
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'location-updated');
    }

    public function updateId(Request $request)
    {
        $user   = $request->user();
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

    // ========================================== //
    // Toggle Sitter Mode                         //
    // ========================================== //
    /**
     * Toggle Sitter Mode using the existing is_sitter boolean.
     * Only available for approved sitters.
     */
    public function toggleSitterMode()
    {
        $user = auth()->user();

        // Only users who were approved as sitters can toggle
        if ($user->sitter_status !== 'approved') {
            return back()->with('error', 'Only approved pet sitters can toggle Sitter Mode.');
        }

        $user->update([
            'is_sitter' => ! $user->is_sitter,
        ]);

        return back()->with(
            'status',
            $user->fresh()->is_sitter
                ? 'Sitter Mode is ON. You are now browsing as a Pet Sitter.'
                : 'Sitter Mode is OFF. You are now browsing as a Pet Owner.'
        );
    }

    // ========================================== //
    // Update Sitter Settings                     //
    // ========================================== //
    /**
     * Update sitter-specific settings (rate, food preference, etc.)
     * Only available for approved sitters.
     */
    public function updateSitterSettings(Request $request)
    {
        $user = $request->user();

        if ($user->sitter_status !== 'approved') {
            return back()->with('error', 'Only approved pet sitters can update these settings.');
        }

        $validated = $request->validate([
            'rate_per_visit'   => 'nullable|numeric|min:0|max:99999',
            'food_preference'  => 'nullable|in:owner_provides,sitter_provides,flexible',
            'bio'              => 'nullable|string|max:2000',
            'pet_types'        => 'nullable|string|max:255',
            'can_provide_food' => 'nullable|boolean',
        ]);

        // Handle can_provide_food checkbox (unchecked = not present in request)
        $validated['can_provide_food'] = $request->boolean('can_provide_food');

        $user->update($validated);

        return redirect()
            ->route('profile.edit')
            ->with('status', 'sitter-settings-updated');
    }

    /**
     * Update user's preferred language.
     */
/**
 * Update user's preferred language.
 */
    public function updateLanguage(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:en,fil,es,fr',
        ]);

        $locale = $request->input('locale');

        $request->user()->update([
            'locale' => $locale,
        ]);

        // Apply instantly for the current request
        app()->setLocale($locale);
        session(['locale' => $locale]);

        return response()->json([
            'success' => true,
            'locale'  => $locale,
            'message' => 'Language updated successfully.',
        ]);
    }

}