<?php

namespace App\Providers;

use App\Models\ChatMessage;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ==========================================
        // Share badge counts sa tanan views
        // ==========================================
        View::composer('*', function ($view) {
            if (!Auth::check()) {
                return;
            }

            $userId = Auth::id();
            $user = Auth::user();

            // Messages count
            $unreadMessagesCount = ChatMessage::where('receiver_id', $userId)
                ->where('is_read', false)
                ->count();

            // Notifications count
            $unreadNotificationsCount = Notification::where('user_id', $userId)
                ->where(function ($q) {
                    $q->where('is_read', false)
                      ->orWhereNull('read_at');
                })
                ->count();

            // Bookings count — depends on role
            $pendingBookingsCount = 0;

            if (class_exists(\App\Models\Booking::class)) {
                if ($user->is_sitter) {
                    // Sitter: pending bookings nga kinahanglan accept/decline
                    $pendingBookingsCount = \App\Models\Booking::where('sitter_id', $userId)
                        ->where('status', 'pending')
                        ->count();
                } else {
                    // Owner: pending + confirmed upcoming bookings
                    $pendingBookingsCount = \App\Models\Booking::where('owner_id', $userId)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->count();
                }
            }

            $view->with([
                'unreadMessagesCount'      => $unreadMessagesCount,
                'unreadNotificationsCount' => $unreadNotificationsCount,
                'pendingBookingsCount'     => $pendingBookingsCount,
            ]);
        });
    }
}