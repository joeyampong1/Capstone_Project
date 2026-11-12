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
        $user = $request->user();

        // FIX ISSUE #41: Google users don't have a password, so bypass validation
        if (!$user->google_id && $user->password) {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);
        }

        Auth::logout();

        // ✅ ANONYMIZATION: Wipe out PII (Personal Identifiable Information) before soft deleting
        // This maintains financial/transactional integrity (bookings still have a valid user_id)
        // while complying with Data Privacy (the real person can no longer be identified).
        $user->update([
            'f_name'                  => 'Deleted',
            'm_name'                  => null,
            'l_name'                  => 'User',
            'email'                   => 'deleted_' . $user->id . '_' . uniqid() . '@petnanny.local',
            'contact_number'          => null,
            'address'                 => null,
            'location'                => null,
            'latitude'                => null,
            'longitude'               => null,
            'date_of_birth'           => null,
            'gender'                  => null,
            'profile_photo'           => null,
            'profile_photo_public_id' => null,
            'cover_photo'             => null,
            'cover_photo_public_id'   => null,
            'gov_id_path'             => null,
            'gov_id_public_id'        => null,
            'selfie_photo'            => null,
            'selfie_photo_public_id'  => null,
            'google_id'               => null,
            'password'                => null,
            'fcm_token'               => null,
        ]);

        $user->delete(); // Soft delete

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'Your account and personal data have been permanently removed.');
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

            // Save ID type if submitted
            if ($request->filled('id_type')) {
                $user->id_type = $request->input('id_type');
            }

            // Delete old ID (Cloudinary o local)
            $this->deleteOldImage($user->gov_id_path, $user->gov_id_public_id);

            // ✅ UPLOAD TO CLOUDINARY — authenticated type
            $upload = $this->cloudinary->upload($request->file('gov_id_path'), 'ids', ['type' => 'authenticated']);

            if (!$upload['success']) {
                \Log::error('Cloudinary ID upload failed', ['error' => $upload['error']]);
                return back()->withErrors(['gov_id_path' => 'Upload failed: ' . $upload['error']]);
            }

            // ✅ SAVE TO USER MODEL
            $user->gov_id_path      = $upload['public_id'];
            $user->gov_id_public_id = $upload['public_id'];

            // ✅ FIX ISSUE #45: Reset verification if a new ID is uploaded
            if ($user->id_validation_status === 'verified') {
                $user->id_validation_status = 'unverified';
                $user->face_match_score     = null;
                $user->name_match           = null;
                $user->birthdate_match      = null;
            }

            \Log::info('ID uploaded to Cloudinary', [
                'url'       => $upload['url'],
                'public_id' => $upload['public_id'],
            ]);

            // ------------------------------------------------------------
            // RUN OCR — save temp local file for OCR service
            // ------------------------------------------------------------
            $tempStoragePath = 'temp/' . uniqid() . '.jpg';

            try {
                $localTempPath = $request->file('gov_id_path')->getRealPath();

                Storage::disk('public')->put($tempStoragePath, file_get_contents($localTempPath));

                $ocrResult = $ocr->extractIdDetails($tempStoragePath);

                \Log::info('=== OCR COMPLETED ===', [
                    'user_id'   => $user->id,
                    'success'   => $ocrResult['success'] ?? false,
                    'has_name'  => !empty($ocrResult['name']),
                    'has_id_no' => !empty($ocrResult['id_number']),
                    'has_dob'   => !empty($ocrResult['dob']),
                ]);

                $user->ocr_result = $ocrResult;

            } catch (\Exception $e) {
                \Log::error('OCR exception', [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]);
                $user->ocr_result = [
                    'success' => false,
                    'error'   => 'OCR failed: ' . $e->getMessage(),
                ];
            } finally {
                // Cleanup temp file
                if (Storage::disk('public')->exists($tempStoragePath)) {
                    Storage::disk('public')->delete($tempStoragePath);
                }
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

            // ✅ UPLOAD TO CLOUDINARY — authenticated type
            $upload = $this->cloudinary->upload($request->file('selfie_photo'), 'selfies', ['type' => 'authenticated']);

            if (!$upload['success']) {
                return back()->withErrors(['selfie_photo' => 'Upload failed: ' . $upload['error']]);
            }

            // ✅ SAVE TO USER MODEL
            $user->selfie_photo           = $upload['public_id'];
            $user->selfie_photo_public_id = $upload['public_id'];

            // ✅ FIX ISSUE #45: Reset verification if a new Selfie is uploaded
            $user->id_validation_status   = 'unverified';
            $user->face_match_score       = null;
            $user->name_match             = null;
            $user->birthdate_match        = null;

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

            $result = null;
            $localIdPath = null;
            $localSelfiePath = null;
            $tempIdStoragePath = 'temp/ids/' . uniqid() . '.jpg';
            $tempSelfieStoragePath = 'temp/selfies/' . uniqid() . '.jpg';

            try {
                // 1. Download ID Image
                $signedIdUrl = $this->cloudinary->getSignedUrl($user->gov_id_public_id);
                if (!$signedIdUrl) throw new \Exception('Failed to generate secure URL for ID.');

                $localIdPath = $this->downloadToTemp($signedIdUrl);
                Storage::disk('public')->put($tempIdStoragePath, file_get_contents($localIdPath));

                // 2. Download Selfie Image
                $signedSelfieUrl = $this->cloudinary->getSignedUrl($user->selfie_photo_public_id);
                if (!$signedSelfieUrl) throw new \Exception('Failed to generate secure URL for selfie.');

                $localSelfiePath = $this->downloadToTemp($signedSelfieUrl);
                Storage::disk('public')->put($tempSelfieStoragePath, file_get_contents($localSelfiePath));

                // 3. Compare BOTH faces
                $result = $faceMatch->verify($tempIdStoragePath, $tempSelfieStoragePath);

            } catch (\Exception $e) {
                // If API fails, log the error and proceed to manual review.
                // Do not block the submission.
                \Log::error('Face match exception, proceeding to manual review', ['error' => $e->getMessage()]);
            } finally {
                // Cleanup temp files
                if ($localIdPath && file_exists($localIdPath)) @unlink($localIdPath);
                if ($localSelfiePath && file_exists($localSelfiePath)) @unlink($localSelfiePath);
                if (Storage::disk('public')->exists($tempIdStoragePath)) Storage::disk('public')->delete($tempIdStoragePath);
                if (Storage::disk('public')->exists($tempSelfieStoragePath)) Storage::disk('public')->delete($tempSelfieStoragePath);
            }

            // Store face match results (if available)
            if ($result && $result['success']) {
                $user->face_match_score        = $result['confidence'];
                $user->face_detected_on_id     = true;
                $user->face_detected_on_selfie = true;
                $user->id_expired              = false;
            } else {
                $user->face_match_score = null; // Null if API fails
            }

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

            // Do not auto-reject on face match failure.
            // Set to pending for manual admin review.
            $user->id_validation_status = 'pending';
            $user->save();

            \Log::info('=== SUBMIT VERIFICATION ===', [
                'user_id'   => $user->id,
                'status'    => $user->id_validation_status,
                'face_score'=> $user->face_match_score,
            ]);

            // Send email notification to user upon successful submission
             try {
                \App\Services\NotificationService::idVerificationSubmitted($user);
            } catch (\Throwable $e) {
                \Log::error('Failed to send ID submission notification: ' . $e->getMessage());
            }

            return redirect()->route('profile.edit')->with('status', 'submitted');
        }

        // ==========================================
        // Step 5: Reset Verification (Retry Button)
        // ==========================================
        if ($action === 'reset_verification') {
            $user->id_validation_status = 'unverified';

            $user->gov_id_path = null;
            $user->gov_id_public_id = null;
            $user->selfie_photo = null;
            $user->selfie_photo_public_id = null;
            $user->ocr_result = null;

            $user->face_match_score     = null;
            $user->name_match           = null;
            $user->birthdate_match      = null;
            $user->save();

            return redirect()->route('profile.edit')->with('status', 'verification-reset');
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

        // USED DIRECT ASSIGNMENT
        $user->is_sitter = !$user->is_sitter;
        $user->save();

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

        if ($publicId) {
            $this->cloudinary->delete($publicId);
            return;
        }

        // Fallback for old local storage images
        if ($path && !str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
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
