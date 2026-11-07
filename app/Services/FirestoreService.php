<?php

namespace App\Services;

use Kreait\Firebase\Contract\Firestore;

class FirestoreService
{
    private ?Firestore $firestore = null;

    public function __construct()
    {
        try {
            // Check if credentials exist before resolving the Firestore service
            if (config('firebase.projects.app.credentials')) {
                $this->firestore = app(Firestore::class);
            }
        } catch (\Throwable $e) {
            // Log a warning but don't crash the app if Firebase is not configured
            \Log::warning('FirestoreService initialization skipped: ' . $e->getMessage());
            $this->firestore = null;
        }
    }

    /**
     * Push a chat message to Firestore for real-time delivery.
     */
    public function sendMessage(string $roomId, array $data): void
    {
        // Skip if Firebase is not configured
        if (!$this->firestore) return;

        try {
            $this->firestore
                ->database()
                ->collection('chats')
                ->document($roomId)
                ->collection('messages')
                ->document((string) $data['id'])
                ->set($data);
        } catch (\Throwable $e) {
            \Log::error('Firestore sendMessage failed', [
                'room_id' => $roomId,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark all messages in a room as read for the given user.
     */
    public function markMessagesRead(string $roomId, int $readerId): void
    {
        // Skip if Firebase is not configured
        if (!$this->firestore) return;

        try {
            $collection = $this->firestore
                ->database()
                ->collection('chats')
                ->document($roomId)
                ->collection('messages');

            $messages = $collection
                ->where('receiver_id', '=', $readerId)
                ->where('is_read', '=', false)
                ->documents();

            foreach ($messages as $doc) {
                if ($doc->exists()) {
                    $doc->reference()->update([
                        'is_read'   => true,
                        'read_at'   => now()->toISOString(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Firestore markMessagesRead failed', [
                'room_id' => $roomId,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update reactions on a specific message in Firestore.
     */
    public function updateMessageReactions(string $roomId, int $messageId, array $reactions): void
    {
        // Skip if Firebase is not configured
        if (!$this->firestore) return;

        try {
            $this->firestore
                ->database()
                ->collection('chats')
                ->document($roomId)
                ->collection('messages')
                ->document((string) $messageId)
                ->update([
                    'reactions' => $reactions,
                ]);
        } catch (\Throwable $e) {
            \Log::error('Firestore updateReactions failed', [
                'room_id'    => $roomId,
                'message_id' => $messageId,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
