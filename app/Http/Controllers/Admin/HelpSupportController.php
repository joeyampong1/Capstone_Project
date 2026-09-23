<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class HelpSupportController extends Controller
{
    /**
     * Show Help & Support page with FAQ, guides, and the admin's ticket history.
     */
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->latest()
            ->get();

        $stats = [
            'total'       => $tickets->count(),
            'open'        => $tickets->where('status', 'open')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'resolved'    => $tickets->where('status', 'resolved')->count(),
        ];

        return view('admin.help_support.index', compact('tickets', 'stats'));
    }

    /**
     * Save new support ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'concern_type' => 'required|string|max:100',
            'priority'     => 'required|in:Low,Normal,High,Critical',
            'subject'      => 'required|string|max:200',
            'description'  => 'required|string|max:5000',
        ]);

        SupportTicket::create([
            'user_id'      => auth()->id(),
            'ticket_code'  => SupportTicket::generateCode(),
            'concern_type' => $validated['concern_type'],
            'priority'     => strtolower($validated['priority']),
            'subject'      => $validated['subject'],
            'description'  => $validated['description'],
            'status'       => 'open',
        ]);

        return redirect()
            ->route('admin.help_support')
            ->with('success', 'Your ticket has been submitted. Support team will get back to you shortly.');
    }
}