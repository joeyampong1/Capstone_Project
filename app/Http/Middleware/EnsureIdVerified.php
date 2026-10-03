<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdVerified
{
    /**
     * Ensure the authenticated user has a verified ID.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin always allowed
        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->id_validation_status !== 'verified') {
            $message = match($user->id_validation_status) {
                'pending'  => 'Your ID verification is still pending. Please wait for admin approval.',
                'rejected' => 'Your ID verification was rejected. Please re-upload a valid ID.',
                default    => 'Please verify your ID first before proceeding.',
            };

            if ($request->expectsJson()) {
                return response()->json([
                    'success'  => false,
                    'message'  => $message,
                    'redirect' => route('profile.edit'),
                ], 403);
            }

            return redirect()
                ->route('profile.edit')
                ->with('error', $message);
        }

        return $next($request);
    }
}
