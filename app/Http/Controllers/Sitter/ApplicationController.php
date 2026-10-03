<?php

namespace App\Http\Controllers\Sitter;

use App\Http\Controllers\Controller;
use App\Models\SitterProfile;
use App\Models\PetType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * Display sitter application form (standalone page).
     */
    public function create()
    {
        $petTypes = PetType::all();
        $sitterProfile = auth()->user()->sitterProfile;

        return view('sitters.sitter_application', compact('petTypes', 'sitterProfile'));
    }

    /**
     * Store new sitter application.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bio' => 'required|string|max:2000',
            'years_experience' => 'required|numeric|min:0|max:50',
            'sitter_type' => 'required|in:small_pets,large_pets,exotic_pets,all_pets',
            'rate_per_visit' => 'required|numeric|min:0',
            'food_preference' => 'required|in:owner_provides,sitter_provides,flexible',
            'food_budget' => 'nullable|numeric|min:0',
            'preferred_pet_types' => 'nullable|array',
            'preferred_pet_types.*' => 'exists:pet_types,id',
            'preferred_pet_sizes' => 'nullable|array',
            'preferred_pet_sizes.*' => 'in:small,medium,large,giant',
            'min_pets_capacity' => 'nullable|integer|min:0|max:20',
            'max_pets_capacity' => 'nullable|integer|min:1|max:20',
            'certificates' => 'nullable|array',
            'certificates.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $user = auth()->user();

        try {
            // Handle multiple certificate uploads
            $certPaths = [];
            if ($request->hasFile('certificates')) {
                foreach ($request->file('certificates') as $file) {
                    $certPaths[] = $file->store('sitter-certificates', 'public');
                }
            }

            $sitterProfile = $user->sitterProfile ?? new SitterProfile();
            $sitterProfile->user_id = $user->id;
            $sitterProfile->bio = $request->bio;
            $sitterProfile->experience_years = $request->years_experience;
            $sitterProfile->sitter_type = $request->sitter_type;
            $sitterProfile->base_rate = $request->rate_per_visit;
            $sitterProfile->food_preference = $request->food_preference;
            $sitterProfile->food_budget = $request->food_budget ?? ($request->food_preference === 'sitter_provides' ? 100 : 0);
            $sitterProfile->preferred_pet_types = $request->input('preferred_pet_types', []);
            $sitterProfile->preferred_pet_sizes = $request->input('preferred_pet_sizes', []);
            $sitterProfile->min_pets_capacity = $request->min_pets_capacity;
            $sitterProfile->max_pets_capacity = $request->max_pets_capacity;
            if (!empty($certPaths)) {
                $sitterProfile->certificates_path = $certPaths;
            }
            $sitterProfile->is_active = true;
            $sitterProfile->save();

            $user->is_sitter = true;
            $user->sitter_status = 'pending';
            $user->sitter_applied_at = Carbon::now();
            $user->save();

            return redirect()
                ->route('profile.edit')
                ->with('status', 'Application submitted successfully!');

        } catch (\Exception $e) {
            \Log::error('Sitter application store failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to submit application: ' . $e->getMessage()]);
        }
    }

    /**
     * Update existing sitter application.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $sitterProfile = $user->sitterProfile;

        if (!$sitterProfile) {
            return back()->withErrors(['error' => 'No sitter application found.']);
        }

        $request->validate([
            'bio' => 'required|string|max:2000',
            'years_experience' => 'required|numeric|min:0|max:50',
            'sitter_type' => 'required|in:small_pets,large_pets,exotic_pets,all_pets',
            'rate_per_visit' => 'required|numeric|min:0',
            'food_preference' => 'required|in:owner_provides,sitter_provides,flexible',
            'food_budget' => 'nullable|numeric|min:0',
            'preferred_pet_types' => 'nullable|array',
            'preferred_pet_types.*' => 'exists:pet_types,id',
            'preferred_pet_sizes' => 'nullable|array',
            'preferred_pet_sizes.*' => 'in:small,medium,large,giant',
            'min_pets_capacity' => 'nullable|integer|min:0|max:20',
            'max_pets_capacity' => 'nullable|integer|min:1|max:20',
            'certificates' => 'nullable|array',
            'certificates.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            $sitterProfile->bio = $request->bio;
            $sitterProfile->experience_years = $request->years_experience;
            $sitterProfile->sitter_type = $request->sitter_type;
            $sitterProfile->base_rate = $request->rate_per_visit;
            $sitterProfile->food_preference = $request->food_preference;
            $sitterProfile->food_budget = $request->food_budget ?? ($request->food_preference === 'sitter_provides' ? 100 : 0);
            $sitterProfile->preferred_pet_types = $request->input('preferred_pet_types', []);
            $sitterProfile->preferred_pet_sizes = $request->input('preferred_pet_sizes', []);
            $sitterProfile->min_pets_capacity = $request->min_pets_capacity;
            $sitterProfile->max_pets_capacity = $request->max_pets_capacity;

            // Handle multiple certificate uploads
            if ($request->hasFile('certificates')) {
                // Delete old certificates (loop through array)
                if (!empty($sitterProfile->certificates_path) && is_array($sitterProfile->certificates_path)) {
                    foreach ($sitterProfile->certificates_path as $oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                // Store new certificates
                $certPaths = [];
                foreach ($request->file('certificates') as $file) {
                    $certPaths[] = $file->store('sitter-certificates', 'public');
                }
                $sitterProfile->certificates_path = $certPaths;
            }

            $sitterProfile->save();

            if ($user->sitter_status === null) {
                $user->is_sitter = true;
                $user->sitter_status = 'pending';
                $user->sitter_applied_at = Carbon::now();
                $user->save();
            }

            return redirect()
                ->route('profile.edit')
                ->with('status', 'Application updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Sitter application update failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update application: ' . $e->getMessage()]);
        }
    }
}
