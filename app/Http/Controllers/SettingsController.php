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
        // 1. Validate incoming preferences
        $validated = $request->validate([
            'dark_mode'                   => 'sometimes|boolean',
            'font_size'                   => 'sometimes|in:small,default,large,extra_large',
            'push_notifications_enabled'  => 'sometimes|boolean',
            'email_notifications_enabled' => 'sometimes|boolean',
            'sms_notifications'           => 'sometimes|boolean',
            'show_email'                  => 'sometimes|boolean',
            'profile_visibility'          => 'sometimes|in:public,private,hidden',
        ]);

        $user = auth()->user();

        // 2. Save Push and Email preferences to the `users` table
        $userData = [];
        if ($request->has('push_notifications_enabled')) {
            $userData['push_notifications_enabled'] = $validated['push_notifications_enabled'];
        }
        if ($request->has('email_notifications_enabled')) {
            $userData['email_notifications_enabled'] = $validated['email_notifications_enabled'];
        }

        if (!empty($userData)) {
            $user->update($userData);
        }

        // 3. Remove Push and Email from the array
        unset($validated['push_notifications_enabled'], $validated['email_notifications_enabled']);

        // 4. Save remaining preferences (Dark mode, font size, SMS) to the `settings` table
        if (!empty($validated)) {
            if (!$user->setting) {
                $user->setting()->create([]);
            }
            $user->setting->update($validated);
        }

        return response()->json([
            'ok' => true,
        ]);
    }

}
