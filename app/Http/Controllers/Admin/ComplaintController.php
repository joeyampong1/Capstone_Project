<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['complainant', 'respondent', 'booking'])
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

        $complaint->update([
            'status'      => 'under_review',
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return back()->with('success', 'Complaint marked as under review.');
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'resolution' => 'required|string|max:2000',
        ]);

        $complaint = Complaint::findOrFail($id);

        if (! in_array($complaint->status, ['pending', 'under_review'])) {
            return back()->with('error', 'This complaint cannot be resolved.');
        }

        $complaint->update([
            'status'      => 'resolved',
            'resolution'  => $request->input('resolution'),
            'admin_notes' => $request->input('admin_notes'),
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Complaint resolved successfully.');
    }

    public function dismiss(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        if (! in_array($complaint->status, ['pending', 'under_review'])) {
            return back()->with('error', 'This complaint cannot be dismissed.');
        }

        $complaint->update([
            'status'      => 'dismissed',
            'admin_notes' => $request->input('admin_notes'),
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Complaint dismissed.');
    }
}