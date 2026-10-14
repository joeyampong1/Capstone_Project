<?php

namespace App\Services;

use Kreait\Firebase\Contract\Firestore;

class FirestoreService
{
    public function __construct(
        private Firestore $firestore
    ) {}

    /**
     * Push a chat message to Firestore for real-time delivery.
     */
    public function sendMessage(string $roomId, array $data): void
    {
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
