<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\CloudinaryService;
use App\Services\OcrService;
use App\Services\FaceMatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private CloudinaryService $cloudinary
    ) {}

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        unset($validated['name']);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // ========================================== //
    // Update Profile Photo (Avatar) — Cloudinary //
    // ========================================== //
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:5120', // 5MB
        ]);

        $user = $request->user();

        // Delete old photo (Cloudinary or local)
        $this->deleteOldImage($user->profile_photo, $user->profile_photo_public_id);

        // Upload to Cloudinary
        $upload = $this->cloudinary->upload(
            $request->file('profile_photo'),
            'avatars',
            [
                'transformation' => [
                    'width'   => 400,
                    'height'  => 400,
                    'crop'    => 'fill',
                    'gravity' => 'face',
                ],
            ]
        );

        if (!$upload['success']) {
            return response()->json([
                'success' => false,
                'error'   => $upload['error'] ?? 'Upload failed',
            ], 500);
        }

        $user->profile_photo           = $upload['url'];
        $user->profile_photo_public_id = $upload['public_id'];
        $user->save();

        return response()->json(['success' => true]);
    }

    // ========================================== //
    // Update Cover Photo — Cloudinary            //
    // ========================================== //
    public function updateCover(Request $request)
    {
        $request->validate([
            'cover_photo' => 'required|image|max:5120',
        ]);

        $user = $request->user();

        // Delete old cover (Cloudinary or local)
        $this->deleteOldImage($user->cover_photo, $user->cover_photo_public_id);

        // Upload to Cloudinary
        $upload = $this->cloudinary->upload(
            $request->file('cover_photo'),
            'covers',
            [
                'transformation' => [
                    'width'  => 1600,
                    'height' => 400,
                    'crop'   => 'fill',
                ],
            ]
        );

        if (!$upload['success']) {
            return response()->json([
                'success' => false,
                'error'   => $upload['error'] ?? 'Upload failed',
            ], 500);
        }

        $user->cover_photo           = $upload['url'];
        $user->cover_photo_public_id = $upload['public_id'];
        $user->save();

        return response()->json(['success' => true]);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'location'  => 'required|string|max:255',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $user = $request->user();
        $user->location  = $request->location;
        $user->latitude  = $request->latitude;
        $user->longitude = $request->longitude;
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'location-updated');
    }

    // ========================================== //
    // ID Verification — Cloudinary + OCR + Face  //
    // ========================================== //
    public function updateId(Request $request, OcrService $ocr, FaceMatchService $faceMatch)
    {
        $user   = $request->user();
        $action = $request->input('action');

        // ==========================================
        // Step 1: Save ID type
        // ==========================================
        if ($action === 'save_id_type') {
            $request->validate(['id_type' => 'required|string|max:50']);
            $user->id_type = $request->id_type;
            $user->save();
            return redirect()->route('profile.edit')->with('status', 'id_type_saved');
        }

        // ==========================================
        // Step 2: Upload ID image + Run OCR
        // ==========================================
        if ($action === 'upload_id') {
            \Log::info('=== upload_id START ===', [
                'user_id'  => $user->id,
                'has_file' => $request->hasFile('gov_id_path'),
                'id_type'  => $request->input('id_type'),
            ]);

            // Validation
            $validator = \Validator::make($request->all(), [
                'gov_id_path' => 'required|file|mimes:jpg,jpeg,png|max:5120',
                'id_type'     => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Save ID type kung gi-submit
            if ($request->filled('id_type')) {
                $user->id_type = $request->input('id_type');
            }

            // Delete old ID (Cloudinary o local)
            $this->deleteOldImage($user->gov_id_path, $user->gov_id_public_id);

            // Upload new ID to Cloudinary
            $upload = $this->cloudinary->upload($request->file('gov_id_path'), 'ids');

            if (!$upload['success']) {
                \Log::error('Cloudinary ID upload failed', ['error' => $upload['error']]);
                return back()->withErrors(['gov_id_path' => 'Upload failed: ' . $upload['error']]);
            }

            $user->gov_id_path      = $upload['url'];
            $user->gov_id_public_id = $upload['public_id'];

            \Log::info('ID uploaded to Cloudinary', [
                'url'       => $upload['url'],
                'public_id' => $upload['public_id'],
            ]);

            // ------------------------------------------------------------
            // RUN OCR — save temp local file for OCR service
            // ------------------------------------------------------------
            try {
                $localTempPath = $request->file('gov_id_path')->getRealPath();
                $tempStoragePath = 'temp/' . uniqid() . '.jpg';

                Storage::disk('public')->put($tempStoragePath, file_get_contents($localTempPath));

                $ocrResult = $ocr->extractIdDetails($tempStoragePath);

                \Log::info('=== OCR COMPLETED ===', [
                    'success'   => $ocrResult['success'] ?? false,
                    'name'      => $ocrResult['name'] ?? null,
                    'id_number' => $ocrResult['id_number'] ?? null,
                    'dob'       => $ocrResult['dob'] ?? null,
                ]);

                $user->ocr_result = $ocrResult;

                // Cleanup temp file
                Storage::disk('public')->delete($tempStoragePath);
            } catch (\Exception $e) {
                \Log::error('OCR exception', [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]);
                $user->ocr_result = [
                    'success' => false,
                    'error'   => 'OCR failed: ' . $e->getMessage(),
                ];
            }

            $user->save();

            \Log::info('=== upload_id END ===');

            return redirect()->route('profile.edit')->with('status', 'id-updated');
        }

        // ==========================================
        // Step 3: Upload Selfie — Cloudinary
        // ==========================================
        if ($action === 'upload_selfie') {
            $request->validate(['selfie_photo' => 'required|file|mimes:jpg,jpeg,png|max:5120']);

            // Delete old selfie
            $this->deleteOldImage($user->selfie_photo, $user->selfie_photo_public_id);

            // Upload to Cloudinary
            $upload = $this->cloudinary->upload($request->file('selfie_photo'), 'selfies');

            if (!$upload['success']) {
                return back()->withErrors(['selfie_photo' => 'Upload failed: ' . $upload['error']]);
            }

            $user->selfie_photo           = $upload['url'];
            $user->selfie_photo_public_id = $upload['public_id'];
            $user->id_validation_status   = 'unverified';
            $user->save();

            return redirect()->route('profile.edit')->with('status', 'id-updated');
        }

        // ==========================================
        // Step 4: Submit for verification (face match)
        // ==========================================
        if ($action === 'submit_verification') {
            // Check profile completeness
            $missingFields = [];
            if (empty($user->f_name))         $missingFields[] = 'First Name';
            if (empty($user->l_name))         $missingFields[] = 'Last Name';
            if (empty($user->date_of_birth))  $missingFields[] = 'Date of Birth';
            if (empty($user->gender))         $missingFields[] = 'Gender';
            if (empty($user->contact_number)) $missingFields[] = 'Contact Number';
            if (empty($user->address))        $missingFields[] = 'Address';

            if (!empty($missingFields)) {
                return back()->withErrors([
                    'error' => 'Please complete your profile first: ' . implode(', ', $missingFields),
                ]);
            }

            // ------------------------------------------------------------
            // RUN FACE MATCH — download selfie from Cloudinary to temp
            // ------------------------------------------------------------
            try {
                $localSelfiePath = $this->downloadToTemp($user->selfie_photo);

                // FaceMatchService expects relative path on 'public' disk
                // Move to storage temporarily
                $tempStoragePath = 'temp/selfies/' . uniqid() . '.jpg';
                Storage::disk('public')->put($tempStoragePath, file_get_contents($localSelfiePath));

                $result = $faceMatch->verify($tempStoragePath);

                // Cleanup
                @unlink($localSelfiePath);
                Storage::disk('public')->delete($tempStoragePath);
            } catch (\Exception $e) {
                \Log::error('Face match exception', ['error' => $e->getMessage()]);
                return back()->withErrors([
                    'selfie_photo' => 'Face verification failed: ' . $e->getMessage(),
                ]);
            }

            if (!$result['success']) {
                return back()->withErrors([
                    'selfie_photo' => 'Face verification failed: ' . ($result['error'] ?? 'unknown error'),
                ]);
            }

            // Store face match results
            $user->face_match_score        = $result['confidence'];
            $user->face_detected_on_id     = true;
            $user->face_detected_on_selfie = true;
            $user->id_expired              = false;

            // Name match
            $ocrName   = strtoupper($user->ocr_result['name'] ?? '');
            $userLast  = strtoupper($user->l_name ?? '');
            $userFirst = strtoupper($user->f_name ?? '');

            $nameMatch = false;
            if ($ocrName && ($userLast || $userFirst)) {
                if ($userLast && str_contains($ocrName, $userLast))         $nameMatch = true;
                elseif ($userFirst && str_contains($ocrName, $userFirst))   $nameMatch = true;
            }
            $user->name_match = $nameMatch ? 'matched' : 'not_matched';

            // Birthdate match
            $ocrDob  = $user->ocr_result['dob'] ?? null;
            $userDob = $user->date_of_birth ? $user->date_of_birth->format('Y/m/d') : null;

            $birthdateMatch = false;
            if ($ocrDob && $userDob) {
                $ocrDobNorm  = str_replace(['-', '.'], '/', $ocrDob);
                $userDobNorm = str_replace(['-', '.'], '/', $userDob);
                $birthdateMatch = ($ocrDobNorm === $userDobNorm);
            }
            $user->birthdate_match = ($ocrDob && $userDob) ? ($birthdateMatch ? 'matched' : 'not_matched') : null;

            $user->document_authenticity = null;
            $user->liveness_detection    = null;

            // Handle match / no match
            if (!$result['match']) {
                $user->id_validation_status = 'rejected';
                $user->save();
                return back()->withErrors([
                    'selfie_photo' => 'Face does not match the ID. Distance: ' . $result['distance'],
                ]);
            }

            $user->id_validation_status = 'pending';
            $user->save();

            \Log::info('=== SUBMIT VERIFICATION ===', [
                'user_id'   => $user->id,
                'status'    => $user->id_validation_status,
                'face_score'=> $user->face_match_score,
            ]);

            return redirect()->route('profile.edit')->with('status', 'submitted');
        }

        return back()->withErrors(['error' => 'Invalid action.']);
    }

    // ========================================== //
    // Toggle Sitter Mode                         //
    // ========================================== //
    public function toggleSitterMode()
    {
        $user = auth()->user();

        if ($user->sitter_status !== 'approved') {
            return back()->with('error', 'Only approved pet sitters can toggle Sitter Mode.');
        }

        $user->update(['is_sitter' => !$user->is_sitter]);

        return back()->with(
            'status',
            $user->fresh()->is_sitter
                ? 'Sitter Mode is ON. You are now browsing as a Pet Sitter.'
                : 'Sitter Mode is OFF. You are now browsing as a Pet Owner.'
        );
    }

    // ========================================== //
    // Update Sitter Settings                     //
    // ========================================== //
    public function updateSitterSettings(Request $request)
    {
        $user = $request->user();

        if ($user->sitter_status !== 'approved') {
            return back()->with('error', 'Only approved pet sitters can update these settings.');
        }

        $validated = $request->validate([
            'base_rate'             => 'nullable|numeric|min:0|max:99999',
            'food_preference'       => 'nullable|in:owner_provides,sitter_provides,flexible,owner_provided,sitter_provided',
            'bio'                   => 'nullable|string|max:2000',
            'sitter_type'           => 'nullable|in:small_pets,large_pets,exotic_pets,all_pets',
            'preferred_pet_sizes'   => 'nullable|array',
            'preferred_pet_sizes.*' => 'in:small,medium,large,giant',
            'min_pets_capacity'     => 'nullable|integer|min:0|max:20',
            'max_pets_capacity'     => 'nullable|integer|min:1|max:20',
        ]);

        $sitterProfile = $user->sitterProfile;

        if (!$sitterProfile) {
            return back()->with('error', 'No sitter profile found.');
        }

        $sitterProfile->update([
            'base_rate'           => $validated['base_rate'] ?? $sitterProfile->base_rate,
            'food_preference'     => $validated['food_preference'] ?? $sitterProfile->food_preference,
            'bio'                 => $validated['bio'] ?? $sitterProfile->bio,
            'sitter_type'         => $validated['sitter_type'] ?? $sitterProfile->sitter_type,
            'preferred_pet_sizes' => $validated['preferred_pet_sizes'] ?? $sitterProfile->preferred_pet_sizes,
            'min_pets_capacity'   => $validated['min_pets_capacity'] ?? $sitterProfile->min_pets_capacity,
            'max_pets_capacity'   => $validated['max_pets_capacity'] ?? $sitterProfile->max_pets_capacity,
        ]);

        return redirect()->route('profile.edit')->with('status', 'sitter-settings-updated');
    }

    /**
     * Update user's preferred language.
     */
    public function updateLanguage(Request $request)
    {
        $request->validate(['locale' => 'required|in:en,fil,es,fr']);

        $locale = $request->input('locale');
        $request->user()->update(['locale' => $locale]);

        app()->setLocale($locale);
        session(['locale' => $locale]);

        return response()->json([
            'success' => true,
            'locale'  => $locale,
            'message' => 'Language updated successfully.',
        ]);
    }

    // ========================================== //
    // PRIVATE HELPERS                            //
    // ========================================== //

    /**
     * Delete an image — Cloudinary (via public_id) or local storage.
     */
    private function deleteOldImage(?string $path, ?string $publicId): void
    {
        if (!$path) return;

        // Cloudinary image
        if (str_starts_with($path, 'http') && $publicId) {
            $this->cloudinary->delete($publicId);
            return;
        }

        // Local storage image
        if (!str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Download a remote URL to a local temp file.
     * Used for Python scripts that need local paths.
     */
    private function downloadToTemp(string $url): string
    {
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'petnanny_' . uniqid() . '.jpg';

        $content = @file_get_contents($url);
        if ($content === false) {
            throw new \Exception('Failed to download image from ' . $url);
        }

        file_put_contents($tempPath, $content);
        return $tempPath;
    }
}
