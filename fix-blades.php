<?php

$files = [
    'resources/views/admin/users/index.blade.php',
    'resources/views/admin/users/show.blade.php',
    'resources/views/complaints/show_complaint.blade.php',
    'resources/views/layouts/navigation.blade.php',
    'resources/views/messages/create_message.blade.php',
    'resources/views/messages/messages_index.blade.php',
    'resources/views/mybookings/create_booking.blade.php',
    'resources/views/mybookings/partials/booking-card.blade.php',
    'resources/views/mybookings/show_booking.blade.php',
    'resources/views/owner/find_sitter.blade.php',
    'resources/views/owner/sitter_profile.blade.php',
    'resources/views/profile/public.blade.php',
];

$replacements = [
    // Profile photo — flexible whitespace between 'storage/' and $var
    "/asset\('storage\/'\s*\.\s*\\\$user->profile_photo\)/"             => '$user->profile_photo_url',
    "/asset\('storage\/'\s*\.\s*\\\$sitter->profile_photo\)/"           => '$sitter->profile_photo_url',
    "/asset\('storage\/'\s*\.\s*\\\$otherUser->profile_photo\)/"        => '$otherUser->profile_photo_url',
    "/asset\('storage\/'\s*\.\s*\\\$counterpart->profile_photo\)/"      => '$counterpart->profile_photo_url',
    "/asset\('storage\/'\s*\.\s*\\\$complainant->profile_photo\)/"      => '$complainant->profile_photo_url',
    "/asset\('storage\/'\s*\.\s*\\\$respondent->profile_photo\)/"       => '$respondent->profile_photo_url',
    "/asset\('storage\/'\s*\.\s*\\\$review->reviewer->profile_photo\)/" => '$review->reviewer->profile_photo_url',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;

    if (!file_exists($path)) {
        echo "SKIP: {$file}\n";
        continue;
    }

    $content  = file_get_contents($path);
    $original = $content;

    foreach ($replacements as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "✅ Updated: {$file}\n";
    } else {
        echo "⏭️  No changes: {$file}\n";
    }
}

echo "\nDone!\n";