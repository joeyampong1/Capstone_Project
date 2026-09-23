<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatArchive;
use App\Models\ChatDelete;
use App\Models\ChatReport;
use App\Models\MessageReaction;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index($id)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($id);

        if ($currentUser->id === $otherUser->id) {
            abort(403, 'You cannot message yourself.');
        }

        $roomId = ChatMessage::makeRoomId($currentUser->id, $otherUser->id);

        $messages = ChatMessage::with(['replyTo.sender', 'reactions'])
            ->where('room_id', $roomId)
            ->orderBy('created_at', 'asc')
            ->get();

        ChatMessage::where('room_id', $roomId)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $messagesJson = $this->formatMessages($messages, $currentUser);

        return view('messages.create_message', compact('otherUser', 'messages', 'roomId', 'messagesJson'));
    }

        public function store(Request $request, $id)
        {
            $request->validate([
                'message' => 'nullable|string|max:5000',
                'reply_to_id' => 'nullable|exists:chat_messages,id',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:20480',
            ]);

            $currentUser = Auth::user();
            $otherUser = User::findOrFail($id);
            $roomId = ChatMessage::makeRoomId($currentUser->id, $otherUser->id);

            $messageText = trim((string) $request->input('message', ''));
            $attachmentPath = null;
            $messageType = 'text';

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentPath = $file->store('chat_attachments', 'public');
                $messageType = in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif'], true)
                    ? 'image'
                    : 'text';
            }

            if ($messageText === '' && $attachmentPath === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a message or choose an attachment.',
                ], 422);
            }

            $msg = ChatMessage::create([
                'room_id' => $roomId,
                'sender_id' => $currentUser->id,
                'receiver_id' => $otherUser->id,
                'message' => $messageText !== '' ? $messageText : 'Attachment',
                'message_type' => $messageType,
                'reply_to_id' => $request->reply_to_id,
                'attachment_path' => $attachmentPath,
                'is_read' => false,
            ]);

            $msg->load(['replyTo.sender', 'reactions']);

            // ============================================
            // INSERT NOTIFICATION 
            // ============================================
            if ($otherUser->id !== $currentUser->id) {
                // Preview text — kung attachment lang walay text
                $preview = $messageText !== ''
                    ? $messageText
                    : ($messageType === 'image' ? '📷 Sent a photo' : '📎 Sent an attachment');

                NotificationService::newMessage(
                    recipient: $otherUser,
                    sender: $currentUser,
                    preview: $preview,
                );
            }
            // ============================================

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $this->formatSingleMessage($msg, $currentUser),
                ]);
            }

            return redirect()->route('owner.messages', $id);
        }
    public function fetch(Request $request, $id)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($id);
        $roomId = ChatMessage::makeRoomId($currentUser->id, $otherUser->id);

        $lastId = $request->query('last_id', 0);

        $newMessages = ChatMessage::with(['replyTo.sender', 'reactions'])
            ->where('room_id', $roomId)
            ->where('id', '>', $lastId)
            ->orderBy('created_at', 'asc')
            ->get();

        ChatMessage::where('room_id', $roomId)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'messages' => $this->formatMessages($newMessages, $currentUser),
            'count' => $newMessages->count(),
        ]);
    }

    public function react(Request $request, $id)
    {
        $request->validate([
            'message_id' => 'required|exists:chat_messages,id',
            'emoji' => 'required|string|max:10',
        ]);

        $currentUser = Auth::user();

        $existing = MessageReaction::where('message_id', $request->message_id)
            ->where('user_id', $currentUser->id)
            ->first();

        if ($existing && $existing->emoji === $request->emoji) {
            $existing->delete();
            return response()->json(['success' => true, 'action' => 'removed']);
        }

        if ($existing) {
            $existing->update(['emoji' => $request->emoji]);
            return response()->json(['success' => true, 'action' => 'updated']);
        }

        MessageReaction::create([
            'message_id' => $request->message_id,
            'user_id' => $currentUser->id,
            'emoji' => $request->emoji,
        ]);

        return response()->json(['success' => true, 'action' => 'added']);
    }

    public function typing(Request $request, $id)
    {
        return response()->json(['status' => 'ok']);
    }

    // ============================================================
    // ✅ NEW: Mark all messages in room as read
    // ============================================================
    public function markAsRead($id)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($id);
        $roomId = ChatMessage::makeRoomId($currentUser->id, $otherUser->id);

        ChatMessage::where('room_id', $roomId)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Marked as read.']);
    }

    // ============================================================
    // ✅ NEW: Archive conversation
    // ============================================================
    public function archive($id)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($id);
        $roomId = ChatMessage::makeRoomId($currentUser->id, $otherUser->id);

        // Check kung naka-archive na
        $existing = ChatArchive::where('user_id', $currentUser->id)
            ->where('room_id', $roomId)
            ->first();

        if ($existing) {
            // Toggle off – unarchive
            $existing->delete();
            return response()->json(['success' => true, 'message' => 'Conversation unarchived.', 'action' => 'unarchived']);
        }

        // Archive
        ChatArchive::create([
            'user_id' => $currentUser->id,
            'room_id' => $roomId,
        ]);

        // Remove from deleted kung naa
        ChatDelete::where('user_id', $currentUser->id)
            ->where('room_id', $roomId)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Conversation archived.', 'action' => 'archived']);
    }

    // ============================================================
    // ✅ NEW: Delete conversation (hide for user only)
    // ============================================================
    public function deleteConversation($id)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($id);
        $roomId = ChatMessage::makeRoomId($currentUser->id, $otherUser->id);

        ChatDelete::updateOrCreate(
            ['user_id' => $currentUser->id, 'room_id' => $roomId]
        );

        // Remove archive kung naa
        ChatArchive::where('user_id', $currentUser->id)
            ->where('room_id', $roomId)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Conversation deleted.']);
    }

    // ============================================================
    // ✅ NEW: Report user
    // ============================================================
    public function report(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $currentUser = Auth::user();
        $otherUser = User::findOrFail($id);
        $roomId = ChatMessage::makeRoomId($currentUser->id, $otherUser->id);

        // Check kung na-report na ni
        $alreadyReported = ChatReport::where('reporter_id', $currentUser->id)
            ->where('reported_id', $otherUser->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyReported) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reported this user. Our team will review it shortly.',
            ], 429);
        }

        ChatReport::create([
            'reporter_id' => $currentUser->id,
            'reported_id' => $otherUser->id,
            'room_id' => $roomId,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json(['success' => true, 'message' => 'Report submitted. Thank you.']);
    }

    private function formatMessages($messages, $currentUser)
    {
        return $messages->map(function ($m) use ($currentUser) {
            return $this->formatSingleMessage($m, $currentUser);
        })->values()->toArray();
    }

    private function formatSingleMessage($m, $currentUser)
    {
        return [
            'id' => $m->id,
            'sender_id' => $m->sender_id,
            'receiver_id' => $m->receiver_id,
            'message' => $m->message,
            'message_type' => $m->message_type,
            'attachment_path' => $m->attachment_path,
            'attachment_url' => $m->attachment_path ? asset('storage/' . $m->attachment_path) : null,
            'created_at' => $m->created_at->format('g:i A'),
            'created_at_full' => $m->created_at->toISOString(),
            'is_mine' => $m->sender_id === $currentUser->id,
            'reply_to' => $m->replyTo ? [
                'id' => $m->replyTo->id,
                'message' => $m->replyTo->message,
                'sender_name' => trim(($m->replyTo->sender->f_name ?? '') . ' ' . ($m->replyTo->sender->l_name ?? '')),
            ] : null,
            'reactions' => $m->reactions->groupBy('emoji')->map(function ($group, $emoji) use ($currentUser) {
                return [
                    'emoji' => $emoji,
                    'count' => $group->count(),
                    'user_reacted' => $group->contains('user_id', $currentUser->id),
                ];
            })->values()->toArray(),
        ];
    }

    /**
     * Show inbox – list of all conversations (excluding deleted)
     */
    public function inbox()
    {
        $currentUser = Auth::user();

        // Get deleted room IDs
        $deletedRooms = ChatDelete::where('user_id', $currentUser->id)
            ->pluck('room_id')
            ->toArray();

        // Get archived room IDs
        $archivedRooms = ChatArchive::where('user_id', $currentUser->id)
            ->pluck('room_id')
            ->toArray();

        // Get all room_ids (excluding deleted)
        $roomIds = ChatMessage::where(function ($q) use ($currentUser) {
                $q->where('sender_id', $currentUser->id)
                  ->orWhere('receiver_id', $currentUser->id);
            })
            ->whereNotIn('room_id', $deletedRooms)
            ->pluck('room_id')
            ->unique();

        $conversations = [];

        foreach ($roomIds as $roomId) {
            $lastMessage = ChatMessage::where('room_id', $roomId)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastMessage) continue;

            $otherUserId = $lastMessage->sender_id === $currentUser->id
                ? $lastMessage->receiver_id
                : $lastMessage->sender_id;

            $otherUser = User::find($otherUserId);
            if (!$otherUser) continue;

            $unreadCount = ChatMessage::where('room_id', $roomId)
                ->where('receiver_id', $currentUser->id)
                ->where('is_read', false)
                ->count();

            $conversations[] = [
                'other_user' => $otherUser,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
                'is_archived' => in_array($roomId, $archivedRooms),
            ];
        }

        // Sort by last message time (newest first)
        usort($conversations, function ($a, $b) {
            return $b['last_message']->created_at <=> $a['last_message']->created_at;
        });

        return view('messages.messages_index', compact('conversations'));
    }
}