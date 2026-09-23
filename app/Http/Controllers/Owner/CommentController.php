<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use App\Services\NotificationService;  // ← ADD
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a new comment or reply
     */
    public function store(Request $request, $sitterId)
    {
        $request->validate([
            'body' => 'required|string|min:2|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Save comment first
        $comment = Comment::create([
            'sitter_id' => $sitterId,
            'user_id' => Auth::id(),
            'body' => $request->body,
            'parent_id' => $request->parent_id,
        ]);

        // ============================================
        // NOTIFICATION LOGIC
        // ============================================
        $sender = Auth::user();

        if ($request->parent_id) {
            // ═══ REPLY scenario — notify parent comment author ═══
            $parentComment = Comment::find($request->parent_id);

            if ($parentComment && $parentComment->user_id !== $sender->id) {
                $recipient = User::find($parentComment->user_id);

                if ($recipient) {
                    NotificationService::commentReceived(
                        recipient: $recipient,
                        commenter: $sender,
                        comment: $request->body,
                        sitterId: (int) $sitterId,
                    );
                }
            }
        } else {
            // ═══ DIRECT COMMENT scenario — notify sitter ═══
            $sitter = User::find($sitterId);

            if ($sitter && $sitter->id !== $sender->id) {
                NotificationService::commentReceived(
                    recipient: $sitter,
                    commenter: $sender,
                    comment: $request->body,
                    sitterId: (int) $sitterId,
                );
            }
        }
        // ============================================

        return redirect()->route('owner.sitter.profile', $sitterId);
    }

    /**
     * Delete a comment (only owner can delete their own)
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Only the author can delete
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $sitterId = $comment->sitter_id;
        $comment->delete();

        return redirect()->route('owner.sitter.profile', $sitterId)
            ->with('status', 'Comment deleted.');
    }
}