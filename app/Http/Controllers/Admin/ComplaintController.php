<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['complainant', 'respondent', 'booking', 'resolver'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total'        => Complaint::count(),
            'pending'      => Complaint::where('status', 'pending')->count(),
            'under_review' => Complaint::where('status', 'under_review')->count(),
            'resolved'     => Complaint::where('status', 'resolved')->count(),
            'dismissed'    => Complaint::where('status', 'dismissed')->count(),
        ];

        return view('admin.complaints.complaints_management', compact('complaints', 'stats'));
    }

    public function markReview(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        if ($complaint->status !== 'pending') {
            return back()->with('error', 'Only pending complaints can be marked as under review.');
        }

        try {
            $complaint->update([
                'status'      => 'under_review',
                'admin_notes' => $request->input('admin_notes'),
            ]);

            return back()->with('success', 'Complaint marked as under review.');
        } catch (\Exception $e) {
            Log::error('Complaint MarkReview Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'resolution' => 'required|string|max:2000',
        ]);

        $complaint = Complaint::with(['complainant', 'respondent'])->findOrFail($id);

        if (! in_array($complaint->status, ['pending', 'under_review'])) {
            return back()->with('error', 'This complaint cannot be resolved.');
        }

        try {
            $resolutionText = $request->input('resolution');

            $complaint->update([
                'status'      => 'resolved',
                'resolution'  => $resolutionText,
                'admin_notes' => $request->input('admin_notes'),
                'resolved_by' => Auth::id(),
                'resolved_at' => now(),
            ]);

            // ✅ FIX ISSUE #44: Notify both parties
            if ($complaint->complainant) {
                NotificationService::complaintResolved($complaint->complainant, $resolutionText);
            }
            if ($complaint->respondent) {
                NotificationService::complaintResolved($complaint->respondent, $resolutionText);
            }

            return back()->with('success', 'Complaint resolved successfully.');
        } catch (\Exception $e) {
            Log::error('Complaint Resolve Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }

    public function dismiss(Request $request, $id)
    {
        $complaint = Complaint::with(['complainant', 'respondent'])->findOrFail($id);

        if (! in_array($complaint->status, ['pending', 'under_review'])) {
            return back()->with('error', 'This complaint cannot be dismissed.');
        }

        try {
            $complaint->update([
                'status'      => 'dismissed',
                'admin_notes' => $request->input('admin_notes'),
                'resolved_by' => Auth::id(),
                'resolved_at' => now(),
            ]);

            // ✅ FIX ISSUE #44: Notify both parties that the complaint was dismissed
            $dismissMessage = __('messages.complaint_dismissed_message') ?? 'The complaint has been dismissed by the admin.';

            if ($complaint->complainant) {
                NotificationService::complaintResolved($complaint->complainant, $dismissMessage);
            }
            if ($complaint->respondent) {
                NotificationService::complaintResolved($complaint->respondent, $dismissMessage);
            }

            return back()->with('success', 'Complaint dismissed.');
        } catch (\Exception $e) {
            Log::error('Complaint Dismiss Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }
}
