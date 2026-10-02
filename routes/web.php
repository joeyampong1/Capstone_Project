<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Admin\SitterVerificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\IdVerificationController;
use App\Http\Controllers\Admin\HelpSupportController;
use App\Http\Controllers\Admin\SupportInboxController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserHelpSupportController;
use App\Http\Controllers\Owner\PublicProfileController;
use App\Http\Controllers\Owner\FindSitterController;
use App\Http\Controllers\Owner\SitterProfileController;
use App\Http\Controllers\Owner\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Owner\BookingController;
use App\Http\Controllers\Owner\CommentController;
use App\Http\Controllers\Owner\PetController;
use App\Http\Controllers\Owner\TaskMonitorController;
use App\Http\Controllers\Owner\ReviewController;
use App\Http\Controllers\Sitter\AvailabilityController;
use App\Http\Controllers\Sitter\ApplicationController;
use App\Http\Controllers\Sitter\TaskController;
use App\Http\Controllers\Sitter\DashboardController as SitterDashboardController;
use App\Http\Controllers\ComplaintController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (Shared: Admin, Owner, Sitter)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ============================================================
    // DASHBOARD
    // ============================================================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['verified'])
        ->name('dashboard');

    // ============================================================
    // PROFILE (All Roles)
    // ============================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::patch('/profile/location', [ProfileController::class, 'updateLocation'])->name('profile.location');
    Route::post('/profile/id', [ProfileController::class, 'updateId'])->name('profile.id');

    Route::patch('/settings/sitter-mode', [ProfileController::class, 'toggleSitterMode'])
        ->name('settings.sitter-mode');

    Route::patch('/profile/sitter-settings', [ProfileController::class, 'updateSitterSettings'])
        ->name('profile.sitter-settings');

    Route::patch('/settings/language', [ProfileController::class, 'updateLanguage'])
        ->name('settings.language');

    Route::patch('/settings/ui-preferences', [SettingsController::class, 'updateUiPreferences'])
        ->name('settings.ui-preferences');

    // ============================================================
    // NOTIFICATIONS (All Roles)
    // ============================================================
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/poll', [NotificationController::class, 'poll'])
        ->name('notifications.poll');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');

    Route::post('/notifications/{notification}/unread', [NotificationController::class, 'markUnread'])
        ->name('notifications.unread');

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');

    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');

    Route::delete('/notifications', [NotificationController::class, 'clearAll'])
        ->name('notifications.clearAll');

    // ============================================================
    // ONBOARDING (All authenticated users)
    // ============================================================
    Route::post('/onboarding/dismiss', function () {
        auth()->user()->update(['onboarding_dismissed' => true]);
        return response()->json(['ok' => true]);
    })->name('onboarding.dismiss');

    // ============================================================
    // BADGE COUNTS (Live polling for navbar)
    // ============================================================
    Route::get('/badges/poll', function () {
        $userId = auth()->id();
        $user   = auth()->user();

        // Messages count
        $messages = \App\Models\ChatMessage::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        // Notifications count
        $notifications = \App\Models\Notification::where('user_id', $userId)
            ->where(function ($q) {
                $q->where('is_read', false)->orWhereNull('read_at');
            })
            ->count();

        // Bookings count (role-based)
        $bookings = 0;

        if (class_exists(\App\Models\Booking::class)) {
            if ($user->is_sitter) {
                $bookings = \App\Models\Booking::where('sitter_id', $userId)
                    ->where('status', 'pending')
                    ->count();
            } else {
                $bookings = \App\Models\Booking::where('owner_id', $userId)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->count();
            }
        }

        return response()->json([
            'success'       => true,
            'messages'      => $messages,
            'notifications' => $notifications,
            'bookings'      => $bookings,
        ]);
    })->name('badges.poll');

    // ============================================================
    // PUBLIC PROFILE (Viewed by any logged-in user)
    // ============================================================
    Route::get('/profile/{id}', [PublicProfileController::class, 'show'])->name('public.profile');

    // ============================================================
    // SETTINGS (All Roles)
    // ============================================================
    Route::get('/settings', function () {
        return view('settings.system_settings');
    })->name('settings.index');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
        Route::prefix('admin')->name('admin.')->group(function () {

        // ============================================================
        // ADMIN HOME (Creative Homepage)
        // ============================================================
        Route::get('/home', [AdminDashboardController::class, 'home'])
            ->name('home');

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // User Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
        Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::post('/users/{user}/restore', [AdminUserController::class, 'restore'])->name('users.restore');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/restore-deleted', [AdminUserController::class, 'restoreDeleted'])->name('users.restoreDeleted');
        Route::post('/users/{user}/force-delete', [AdminUserController::class, 'forceDelete'])->name('users.forceDelete');
        Route::post('/users/{user}/promote', [AdminUserController::class, 'promote'])->name('users.promote');
        Route::post('/users/{user}/demote', [AdminUserController::class, 'demote'])->name('users.demote');

        // Verification — ID
        Route::get('/verification/id', [IdVerificationController::class, 'index'])
            ->name('verification.id');

        Route::post('/verification/id/{id}/approve', [IdVerificationController::class, 'approve'])
            ->name('verification.id.approve');

        Route::post('/verification/id/{id}/reject', [IdVerificationController::class, 'reject'])
            ->name('verification.id.reject');

        Route::get('/verification/id/{id}/download', [IdVerificationController::class, 'download'])
            ->name('verification.id.download');

        Route::post('/verification/id/{id}/update-ocr', [IdVerificationController::class, 'updateOcrName'])  ->name('verification.id.updateOcr');

        // Verification — Sitter
        Route::get('/verification/sitter', [SitterVerificationController::class, 'index'])
            ->name('verification.sitter');

        Route::post('/verification/sitter/{id}/approve', [SitterVerificationController::class, 'approve'])
            ->name('verification.sitter.approve');

        Route::post('/verification/sitter/{id}/reject', [SitterVerificationController::class, 'reject'])
            ->name('verification.sitter.reject');

        Route::get('/verification/sitter/{id}/download', [SitterVerificationController::class, 'download'])
            ->name('verification.sitter.download');

        // Bookings (admin)
        Route::get('/bookings', [AdminBookingController::class, 'index'])
            ->name('bookings');

        Route::post('/bookings/{id}/cancel', [AdminBookingController::class, 'cancel'])
            ->name('bookings.cancel');

        Route::post('/bookings/{id}/complete', [AdminBookingController::class, 'complete'])
            ->name('bookings.complete');

        Route::post('/bookings/{id}/mark-paid', [AdminBookingController::class, 'markPaid'])
            ->name('bookings.markPaid');

        // Complaints
        Route::get('/complaints', [AdminComplaintController::class, 'index'])
            ->name('complaints');

        Route::post('/complaints/{id}/review', [AdminComplaintController::class, 'markReview'])
            ->name('complaints.review');

        Route::post('/complaints/{id}/resolve', [AdminComplaintController::class, 'resolve'])
            ->name('complaints.resolve');

        Route::post('/complaints/{id}/dismiss', [AdminComplaintController::class, 'dismiss'])
            ->name('complaints.dismiss');

        // Reports
        Route::get('/reports', [AdminReportController::class, 'index'])
            ->name('reports');

        Route::get('/reports/export', [AdminReportController::class, 'export'])
            ->name('reports.export');

        // Analytics
       Route::get('/analytics', [AdminAnalyticsController::class, 'index'])
            ->name('analytics');

         // Help & Support (Admin)
        Route::get('/help-support', [HelpSupportController::class, 'index'])
            ->name('help_support');

        Route::post('/help-support/tickets', [HelpSupportController::class, 'store'])
            ->name('help_support.store');

        Route::get('/messages', [SupportInboxController::class, 'index'])
            ->name('messages.index');

        Route::get('/messages/{id}', [SupportInboxController::class, 'show'])
            ->name('messages.show');

        Route::post('/messages/{id}/reply', [SupportInboxController::class, 'reply'])
            ->name('messages.reply');

        Route::delete('/messages/{id}', [SupportInboxController::class, 'destroy'])
            ->name('messages.destroy');

    });

    /*
    |--------------------------------------------------------------------------
    | OWNER ROUTES
    |--------------------------------------------------------------------------
    */

    // Find Sitter (Browse & View)
    Route::get('/find-sitter', [FindSitterController::class, 'index'])->name('find.sitter');

    // My Pets (CRUD)
    Route::resource('mypets', PetController::class);

    // ============================================================
    // BOOKINGS (Owner + Sitter)
    // ============================================================
    Route::get('/mybookings', [BookingController::class, 'index'])->name('mybookings.index');
    Route::get('/mybookings/create', [BookingController::class, 'create'])->name('mybookings.create');
    Route::post('/mybookings', [BookingController::class, 'store'])->name('mybookings.store');
    Route::get('/mybookings/{booking}', [BookingController::class, 'show'])->name('mybookings.show');

    // Booking actions (Sitter)
    Route::post('/bookings/{booking}/accept', [BookingController::class, 'accept'])->name('bookings.accept');
    Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');

    // Booking actions (Owner)
    Route::post('/mybookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('mybookings.cancel');

    /*
    |--------------------------------------------------------------------------
    | SITTER ROUTES
    |--------------------------------------------------------------------------
    */

    // ---------- SITTER DASHBOARD ----------
    Route::get('/sitter/dashboard', [SitterDashboardController::class, 'index'])
        ->name('sitter.dashboard');

    // ---------- SITTER TASKS (Visits) ----------
    Route::prefix('sitter/tasks')->name('sitter.tasks.')->group(function () {
        // Booking list (1 card per booking)
        Route::get('/', [TaskController::class, 'index'])->name('index');

        // Individual visit detail (must be BEFORE /{booking})
        Route::get('/visit/{visit}', [TaskController::class, 'visitDetail'])->name('visitDetail');

        // Visits for a specific booking
        Route::get('/{booking}', [TaskController::class, 'show'])->name('show');

        // Actions
        Route::post('/visit/{visit}/check-in', [TaskController::class, 'checkIn'])->name('checkIn');
        Route::post('/visit/{visit}/check-out', [TaskController::class, 'checkOut'])->name('checkOut');
        Route::post('/visit/{visit}/complete', [TaskController::class, 'complete'])->name('complete');
        Route::post('/visit/{visit}/tasks', [TaskController::class, 'updateTasks'])->name('updateTasks');
        Route::post('/visit/{visit}/photo', [TaskController::class, 'uploadPhoto'])->name('uploadPhoto');
        Route::post('/visit/{visit}/photo/delete', [TaskController::class, 'deletePhoto'])->name('deletePhoto');
        Route::post('/visit/{visit}/note', [TaskController::class, 'addNote'])->name('addNote');
    });

    // ---------- SITTER AVAILABILITY ----------
    Route::get('/sitter/availability', [AvailabilityController::class, 'index'])->name('sitter.availability');
    Route::post('/sitter/availability', [AvailabilityController::class, 'store'])->name('sitter.availability.store');
    Route::delete('/sitter/availability/{id}', [AvailabilityController::class, 'destroy'])->name('sitter.availability.destroy');

    // ---------- SITTER PAYMENTS ----------
    // Route::get('/sitter/payments', function () {
    //     return view('sitters.sitter_payment_index');
    // })->name('sitter.payments');

    // ---------- SITTER APPLICATION ----------
    Route::get('/sitter/apply', [ApplicationController::class, 'create'])->name('sitter.application');
    Route::post('/sitter/apply', [ApplicationController::class, 'store'])->name('sitter.application.store');
    Route::put('/sitter/apply', [ApplicationController::class, 'update'])->name('sitter.application.update');

    /*
    |--------------------------------------------------------------------------
    | SHARED: SITTER PROFILE VIEW
    |--------------------------------------------------------------------------
    | MUST BE LAST AMONG /sitter/* ROUTES
    */

    Route::get('/sitter/{id}', [SitterProfileController::class, 'show'])->name('owner.sitter.profile');

    // Sitter profile actions
    Route::get('/sitter/{id}/book', [BookingController::class, 'create'])->name('owner.book');

    // Community Chat / Comments
    Route::post('/sitter/{id}/comment', [CommentController::class, 'store'])->name('sitter.comment.store');
    Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->name('sitter.comment.destroy');

    /*
    |--------------------------------------------------------------------------
    | MESSAGES / CHAT
    |--------------------------------------------------------------------------
    */

    // Specific routes FIRST
    Route::get('/messages/{id}/fetch', [MessageController::class, 'fetch'])->name('messages.fetch');
    Route::post('/messages/{id}/typing', [MessageController::class, 'typing'])->name('messages.typing');
    Route::post('/messages/{id}', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{id}', [MessageController::class, 'index'])->name('owner.messages');
    Route::post('/messages/{id}/react', [MessageController::class, 'react'])->name('messages.react');
    Route::get('/messages', [MessageController::class, 'inbox'])->name('messages.index');
    Route::post('/messages/{id}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.markRead');
    Route::post('/messages/{id}/archive', [MessageController::class, 'archive'])->name('messages.archive');
    Route::delete('/messages/{id}/delete', [MessageController::class, 'deleteConversation'])->name('messages.deleteConversation');
    Route::post('/messages/{id}/report', [MessageController::class, 'report'])->name('messages.report');

    // ============================================================
    // COMPLAINTS (Owner + Sitter)
    // ============================================================
    Route::get('/complaints', [ComplaintController::class, 'index'])
        ->name('complaints.index');

    Route::get('/complaints/create', [ComplaintController::class, 'create'])
        ->name('complaints.create');

    Route::post('/complaints', [ComplaintController::class, 'store'])
        ->name('complaints.store');

    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])
        ->name('complaints.show');

    /*
    |--------------------------------------------------------------------------
    | SHARED: PROTOTYPE / STATIC PAGES
    |--------------------------------------------------------------------------
    */

    // // Claims
    // Route::get('/claims', function () {
    //     return view('claims.claims_index');
    // })->name('claims.index');

    // Route::get('/claims/create', function () {
    //     return view('claims.create_claims');
    // })->name('claims.create');

    // // Invoices / Payments
    // Route::get('/invoices', function () {
    //     return view('payments.invoice_index');
    // })->name('invoices.index');

    // Route::get('/payments', function () {
    //     return view('payments.payment_index');
    // })->name('payments.index');

    // Route::get('/payments/create', function () {
    //     return view('payments.create_payment');
    // })->name('payments.create');

    // Help & Support (Owner + Sitter)
    Route::get('/help-support', [UserHelpSupportController::class, 'index'])
        ->name('help_support.index');

    Route::get('/help-support/contact', [UserHelpSupportController::class, 'createContact'])
        ->name('support.create');

    Route::post('/help-support/contact', [UserHelpSupportController::class, 'storeContact'])
        ->name('support.store');

    Route::get('/help-support/report', [UserHelpSupportController::class, 'createReport'])
        ->name('report.create');

    Route::post('/help-support/report', [UserHelpSupportController::class, 'storeReport'])
        ->name('report.store');

    // ============================================================
    // TASK MONITOR (Owner)
    // ============================================================
    Route::get('/tasks/monitor', [TaskMonitorController::class, 'index'])
        ->name('tasks.monitor');

    // Individual visit detail (must be BEFORE /{booking})
    Route::get('/tasks/monitor/visit/{visit}', [TaskMonitorController::class, 'visitDetail'])
        ->name('task.monitor.visit');

    // Booking with all visits
    Route::get('/tasks/monitor/{booking}', [TaskMonitorController::class, 'show'])
        ->name('task.monitor');

    // ============================================================
    // REVIEWS (Owner → Sitter)
    // ============================================================
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])
        ->name('bookings.review');

    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy');
});

require __DIR__.'/auth.php';
