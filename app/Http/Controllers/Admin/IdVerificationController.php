<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class IdVerificationController extends Controller
{
    public function index()
    {
        $verifications = User::query()
            ->whereNotNull('gov_id_path')
            ->latest()
            ->paginate(10);

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

        $user->update([
            'id_validation_status' => 'verified',
            'admin_notes'          => $request->input('admin_notes'),
            'id_reviewed_by'       => auth()->id(),
            'id_reviewed_at'       => now(),
        ]);

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

        $user->update([
            'id_validation_status' => 'rejected',
            'admin_notes'          => $request->input('admin_notes'),
            'id_reviewed_by'       => auth()->id(),
            'id_reviewed_at'       => now(),
        ]);

        Log::info('User ID verification rejected', [
            'user_id'  => $user->id,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success', 'Verification rejected. User will be asked to resubmit.');
    }

    public function download($id)
    {
        $user = User::findOrFail($id);

        $files = array_filter([
            'gov_id' => $user->gov_id_path,
            'selfie' => $user->selfie_photo,
        ]);

        if (empty($files)) {
            return back()->with('error', 'No documents found for this user.');
        }

        if (count($files) === 1) {
            $path = reset($files);
            if (! Storage::disk('public')->exists($path)) {
                return back()->with('error', 'File not found on storage.');
            }
            return Storage::disk('public')->download($path);
        }

        $zipName = 'verification_user_' . $user->id . '_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/tmp/' . $zipName);
        if (! is_dir(dirname($zipPath))) mkdir(dirname($zipPath), 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Unable to create ZIP archive.');
        }

        foreach ($files as $label => $path) {
            if (Storage::disk('public')->exists($path)) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $zip->addFromString($label . '.' . $ext, Storage::disk('public')->get($path));
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
