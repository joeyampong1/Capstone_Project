<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1. First, check if user exists by Google ID (Secure)
            $user = User::where('google_id', $googleUser->id)->first();

            // 2. If not found, check if the email is already registered
            if (!$user) {
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // ISSUE #8 FIX: If the email is already registered, ensure it is verified before linking
                    if (!$existingUser->email_verified_at) {
                        return redirect()->route('login')->with('error', 'This email is already registered but not verified. Please verify your email first or contact support.');
                    }

                    // If verified, safely link the Google ID
                    $user = $existingUser;
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
            }

            // 3. If user is found (either by google_id or verified email)
            if ($user) {
                // ISSUE #6 FIX: Check account status (banned/suspended)
                if ($user->status !== 'active') {
                    return redirect()->route('login')->with('error', 'Your account has been suspended or banned. Please contact support.');
                }

                Auth::login($user);
            }
            // 4. If no user exists, create a new account
            else {
                // Ensure the email from Google is verified
                if (!($googleUser->user['email_verified'] ?? false)) {
                    return redirect()->route('login')->with('error', 'Your Google email is not verified. Please verify it with Google first.');
                }

                $newUser = User::create([
                    'f_name'    => $googleUser->user['given_name'] ?? 'User',
                    'l_name'    => $googleUser->user['family_name'] ?? '',
                    'email'     => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password'  => Hash::make(Str::random(64)),
                ]);

                // Direct assignment for non-fillable fields
                $newUser->role                 = 'owner';
                $newUser->status               = 'active';
                $newUser->is_sitter            = false;
                $newUser->sitter_level         = 1;
                $newUser->sitter_status        = null;
                $newUser->id_validation_status = 'unverified';
                $newUser->pet_count            = 0;

                // Since Google verified the email, mark it as verified in our system
                $newUser->email_verified_at    = now();

                $newUser->save();

                Auth::login($newUser);
            }

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Throwable $th) {
            return redirect()->route('login')->with('error', 'Something went wrong with Google Login.');
        }
    }
}
