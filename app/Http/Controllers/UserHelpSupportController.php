<?php

namespace App\Http\Controllers;

use App\Models\UserSupportMessage;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class UserHelpSupportController extends Controller
{
    /**
     * Show the Help & Support page with the user's own messages.
     */
    public function index()
    {
        $messages = UserSupportMessage::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('help_support.index', compact('messages'));
    }

    /* ==============================================================
     |  CONTACT SUPPORT
     |==============================================================*/

    public function createContact()
    {
        return view('help_support.support_create');
    }

    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'subject'  => 'required|string|max:200',
            'category' => 'required|string|max:100',
            'message'  => 'required|string|max:5000',
        ]);

        $supportMessage = UserSupportMessage::create([
            'user_id'      => auth()->id(),
            'message_code' => UserSupportMessage::generateCode(),
            'type'         => 'contact',
            'subject'      => $validated['subject'],
            'category'     => $validated['category'],
            'message'      => $validated['message'],
            'status'       => 'open',
        ]);

        // Notify all admins
        NotificationService::supportMessageSubmitted(
            auth()->user(),
            $supportMessage
        );

        return redirect()
            ->route('help_support.index')
            ->with('success', 'Your message has been sent to the support team.');
    }

    /* ==============================================================
     |  REPORT A PROBLEM
     |==============================================================*/

    public function createReport()
    {
        return view('help_support.report_issue_create');
    }

    public function storeReport(Request $request)
    {
        $validated = $request->validate([
            'issue_title'         => 'required|string|max:200',
            'issue_category'      => 'required|string|max:100',
            'description'         => 'required|string|max:5000',
            'steps_to_reproduce'  => 'nullable|string|max:5000',
            'device_info'         => 'nullable|string|max:200',
        ]);

        $supportMessage = UserSupportMessage::create([
            'user_id'            => auth()->id(),
            'message_code'       => UserSupportMessage::generateCode(),
            'type'               => 'report',
            'subject'            => $validated['issue_title'],
            'category'           => $validated['issue_category'],
            'message'            => $validated['description'],
            'steps_to_reproduce' => $validated['steps_to_reproduce'] ?? null,
            'device_info'        => $validated['device_info'] ?? null,
            'status'             => 'open',
        ]);

        // Notify all admins
        NotificationService::supportMessageSubmitted(
            auth()->user(),
            $supportMessage
        );

        return redirect()
            ->route('help_support.index')
            ->with('success', 'Your report has been submitted. Our team will review it shortly.');
    }
}