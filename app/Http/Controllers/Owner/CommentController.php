<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\SitterProfile;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        // Resolve the SitterProfile from User ID
        $sitterProfile = SitterProfile::where('user_id', $sitterId)->first();

        if (!$sitterProfile) {
            return back()->withErrors([
                'body' => 'This user does not have a sitter profile yet.',
            ]);
        }

        // ============================================
        // FIX ISSUE #32: CHECK IF PROFILE IS HIDDEN/PRIVATE
        // ============================================
        // IMPORTANT: Check your database columns.
        // Adjust the condition if your column name is different (e.g., 'is_private', 'visibility_status').
        $isHidden = $sitterProfile->is_hidden ?? false;

        if ($isHidden) {
            abort(403, 'You cannot comment on a hidden or private profile.');
        }

        // ============================================
        // FIX ISSUE #32: CHECK IF PARENT ID BELONGS TO THIS SITTER
        // ============================================
        if ($request->filled('parent_id')) {
            $parentComment = Comment::find($request->parent_id);

            // Ensure the parent comment belongs to this sitter profile
            if (!$parentComment || $parentComment->sitter_id !== $sitterProfile->id) {
                return back()->withErrors([
                    'body' => 'Invalid reply target. This comment does not belong to this profile.',
                ]);
            }
        }

        // ============================================
        // FIX ISSUE #31: TRY-CATCH FOR DB ERRORS
        // ============================================
        try {
            // Save comment using the SitterProfile ID (not the User ID)
            $comment = Comment::create([
                'sitter_id' => $sitterProfile->id,   // FK to sitter_profiles.id
                'user_id'   => Auth::id(),
                'body'      => $request->body,
                'parent_id' => $request->parent_id,
            ]);

            // ============================================
            // NOTIFICATION LOGIC
            // ============================================
            $sender = Auth::user();

            if ($request->parent_id) {
                // REPLY scenario — notify parent comment author
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
                // DIRECT COMMENT scenario — notify sitter
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

            return redirect()->route('owner.sitter.profile', $sitterId);

        } catch (\Exception $e) {
            Log::error('Comment Store Error: ' . $e->getMessage());
            return back()->withErrors([
                'body' => __('messages.db_error_friendly'),
            ])->withInput();
        }
    }

    /**
     * Delete a comment (only author can delete their own)
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Only the author can delete
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        // Resolve the User ID from the comment's SitterProfile ID
        $sitterProfile = SitterProfile::find($comment->sitter_id);
        $sitterUserId = $sitterProfile ? $sitterProfile->user_id : null;

        try {
            $comment->delete();

            if ($sitterUserId) {
                return redirect()->route('owner.sitter.profile', $sitterUserId)
                    ->with('status', 'Comment deleted.');
            }

            return back()->with('status', 'Comment deleted.');

        } catch (\Exception $e) {
            Log::error('Comment Delete Error: ' . $e->getMessage());
            return back()->with('error', __('messages.db_error_friendly'));
        }
    }
}
