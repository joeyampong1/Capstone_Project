<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Complaint;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    // ==========================================
    // INDEX — List complaints filed by user
    // ==========================================

    public function index()
    {
        $user = Auth::user();

        $complaints = Complaint::with(['booking.pet', 'booking.owner', 'booking.sitter', 'complainant', 'respondent'])
            ->where('complainant_id', $user->id)
            ->recent()
            ->get();

        // Stats
        $stats = [
            'total'        => $complaints->count(),
            'pending'      => $complaints->where('status', 'pending')->count(),
            'under_review' => $complaints->where('status', 'under_review')->count(),
            'resolved'     => $complaints->where('status', 'resolved')->count(),
            'dismissed'    => $complaints->where('status', 'dismissed')->count(),
        ];

        return view('complaints.index', compact('complaints', 'stats'));
    }

    // ==========================================
    // CREATE — Show complaint form
    // ==========================================

    public function create(Request $request)
    {
        $user = Auth::user();
        $preselectedBookingId = $request->query('booking_id');

        // Bookings involved with the user (as owner or sitter)
        $bookings = Booking::with(['pet.petType', 'owner', 'sitter'])
            ->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('sitter_id', $user->id);
            })
            ->whereIn('status', ['accepted', 'completed'])
            ->orderByDesc('created_at')
            ->get();

        return view('complaints.create_complaints', compact('bookings', 'preselectedBookingId'));
    }

    // ==========================================
    // STORE — Save new complaint
    // ==========================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id'      => 'required|exists:bookings,id',
            'complaint_type'  => 'required|in:missed_visit,poor_service,no_proof,rude_behavior,others',
            'description'     => 'required|string|max:3000',
            'evidence'        => 'nullable|array|max:10',
            'evidence.*'      => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,pdf,doc,docx|max:10240',
        ]);

        $user = Auth::user();

        // Load booking
        $booking = Booking::with(['owner', 'sitter'])
            ->findOrFail($validated['booking_id']);

        // Verify the user is involved in this booking
        abort_if(
            $booking->owner_id !== $user->id && $booking->sitter_id !== $user->id,
            403,
            'Unauthorized — you are not part of this booking.'
        );

        // ✅ FIX ISSUE #33: Ensure booking status is valid (prevent backend bypass)
        abort_if(
            !in_array($booking->status, ['accepted', 'completed']),
            400,
            'You can only file a complaint for accepted or completed bookings.'
        );

        // Determine the respondent (counterpart)
        $respondentId = $booking->owner_id === $user->id
            ? $booking->sitter_id
            : $booking->owner_id;

        // Prevent duplicate complaints for the same booking
        $existing = Complaint::where('booking_id', $booking->id)
            ->where('complainant_id', $user->id)
            ->whereIn('status', ['pending', 'under_review'])
            ->exists();

        if ($existing) {
            return back()
                ->withErrors(['booking_id' => 'You already have an active complaint for this booking.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Handle file uploads
            $evidencePaths = [];
            if ($request->hasFile('evidence')) {
                foreach ($request->file('evidence') as $file) {
                    // ✅ FIX ISSUE #33: Store in 'local' (private) disk instead of 'public'
                    $evidencePaths[] = $file->store('complaints/evidence', 'local');
                }
            }

            $complaint = Complaint::create([
                'booking_id'     => $booking->id,
                'complainant_id' => $user->id,
                'respondent_id'  => $respondentId,
                'complaint_type' => $validated['complaint_type'],
                'description'    => $validated['description'],
                'evidence_paths' => $evidencePaths ?: null,
                'status'         => 'pending',
            ]);

            // Notify admins
            NotificationService::notifyAdmins(
                type:      'complaint_filed',
                title:     'New complaint filed',
                message:   "{$user->f_name} filed a {$complaint->type_label} complaint against {$complaint->respondent->f_name}.",
                actionUrl: route('admin.complaints'),
                meta:      ['complaint_id' => $complaint->id],
            );

            DB::commit();

            return redirect()
                ->route('complaints.index')
                ->with('status', 'Complaint submitted successfully. Our team will review it shortly.');
        } catch (\Exception $e) {
            DB::rollBack();
            // ✅ FIX ISSUE #31
            Log::error('Complaint Store Error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => __('messages.db_error_friendly')])
                ->withInput();
        }
    }

    // ==========================================
    // SHOW — View single complaint
    // ==========================================

    public function show(Complaint $complaint)
    {
        // Security: complainant, respondent, or admin only
        $user = Auth::user();
        abort_if(
            $complaint->complainant_id !== $user->id
            && $complaint->respondent_id !== $user->id
            && !$user->isAdmin(),
            403,
            'Unauthorized.'
        );

        $complaint->load(['booking.pet', 'complainant', 'respondent']);

        return view('complaints.show_complaint', compact('complaint'));
    }

    // ==========================================
    // VIEW EVIDENCE — Serve private files securely
    // ==========================================

    public function viewEvidence(Complaint $complaint, $index)
    {
        $user = Auth::user();

        // Security: Ensure the requester is authorized to view this complaint
        abort_if(
            $complaint->complainant_id !== $user->id
            && $complaint->respondent_id !== $user->id
            && !$user->isAdmin(),
            403,
            'Unauthorized.'
        );

        $paths = $complaint->evidence_paths ?? [];

        if (!isset($paths[$index])) {
            abort(404, 'Evidence not found.');
        }

        $path = $paths[$index];

        // Check if file exists in private local disk
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File no longer exists.');
        }

        // Return the file response securely
        return Storage::disk('local')->response($path);
    }
}
