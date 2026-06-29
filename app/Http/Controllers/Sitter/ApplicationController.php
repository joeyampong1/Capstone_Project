<?php

namespace App\Http\Controllers\Sitter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function create()
    {
        return view('sitter.sitter_application');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'bio' => 'nullable|string',
            'rate_per_visit' => 'nullable|numeric|min:0',
            'pet_types' => 'nullable|string|max:255',
            'food_preference' => 'nullable|in:owner_provides,sitter_provides,flexible',
            'can_provide_food' => 'sometimes|boolean',
        ]);

        $user->bio = $validated['bio'];
        $user->rate_per_visit = $validated['rate_per_visit'];
        $user->pet_types = $validated['pet_types'];
        $user->food_preference = $validated['food_preference'];
        $user->can_provide_food = $request->has('can_provide_food');
        
        // If user is not yet a sitter, set application status
        if (!$user->is_sitter) {
            $user->is_sitter = true;
            $user->sitter_status = 'pending';
            $user->sitter_applied_at = now();
        }

        $user->save();

        return redirect()->route('sitter.application.create')->with('status', 'Application submitted successfully!');
    }
}