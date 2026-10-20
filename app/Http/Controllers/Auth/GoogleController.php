<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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

            // Check if user exists (by google_id or email)
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Link Google ID if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                Auth::login($user);
            } else {
                // Create new user
                $newUser = User::create([
                    'f_name' => $googleUser->user['given_name'] ?? 'User',
                    'l_name' => $googleUser->user['family_name'] ?? '',
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                    'role' => 'owner',
                    'status' => 'active',
                ]);

                Auth::login($newUser);
            }

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Throwable $th) {
            return redirect()->route('login')->with('error', 'Something went wrong with Google Login.');
        }
    }
}
