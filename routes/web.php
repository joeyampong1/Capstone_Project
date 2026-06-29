<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Owner\FindSitterController;
use App\Http\Controllers\Owner\SitterProfileController; 
use App\Http\Controllers\Owner\MessageController;      
use App\Http\Controllers\Owner\BookingController;      
use App\Http\Controllers\Owner\CommentController; 
use App\Http\Controllers\Sitter\ApplicationController; 
          

use Illuminate\Support\Facades\Route;

// ========================================== //
// PUBLIC ROUTES                              //
// ========================================== //
Route::get('/', function () {
    return view('welcome');
});

// ========================================== //
// AUTHENTICATED ROUTES                       //
// ========================================== //

// Dashboard – role-based (admin or owner/sitter)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile routes – All in one group
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Photo upload
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    
    // Location update
    Route::patch('/profile/location', [ProfileController::class, 'updateLocation'])->name('profile.location');
    
    // ID upload
    Route::post('/profile/id', [ProfileController::class, 'updateId'])->name('profile.id');
});

// ========================================== //
// OWNER ROUTES                               //
// ========================================== //
Route::get('/find-sitter', [FindSitterController::class, 'index'])
    ->middleware(['auth'])
    ->name('find.sitter');

// ========================================== //
// SITTER ROUTES - ALL PROTECTED BY AUTH      //
// ========================================== //
Route::middleware(['auth'])->group(function () {
    
    // Sitter profile
    Route::get('/sitter/{id}', [SitterProfileController::class, 'show'])
        ->name('owner.sitter.profile');
    
    // Message & Booking (placeholder routes – you'll implement controllers later)
    // Commented out – static prototype is used for now
    // Route::get('/sitter/{id}/message', [MessageController::class, 'create'])->name('owner.messages');
    Route::get('/sitter/{id}/book', [BookingController::class, 'create'])
        ->name('owner.book');
    
    // Comments & Replies
    Route::post('/sitter/{id}/comment', [CommentController::class, 'store'])
        ->name('owner.sitter.comment');
    Route::post('/comment/{comment}/reply', [CommentController::class, 'reply'])
        ->name('owner.sitter.comment.reply');

    // TEMPORARY: STATIC PROTOTYPE ROUTES
    Route::middleware(['auth'])->group(function () {
        
        // Static Sitter Profile
        Route::get('/sitter/{id}', function ($id) {
            return view('owner.sitter_profile');
        })->name('owner.sitter.profile');

        // My Bookings
        Route::get('/mybookings', function () {
            return view('mybookings.mybookings');  
        })->name('mybookings.index');

        // My Pets
        Route::get('/mypets', function () {
            return view('mypets.index');
        })->name('mypets.index');

        Route::get('/mypets/create', function () {
            return view('mypets.create');
        })->name('mypets.create');

        // Static Messages – now points to the new view
        Route::get('/sitter/{id}/message', function ($id) {
            return view('messages.create_message');
        })->name('owner.messages');

        Route::get('/messages', function () {
            return view('messages.messages');
        })->name('messages.index');

        // Static Notifications – now points to the new view
        Route::get('/notifications', function () {
            return view('notifactions.notifications');
        })->name('notifications.index');

        // ✅ Static Public Profile (with dummy data)
        Route::get('/profile/{id}', function ($id) {
            $user = (object) [
                'id' => $id,
                'name' => 'Maria Santos',
                'gender' => 'female',                  
                'is_sitter' => true,                  
                'sitter_level' => 3,
                'email' => 'maria@example.com',
                'location' => 'Makati City',
                'bio' => 'I love caring for pets...',
                'role' => 'owner',
                'rate_per_visit' => 350,
                'pet_types' => 'dogs, cats',
                'food_preference' => 'flexible',
                'can_provide_food' => true,
                'id_validation_status' => 'verified',
                'contact_number' => '0917 123 4567',
                'address' => 'Makati City, Metro Manila',
                'cover_photo' => null,
                'profile_photo' => null,
                'pets' => collect([
                    (object) ['name' => 'Mingming', 'type' => 'Cat', 'breed' => 'Persian'],
                    (object) ['name' => 'Buboy', 'type' => 'Cat', 'breed' => 'Siamese'],
                ]),
                'reviews' => collect([]),
                'average_rating' => 4.8,
                'created_at' => now()->subMonths(6),
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Maria+Santos&background=f07a3a&color=fff&size=100',
            ];
            return view('profile.public', compact('user'));
        })->name('public.profile');

        // Static Booking (placeholder)
        Route::get('/sitter/{id}/book', function ($id) {
            return view('owner.booking');
        })->name('owner.book');
        
    });  // <-- This closes the inner group

    Route::get('/sitter/apply', [Sitter\ApplicationController::class, 'create'])->name('sitter.application.create');
    Route::post('/sitter/apply', [Sitter\ApplicationController::class, 'store'])->name('sitter.application.store');
    
});

require __DIR__.'/auth.php';