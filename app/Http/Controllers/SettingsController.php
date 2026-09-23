<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Update user's language preference.
     */
    public function updateLanguage(Request $request): RedirectResponse
    {
        $request->validate([
            'locale' => 'required|in:en,fil,es,fr',
        ]);

        auth()->user()->update([
            'locale' => $request->locale,
        ]);

        return back();
    }

    /**
     * Toggle sitter mode on/off.
     */
    public function updateSitterMode(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Only verified sitters can toggle
        if (! $user->isSitter()) {
            return back()->with('error', 'You are not eligible to switch sitter mode.');
        }

        $user->update([
            'is_sitter' => ! $user->is_sitter,
        ]);

        return back()->with('status', 'Sitter mode updated.');
    }

    /**
     * Update UI preferences (dark mode, font size, notifications, privacy).
     */
    public function updateUiPreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dark_mode'           => 'sometimes|boolean',
            'font_size'           => 'sometimes|in:small,default,large,extra_large',
            'push_notifications'  => 'sometimes|boolean',
            'email_notifications' => 'sometimes|boolean',
            'sms_notifications'   => 'sometimes|boolean',
            'show_email'          => 'sometimes|boolean',
            'profile_visibility'  => 'sometimes|in:public,private,hidden',
        ]);

        auth()->user()->setting->update($validated);

        return response()->json([
            'ok'          => true,
            'preferences' => auth()->user()->setting->only(array_keys($validated)),
        ]);
    }
}