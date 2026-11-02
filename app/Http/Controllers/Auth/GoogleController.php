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

            // Check if user exists (by google_id or email)
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // ✅ CHECK STATUS BEFORE LOGGING IN (Issue #6 Fix)
                if ($user->status !== 'active') {
                    return redirect()->route('login')->with('error', 'Your account has been suspended or banned. Please contact support.');
                }

                // Link Google ID if not set (google_id IS fillable — safe)
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
                Auth::login($user);
            } else {
                // ✅ Create with ONLY fillable fields
                $newUser = User::create([
                    'f_name'    => $googleUser->user['given_name'] ?? 'User',
                    'l_name'    => $googleUser->user['family_name'] ?? '',
                    'email'     => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password'  => Hash::make(Str::random(64)),
                ]);

                // ✅ Direct assignment for non-fillable fields
                $newUser->role                 = 'owner';
                $newUser->status               = 'active';
                $newUser->is_sitter            = false;
                $newUser->sitter_level         = 1;
                $newUser->sitter_status        = null;
                $newUser->id_validation_status = 'unverified';
                $newUser->pet_count            = 0;
                $newUser->save();

                Auth::login($newUser);
            }

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Throwable $th) {
            return redirect()->route('login')->with('error', 'Something went wrong with Google Login.');
        }
    }
}
