<?php

namespace App\Providers;

use App\Mail\BrevoTransport;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS if there is a production environment
        if (config('app.env') === 'production') {
            URL::forceScheme('https'); // <--- GIDUGANG
        }

        // Register Brevo HTTP API Mail Transport
        Mail::extend('brevo', function (array $config) {
            return new BrevoTransport((string) config('services.brevo.key'));
        });

        // ==========================================
        // Share badge counts across specific layouts only
        // ==========================================
        View::composer([
            'layouts.app',
            'layouts.admin',
            'layouts.owner',
            'layouts.sitter'
        ], function ($view) {
            if (!Auth::check()) {
                return;
            }

            $userId = Auth::id();
            $user = Auth::user();

            $counts = Cache::remember("user_badge_counts_{$userId}", 60, function () use ($userId, $user) {
                $unreadMessagesCount = ChatMessage::where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();

                $unreadNotificationsCount = Notification::where('user_id', $userId)
                    ->where(function ($q) {
                        $q->where('is_read', false)
                          ->orWhereNull('read_at');
                    })
                    ->count();

                $pendingBookingsCount = 0;

                if (class_exists(Booking::class)) {
                    if ($user->is_sitter) {
                        $pendingBookingsCount = Booking::where('sitter_id', $userId)
                            ->where('status', 'pending')
                            ->count();
                    } else {
                        $pendingBookingsCount = Booking::where('owner_id', $userId)
                            ->whereIn('status', ['pending', 'accepted'])
                            ->count();
                    }
                }

                return [
                    'unreadMessagesCount'      => $unreadMessagesCount,
                    'unreadNotificationsCount' => $unreadNotificationsCount,
                    'pendingBookingsCount'     => $pendingBookingsCount,
                ];
            });

            $view->with($counts);
        });
    }
}
