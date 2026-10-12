<?php

namespace App\Http\Controllers\Sitter;

use App\Http\Controllers\Controller;
use App\Models\SitterProfile;
use App\Models\PetType;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function __construct(
        private CloudinaryService $cloudinary
    ) {}

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
     * Shared validation rules — para walay duplication sa store/update.
     */
    private function validationRules(): array
    {
        return [
            'bio'                           => 'required|string|max:2000',
            'years_experience'              => 'required|numeric|min:0|max:50',
            'sitter_type'                   => 'required|in:small_pets,large_pets,exotic_pets,all_pets',
            'rate_per_visit'                => 'required|numeric|min:0',
            'food_preference'               => 'required|in:owner_provides,sitter_provides,flexible',
            'food_budget'                   => 'nullable|numeric|min:0',

            'preferred_pet_types'           => 'nullable|array',
            'preferred_pet_types.*'         => 'exists:pet_types,id',

            'preferred_pet_sizes'           => 'nullable|array',
            'preferred_pet_sizes.*'         => 'in:small,medium,large,giant',

            'accepted_health_conditions'    => 'nullable|array',
            'accepted_health_conditions.*'  => 'in:special_needs,partial_vax,chronic,recovering',

            'accepted_temperaments'         => 'nullable|array',
            'accepted_temperaments.*'       => 'in:friendly,playful,shy,aggressive,anxious',

            'min_pets_capacity'             => 'nullable|integer|min:0|max:20',
            'max_pets_capacity'             => 'nullable|integer|min:1|max:20|gte:min_pets_capacity',

            'certificates'                  => 'nullable|array',
            'certificates.*'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    /**
     * Store new sitter application (with Cloudinary certificates).
     */
    public function store(Request $request)
    {
        $request->validate($this->validationRules());

        $user = auth()->user();

        try {
            // Upload certificates to Cloudinary
            $certUrls = [];
            $certIds  = [];

            if ($request->hasFile('certificates')) {
                foreach ($request->file('certificates') as $file) {
                    $upload = $this->cloudinary->upload($file, 'sitter-certificates');

                    if ($upload['success']) {
                        $certUrls[] = $upload['url'];
                        $certIds[]  = $upload['public_id'];
                    } else {
                        \Log::error('Certificate upload failed', [
                            'error' => $upload['error'],
                        ]);
                    }
                }
            }

            $sitterProfile = $user->sitterProfile ?? new SitterProfile();
            $sitterProfile->user_id                     = $user->id;
            $sitterProfile->bio                         = $request->bio;
            $sitterProfile->experience_years            = $request->years_experience;
            $sitterProfile->sitter_type                 = $request->sitter_type;
            $sitterProfile->base_rate                   = $request->rate_per_visit;
            $sitterProfile->food_preference             = $request->food_preference;
            $sitterProfile->food_budget                 = $request->food_budget ?? ($request->food_preference === 'sitter_provides' ? 100 : 0);
            $sitterProfile->preferred_pet_types         = $request->input('preferred_pet_types', []);
            $sitterProfile->preferred_pet_sizes         = $request->input('preferred_pet_sizes', []);
            $sitterProfile->accepted_health_conditions  = $request->input('accepted_health_conditions', []);
            $sitterProfile->accepted_temperaments       = $request->input('accepted_temperaments', []);
            $sitterProfile->min_pets_capacity           = $request->min_pets_capacity;
            $sitterProfile->max_pets_capacity           = $request->max_pets_capacity;

            if (!empty($certUrls)) {
                $sitterProfile->certificates_path         = $certUrls;
                $sitterProfile->certificates_public_ids   = $certIds;
            }

            $sitterProfile->is_active = true;
            $sitterProfile->save();

            $user->is_sitter         = true;
            $user->sitter_status     = 'pending';
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
     * Update existing sitter application (with Cloudinary certificates).
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $sitterProfile = $user->sitterProfile;

        if (!$sitterProfile) {
            return back()->withErrors(['error' => 'No sitter application found.']);
        }

        $request->validate($this->validationRules());

        try {
            $sitterProfile->bio                         = $request->bio;
            $sitterProfile->experience_years            = $request->years_experience;
            $sitterProfile->sitter_type                 = $request->sitter_type;
            $sitterProfile->base_rate                   = $request->rate_per_visit;
            $sitterProfile->food_preference             = $request->food_preference;
            $sitterProfile->food_budget                 = $request->food_budget ?? ($request->food_preference === 'sitter_provides' ? 100 : 0);
            $sitterProfile->preferred_pet_types         = $request->input('preferred_pet_types', []);
            $sitterProfile->preferred_pet_sizes         = $request->input('preferred_pet_sizes', []);
            $sitterProfile->accepted_health_conditions  = $request->input('accepted_health_conditions', []);
            $sitterProfile->accepted_temperaments       = $request->input('accepted_temperaments', []);
            $sitterProfile->min_pets_capacity           = $request->min_pets_capacity;
            $sitterProfile->max_pets_capacity           = $request->max_pets_capacity;

            // Handle new certificate uploads
            if ($request->hasFile('certificates')) {
                // Delete old certificates (Cloudinary or local)
                $this->deleteOldCertificates(
                    $sitterProfile->certificates_path,
                    $sitterProfile->certificates_public_ids
                );

                // Upload new certificates to Cloudinary
                $certUrls = [];
                $certIds  = [];

                foreach ($request->file('certificates') as $file) {
                    $upload = $this->cloudinary->upload($file, 'sitter-certificates');

                    if ($upload['success']) {
                        $certUrls[] = $upload['url'];
                        $certIds[]  = $upload['public_id'];
                    } else {
                        \Log::error('Certificate upload failed during update', [
                            'error' => $upload['error'],
                        ]);
                    }
                }

                $sitterProfile->certificates_path       = $certUrls;
                $sitterProfile->certificates_public_ids = $certIds;
            }

            $sitterProfile->save();

            if ($user->sitter_status === null) {
                $user->is_sitter         = true;
                $user->sitter_status     = 'pending';
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

    // ========================================== //
    // PRIVATE HELPER                              //
    // ========================================== //

    /**
     * Delete old certificates — Cloudinary (via public_ids) or local storage.
     */
    private function deleteOldCertificates($paths, $publicIds): void
    {
        if (empty($paths) || !is_array($paths)) {
            return;
        }

        foreach ($paths as $index => $path) {
            // Cloudinary URL
            if (str_starts_with($path, 'http')) {
                $publicId = is_array($publicIds) ? ($publicIds[$index] ?? null) : null;
                if ($publicId) {
                    $this->cloudinary->delete($publicId);
                }
            }
            // Local storage
            elseif (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
