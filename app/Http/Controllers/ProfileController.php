<?php

namespace App\Http\Controllers;
use App\Http\Requests\ProfileUpdateRequest;
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
    // Update Profile Photo                       //
    // ========================================== //
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048', // Max 2MB
        ]);

        $user = $request->user();

        // Delete old photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->profile_photo = $path;
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
            // ------------------------------------------------------------
            // REQUEST DEBUG
            // ------------------------------------------------------------
            \Log::info('=== upload_id START ===', [
                'user_id'   => $user->id,
                'has_file'  => $request->hasFile('gov_id_path'),
                'files'     => array_keys($request->allFiles()),
                'input'     => array_keys($request->all()),
                'action'    => $request->input('action'),
                'id_type'   => $request->input('id_type'),
            ]);

            // ------------------------------------------------------------
            // VALIDATION
            // ------------------------------------------------------------
            $validator = \Validator::make($request->all(), [
                'gov_id_path' => 'required|file|mimes:jpg,jpeg,png|max:5120',
                'id_type'     => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                \Log::error('=== VALIDATION FAILED ===', [
                    'errors' => $validator->errors()->toArray(),
                ]);

                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            \Log::info('Validation passed');

            // ------------------------------------------------------------
            // SAVE ID TYPE (kung gi-submit)
            // ------------------------------------------------------------
            if ($request->filled('id_type')) {
                $user->id_type = $request->input('id_type');
                \Log::info('ID type saved', ['id_type' => $user->id_type]);
            }

            // ------------------------------------------------------------
            // DELETE OLD FILE
            // ------------------------------------------------------------
            if ($user->gov_id_path) {
                \Log::info('Deleting old file', ['old_path' => $user->gov_id_path]);

                if (Storage::disk('public')->exists($user->gov_id_path)) {
                    Storage::disk('public')->delete($user->gov_id_path);
                    \Log::info('Old file deleted');
                } else {
                    \Log::warning('Old file not found on disk', ['path' => $user->gov_id_path]);
                }
            }

            // ------------------------------------------------------------
            // STORE NEW FILE
            // ------------------------------------------------------------
            try {
                $path = $request->file('gov_id_path')->store('ids', 'public');
            } catch (\Exception $e) {
                \Log::error('=== FILE STORE FAILED ===', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return back()->withErrors(['gov_id_path' => 'Failed to store file: ' . $e->getMessage()]);
            }

            $user->gov_id_path = $path;

            $absolutePath = storage_path("app/public/{$path}");

            \Log::info('File stored', [
                'path'         => $path,
                'absolute'     => $absolutePath,
                'exists'       => file_exists($absolutePath),
                'size'         => file_exists($absolutePath) ? filesize($absolutePath) : 0,
                'mime'         => file_exists($absolutePath) ? mime_content_type($absolutePath) : 'unknown',
            ]);

            // ------------------------------------------------------------
            // RUN OCR
            // ------------------------------------------------------------
            \Log::info('Starting OCR...');

            try {
                $ocrResult = $ocr->extractIdDetails($path);

                \Log::info('=== OCR COMPLETED ===', [
                    'success'    => $ocrResult['success'] ?? false,
                    'error'      => $ocrResult['error'] ?? null,
                    'name'       => $ocrResult['name'] ?? null,
                    'id_number'  => $ocrResult['id_number'] ?? null,
                    'dob'        => $ocrResult['dob'] ?? null,
                    'raw_length' => isset($ocrResult['raw_text']) ? strlen($ocrResult['raw_text']) : 0,
                ]);

                $user->ocr_result = $ocrResult;

            } catch (\Exception $e) {
                \Log::error('=== OCR EXCEPTION ===', [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'trace'   => $e->getTraceAsString(),
                ]);

                $user->ocr_result = [
                    'success' => false,
                    'error'   => 'OCR service unavailable: ' . $e->getMessage(),
                ];
            } catch (\Throwable $e) {
                \Log::error('=== OCR FATAL ERROR ===', [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'trace'   => $e->getTraceAsString(),
                ]);

                $user->ocr_result = [
                    'success' => false,
                    'error'   => 'Fatal error: ' . $e->getMessage(),
                ];
            }

            // ------------------------------------------------------------
            // SAVE USER
            // ------------------------------------------------------------
            try {
                $user->save();

                \Log::info('=== USER SAVED ===', [
                    'user_id'     => $user->id,
                    'gov_id_path' => $user->gov_id_path,
                    'id_type'     => $user->id_type,
                    'ocr_success' => $user->ocr_result['success'] ?? false,
                ]);

            } catch (\Exception $e) {
                \Log::error('=== USER SAVE FAILED ===', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return back()->withErrors(['error' => 'Failed to save user data.']);
            }

            \Log::info('=== upload_id END ===');

            return redirect()->route('profile.edit')->with('status', 'id-updated');
        }

        // ==========================================
        // Step 3: Upload selfie
        // ==========================================
        if ($action === 'upload_selfie') {
            $request->validate(['selfie_photo' => 'required|file|mimes:jpg,jpeg,png|max:5120']);

            if ($user->selfie_photo) {
                Storage::disk('public')->delete($user->selfie_photo);
            }

            $path = $request->file('selfie_photo')->store('selfies', 'public');
            $user->selfie_photo = $path;
            $user->id_validation_status = 'unverified';
            $user->save();

            return redirect()->route('profile.edit')->with('status', 'id-updated');
        }

        // ==========================================
        // Step 4: Submit for verification (face match)
        // ==========================================
        if ($action === 'submit_verification') {
            // CHECK PROFILE COMPLETENESS
            $missingFields = [];

            if (empty($user->f_name))         $missingFields[] = 'First Name';
            if (empty($user->l_name))         $missingFields[] = 'Last Name';
            if (empty($user->date_of_birth))  $missingFields[] = 'Date of Birth';
            if (empty($user->gender))         $missingFields[] = 'Gender';
            if (empty($user->contact_number)) $missingFields[] = 'Contact Number';
            if (empty($user->address))        $missingFields[] = 'Address';

            if (! empty($missingFields)) {
                return back()->withErrors([
                    'error' => 'Please complete your profile first: ' . implode(', ', $missingFields),
                ]);
            }

            // ------------------------------------------------------------
            // RUN FACE MATCH
            // ------------------------------------------------------------
            $result = $faceMatch->verify($user->selfie_photo);

            if (! $result['success']) {
                return back()->withErrors([
                    'selfie_photo' => 'Face verification failed: ' . ($result['error'] ?? 'unknown error'),
                ]);
            }

            // ------------------------------------------------------------
            // STORE FACE MATCH RESULTS
            // ------------------------------------------------------------
            $user->face_match_score        = $result['confidence'];
            $user->face_detected_on_id     = true;
            $user->face_detected_on_selfie = true;
            $user->id_expired              = false;

            // ------------------------------------------------------------
            // NAME MATCH — compare OCR name vs user profile name
            // ------------------------------------------------------------
            $ocrName   = strtoupper($user->ocr_result['name'] ?? '');
            $userLast  = strtoupper($user->l_name ?? '');
            $userFirst = strtoupper($user->f_name ?? '');

            $nameMatch = false;
            if ($ocrName && ($userLast || $userFirst)) {
                // Check kung naa ang last name o first name sa OCR text
                if ($userLast && str_contains($ocrName, $userLast)) {
                    $nameMatch = true;
                } elseif ($userFirst && str_contains($ocrName, $userFirst)) {
                    $nameMatch = true;
                }
            }
            $user->name_match = $nameMatch ? 'matched' : 'not_matched';

            \Log::info('=== NAME MATCH ===', [
                'ocr_name'   => $ocrName,
                'user_last'  => $userLast,
                'user_first' => $userFirst,
                'result'     => $user->name_match,
            ]);

            // ------------------------------------------------------------
            // BIRTHDATE MATCH — compare OCR DOB vs user date_of_birth
            // ------------------------------------------------------------
            $ocrDob  = $user->ocr_result['dob'] ?? null;
            $userDob = $user->date_of_birth ? $user->date_of_birth->format('Y/m/d') : null;

            $birthdateMatch = false;
            if ($ocrDob && $userDob) {
                // Normalize format
                $ocrDobNorm  = str_replace(['-', '.'], '/', $ocrDob);
                $userDobNorm = str_replace(['-', '.'], '/', $userDob);
                $birthdateMatch = ($ocrDobNorm === $userDobNorm);
            }
            $user->birthdate_match = ($ocrDob && $userDob) ? ($birthdateMatch ? 'matched' : 'not_matched') : null;

            \Log::info('=== BIRTHDATE MATCH ===', [
                'ocr_dob'  => $ocrDob,
                'user_dob' => $userDob,
                'result'   => $user->birthdate_match,
            ]);

            // ------------------------------------------------------------
            // Placeholder for future features
            // ------------------------------------------------------------
            $user->document_authenticity = null;
            $user->liveness_detection    = null;

            // ------------------------------------------------------------
            // HANDLE MATCH / NO MATCH
            // ------------------------------------------------------------
            if (! $result['match']) {
                $user->id_validation_status = 'rejected';
                $user->save();

                return back()->withErrors([
                    'selfie_photo' => 'Face does not match the ID. Distance: ' . $result['distance'],
                ]);
            }

            // Match! Queue for admin review
            $user->id_validation_status = 'pending';
            $user->save();

            \Log::info('=== SUBMIT VERIFICATION ===', [
                'user_id'        => $user->id,
                'face_score'     => $user->face_match_score,
                'name_match'     => $user->name_match,
                'birthdate_match'=> $user->birthdate_match,
                'status'         => $user->id_validation_status,
            ]);

            return redirect()->route('profile.edit')->with('status', 'submitted');
        }

        return back()->withErrors(['error' => 'Invalid action.']);
    }

    // ========================================== //
    // Toggle Sitter Mode                         //
    // ========================================== //
    /**
     * Toggle Sitter Mode using the existing is_sitter boolean.
     * Only available for approved sitters.
     */
    public function toggleSitterMode()
    {
        $user = auth()->user();

        // Only users who were approved as sitters can toggle
        if ($user->sitter_status !== 'approved') {
            return back()->with('error', 'Only approved pet sitters can toggle Sitter Mode.');
        }

        $user->update([
            'is_sitter' => ! $user->is_sitter,
        ]);

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
    /**
     * Update sitter-specific settings (rate, food preference, etc.)
     * Only available for approved sitters.
     */
    public function updateSitterSettings(Request $request)
    {
        $user = $request->user();

        if ($user->sitter_status !== 'approved') {
            return back()->with('error', 'Only approved pet sitters can update these settings.');
        }

        $validated = $request->validate([
            'base_rate'        => 'nullable|numeric|min:0|max:99999',
            'food_preference'  => 'nullable|in:owner_provides,sitter_provides,flexible,owner_provided,sitter_provided',
            'bio'              => 'nullable|string|max:2000',
            'sitter_type'      => 'nullable|in:small_pets,large_pets,exotic_pets,all_pets',
            'preferred_pet_sizes' => 'nullable|array',
            'preferred_pet_sizes.*' => 'in:small,medium,large,giant',
            'min_pets_capacity' => 'nullable|integer|min:0|max:20',
            'max_pets_capacity' => 'nullable|integer|min:1|max:20',
        ]);

        // I-save sa sitterProfile — dili sa users table
        $sitterProfile = $user->sitterProfile;

        if (!$sitterProfile) {
            return back()->with('error', 'No sitter profile found.');
        }

        $sitterProfile->update([
            'base_rate'         => $validated['base_rate'] ?? $sitterProfile->base_rate,
            'food_preference'   => $validated['food_preference'] ?? $sitterProfile->food_preference,
            'bio'               => $validated['bio'] ?? $sitterProfile->bio,
            'sitter_type'       => $validated['sitter_type'] ?? $sitterProfile->sitter_type,
            'preferred_pet_sizes' => $validated['preferred_pet_sizes'] ?? $sitterProfile->preferred_pet_sizes,
            'min_pets_capacity' => $validated['min_pets_capacity'] ?? $sitterProfile->min_pets_capacity,
            'max_pets_capacity' => $validated['max_pets_capacity'] ?? $sitterProfile->max_pets_capacity,
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('status', 'sitter-settings-updated');
    }

    /**
     * Update user's preferred language.
     */
    public function updateLanguage(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:en,fil,es,fr',
        ]);

        $locale = $request->input('locale');

        $request->user()->update([
            'locale' => $locale,
        ]);

        // Apply instantly for the current request
        app()->setLocale($locale);
        session(['locale' => $locale]);

        return response()->json([
            'success' => true,
            'locale'  => $locale,
            'message' => 'Language updated successfully.',
        ]);
    }
}
