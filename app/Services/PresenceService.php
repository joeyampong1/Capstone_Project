<?php

namespace App\Services;

use Kreait\Firebase\Contract\Database;

class PresenceService
{
    public function __construct(
        private Database $database
    ) {}

    /**
     * Set user as online.
     */
    public function setOnline(int $userId): void
    {
        try {
            $this->database->getReference("presence/{$userId}")->update([
                'status'    => 'online',
                'last_seen' => now()->toISOString(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Presence setOnline failed', [
                'user_id' => $userId,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Set user as offline.
     */
    public function setOffline(int $userId): void
    {
        try {
            $this->database->getReference("presence/{$userId}")->update([
                'status'    => 'offline',
                'last_seen' => now()->toISOString(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Presence setOffline failed', [
                'user_id' => $userId,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Set typing indicator for a user in a room.
     */
    public function setTyping(string $roomId, int $userId, bool $isTyping): void
    {
        try {
            $this->database->getReference("typing/{$roomId}/{$userId}")->set([
                'is_typing'  => $isTyping,
                'updated_at' => now()->timestamp,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Presence setTyping failed', [
                'room_id' => $roomId,
                'user_id' => $userId,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get a single user's presence data.
     */
    public function getUserPresence(int $userId): ?array
    {
        try {
            $snapshot = $this->database->getReference("presence/{$userId}")->getSnapshot();

            return $snapshot->exists() ? $snapshot->getValue() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get presence for multiple users (for inbox listing).
     */
    public function getMultipleUserPresence(array $userIds): array
    {
        $result = [];

        foreach ($userIds as $userId) {
            $result[$userId] = $this->getUserPresence($userId);
        }

        return $result;
    }

    /**
     * Check if a user is currently online (with 60s grace period).
     */
    public function isOnline(?array $presence): bool
    {
        if (!$presence || ($presence['status'] ?? 'offline') !== 'online') {
            return false;
        }

        $lastSeen = $presence['last_seen'] ?? null;
        if (!$lastSeen) {
            return false;
        }

        try {
            return \Carbon\Carbon::parse($lastSeen)->diffInSeconds(now()) < 60;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
