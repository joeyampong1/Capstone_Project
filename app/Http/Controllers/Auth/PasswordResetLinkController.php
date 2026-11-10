<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // ✅ FIX ISSUE #46: Prevent Email Enumeration
        // We attempt to send the link, but REGARDLESS of the outcome (success or fail),
        // we return the SAME generic success message to the user.
        Password::sendResetLink(
            $request->only('email')
        );

        return back()->with('status', __('messages.pf_reset_link_sent'));
    }
}
