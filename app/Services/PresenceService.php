<?php

namespace App\Services;

use Kreait\Firebase\Contract\Database;

class PresenceService
{
    private ?Database $database = null;

    public function __construct()
    {
        try {
            // Check if credentials exist before resolving the Database service
            if (config('firebase.projects.app.credentials')) {
                $this->database = app(Database::class);
            }
        } catch (\Throwable $e) {
            \Log::warning('PresenceService initialization skipped: ' . $e->getMessage());
            $this->database = null;
        }
    }

    /**
     * Set user as online.
     */
    public function setOnline(int $userId): void
    {
        // Skip if Firebase is not configured
        if (!$this->database) return;

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
        // Skip if Firebase is not configured
        if (!$this->database) return;

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
        // Skip if Firebase is not configured
        if (!$this->database) return;

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
        // Skip if Firebase is not configured
        if (!$this->database) return null;

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
        // Skip if Firebase is not configured
        if (!$this->database) return [];

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
