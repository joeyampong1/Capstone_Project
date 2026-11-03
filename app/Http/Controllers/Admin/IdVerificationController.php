<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Services\CloudinaryService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class IdVerificationController extends Controller
{
    public function __construct(
        private CloudinaryService $cloudinary
    ) {}

    public function index()
    {
        $verifications = User::query()
            ->whereNotNull('gov_id_path')
            ->latest()
            ->paginate(10);

        // Generate Signed URLs for authenticated Cloudinary assets
        $verifications->getCollection()->transform(function ($user) {
            $user->gov_id_signed_url = $this->cloudinary->getSignedUrl($user->gov_id_public_id);
            $user->selfie_signed_url = $this->cloudinary->getSignedUrl($user->selfie_photo_public_id);
            return $user;
        });

        $stats = [
            'pending'  => User::whereNotNull('gov_id_path')->where('id_validation_status', 'pending')->count(),
            'verified' => User::whereNotNull('gov_id_path')->where('id_validation_status', 'verified')->count(),
            'rejected' => User::whereNotNull('gov_id_path')->where('id_validation_status', 'rejected')->count(),
            'total'    => User::whereNotNull('gov_id_path')->count(),
        ];

        return view('admin.id_verification.id_verification', compact('verifications', 'stats'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:1000']);

        $user = User::findOrFail($id);

        if ($user->id_validation_status === 'verified') {
            return back()->with('error', 'This user is already verified.');
        }

        // ✅ DIRECT ASSIGNMENT
        $user->id_validation_status = 'verified';
        $user->admin_notes          = $request->input('admin_notes');
        $user->id_reviewed_by       = auth()->id();
        $user->id_reviewed_at       = now();
        $user->save();

        NotificationService::idVerified($user);

        Log::info('User ID verification approved', [
            'user_id'  => $user->id,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success', 'Verification approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:1000']);

        $user = User::findOrFail($id);

        if ($user->id_validation_status === 'rejected') {
            return back()->with('error', 'This user is already rejected.');
        }

        // ✅ DIRECT ASSIGNMENT
        $user->id_validation_status = 'rejected';
        $user->admin_notes          = $request->input('admin_notes');
        $user->id_reviewed_by       = auth()->id();
        $user->id_reviewed_at       = now();
        $user->save();

        NotificationService::idRejected($user, $request->input('admin_notes') ?? '');

        Log::info('User ID verification rejected', [
            'user_id'  => $user->id,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success', 'Verification rejected. User will be asked to resubmit.');
    }

    public function download($id)
    {
        $user = User::findOrFail($id);

        // Generate Signed URLs for the authenticated files
        $govIdUrl  = $this->cloudinary->getSignedUrl($user->gov_id_public_id);
        $selfieUrl = $this->cloudinary->getSignedUrl($user->selfie_photo_public_id);

        $files = array_filter([
            'gov_id' => $govIdUrl,
            'selfie' => $selfieUrl,
        ]);

        if (empty($files)) {
            return back()->with('error', 'No documents found for this user.');
        }

        if (count($files) === 1) {
            $label = array_key_first($files);
            $url   = reset($files);

            // Download directly from Cloudinary URL
            $content = @file_get_contents($url);
            if ($content === false) {
                return back()->with('error', 'File not found or link expired.');
            }

            return response($content, 200)
                ->header('Content-Type', 'image/jpeg')
                ->header('Content-Disposition', 'attachment; filename="' . $label . '_' . $user->id . '.jpg"');
        }

        // Multiple files — create ZIP
        $zipName = 'verification_user_' . $user->id . '_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/tmp/' . $zipName);
        if (! is_dir(dirname($zipPath))) mkdir(dirname($zipPath), 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Unable to create ZIP archive.');
        }

        foreach ($files as $label => $url) {
            $content = @file_get_contents($url);
            if ($content !== false) {
                $zip->addFromString($label . '.jpg', $content);
            }
        }
        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function updateOcrName(Request $request, $id)
    {
        $request->validate(['ocr_name' => 'required|string|max:255']);

        $user = User::findOrFail($id);
        $ocr = $user->ocr_result;
        $ocr['name'] = $request->ocr_name;
        $user->ocr_result = $ocr;
        $user->save();

        return response()->json(['success' => true]);
    }
}
