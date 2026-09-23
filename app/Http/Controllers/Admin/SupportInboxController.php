<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSupportMessage;
use Illuminate\Http\Request;

class SupportInboxController extends Controller
{
    /**
     * List all support messages from users.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $type   = $request->query('type', 'all');
        $search = $request->query('search');

        $query = UserSupportMessage::with('user')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message_code', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('f_name', 'like', "%{$search}%")
                        ->orWhere('l_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $messages = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => UserSupportMessage::count(),
            'open'        => UserSupportMessage::where('status', 'open')->count(),
            'in_progress' => UserSupportMessage::where('status', 'in_progress')->count(),
            'resolved'    => UserSupportMessage::where('status', 'resolved')->count(),
            'contact'     => UserSupportMessage::where('type', 'contact')->count(),
            'report'      => UserSupportMessage::where('type', 'report')->count(),
        ];

        return view('admin.messages.index', compact('messages', 'stats', 'status', 'type', 'search'));
    }

    /**
     * Show single message detail and mark as in-progress if still open.
     */
    public function show($id)
    {
        $message = UserSupportMessage::with(['user', 'repliedBy'])->findOrFail($id);

        if ($message->status === 'open') {
            $message->update(['status' => 'in_progress']);
        }

        return view('admin.messages.show', compact('message'));
    }

    /**
     * Send reply to user's support message.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:5000',
            'action'      => 'required|in:in_progress,resolved',
        ]);

        $message = UserSupportMessage::findOrFail($id);

        $message->update([
            'admin_reply' => $request->input('admin_reply'),
            'status'      => $request->input('action'),
            'replied_by'  => auth()->id(),
            'replied_at'  => now(),
        ]);

        // Notify the user who submitted the support message
        if ($message->user) {
            \App\Services\NotificationService::supportMessageReplied(
                recipient: $message->user,
                admin: auth()->user(),
                supportMessage: $message
            );
        }

        return redirect()
            ->route('admin.messages.show', $id)
            ->with('success', 'Reply sent successfully.');
    }

    /**
     * Delete a message (optional).
     */
    public function destroy($id)
    {
        $message = UserSupportMessage::findOrFail($id);
        $message->delete();

        return redirect()
            ->route('admin.messages.index')
            ->with('success', 'Message deleted.');
    }
}