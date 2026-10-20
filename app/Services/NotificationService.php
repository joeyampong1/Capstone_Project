<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class NotificationService
{
    // ==========================================
    // CORE — Create single notification
    // ==========================================

    public static function send(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?int $actorId = null,
        array $meta = [],
        bool $sendEmail = true // <--- BAG-O NGA PARAMETER
    ): Notification {

        // 1. SAVE TO DATABASE (Bell Icon)
        $notification = Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => array_merge($meta, [
                'actor_id'   => $actorId,
                'action_url' => $actionUrl,
            ]),
            'is_read' => false,
        ]);

        // 2. EXTRA FEATURES: Email & Push
        $user = User::find($userId);

        if ($user) {
            try {
                // Send Email (Brevo) - ONLY IF $sendEmail is TRUE
                if ($sendEmail && $user->email_notifications_enabled) {
                    self::sendEmail($user, $title, $message, $actionUrl);
                }

                // Send Push (FCM) - Always send if enabled
                if ($user->push_notifications_enabled && $user->fcm_token) {
                    self::sendPush($user, $title, $message, $actionUrl);
                }
            } catch (\Throwable $e) {
                \Log::error("Notification Extra (Email/Push) Failed for User {$userId}: " . $e->getMessage());
            }
        }

        // 3. RETURN PARA SA BELL ICON
        return $notification;
    }

    // ==========================================
    // HELPER: Send Email via Brevo
    // ==========================================
    private static function sendEmail(User $user, string $title, string $message, ?string $actionUrl): void
    {
        $primaryColor = '#f07a3a';
        $darkText     = '#1b3b36';
        $year         = date('Y');
        $btnHtml      = '';

        if ($actionUrl) {
            $btnHtml = "
                <div style='text-align: center; margin-top: 32px;'>
                    <a href='{$actionUrl}' style='background-color: {$primaryColor}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 12px; font-weight: bold; font-size: 15px; display: inline-block;'>
                        View Details
                    </a>
                </div>
            ";
        }

        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f6f8; padding: 40px 20px;">
                <tr>
                    <td align="center">
                        <!-- Main Card -->
                        <table width="100%" max-width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">

                            <!-- Header / Logo area -->
                            <tr>
                                <td align="center" style="background-color: {$primaryColor}; padding: 24px;">
                                    <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 900; letter-spacing: -0.5px;">
                                        PetNanny
                                    </h1>
                                </td>
                            </tr>

                            <!-- Content area -->
                            <tr>
                                <td style="padding: 40px 30px;">
                                    <h2 style="margin-top: 0; margin-bottom: 16px; color: {$darkText}; font-size: 22px; font-weight: 800;">
                                        {$title}
                                    </h2>
                                    <p style="margin: 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                        {$message}
                                    </p>

                                    {$btnHtml}
                                </td>
                            </tr>

                            <!-- Footer area -->
                            <tr>
                                <td align="center" style="background-color: #f9fafb; padding: 24px 20px; border-top: 1px solid #e5e7eb;">
                                    <p style="margin: 0; color: #9ca3af; font-size: 13px; line-height: 1.5;">
                                        This is an automated message from PetNanny.<br>Please do not reply directly to this email.
                                    </p>
                                    <p style="margin: 8px 0 0 0; color: #d1d5db; font-size: 11px;">
                                        &copy; {$year} PetNanny. All rights reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;

        Mail::html($html, function ($msg) use ($user, $title) {
            $msg->to($user->email)
                ->subject("PetNanny: {$title}");
        });
    }

    // ==========================================
    // HELPER: Send Push Notification via FCM
    // ==========================================
    private static function sendPush(User $user, string $title, string $message, ?string $actionUrl): void
    {
        $messaging = app('firebase.messaging');

        $cloudMessage = CloudMessage::withTarget('token', $user->fcm_token)
            ->withNotification(FcmNotification::create($title, $message))
            ->withData([
                'url' => $actionUrl ?? route('dashboard'),
            ]);

        $messaging->send($cloudMessage);
    }

    // ==========================================
    // BULK — Send to many users
    // ==========================================

    public static function sendMany(
        array $userIds,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        array $meta = []
    ): void {
        foreach ($userIds as $userId) {
            self::send($userId, $type, $title, $message, $actionUrl, null, $meta);
        }
    }

    // ==========================================
    // ADMIN — Notify all admins
    // ==========================================

    public static function notifyAdmins(
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        array $meta = []
    ): void {
        $admins = User::where('role', 'admin')->pluck('id');

        foreach ($admins as $adminId) {
            self::send($adminId, $type, $title, $message, $actionUrl, null, $meta);
        }
    }

    // ==========================================
    // DOMAIN EVENTS — Booking (NAA EMAIL)
    // ==========================================

    public static function bookingCreated(User $owner, User $sitter, $booking): void
    {
        self::send(
            userId:    $sitter->id,
            type:      Notification::TYPE_BOOKING_CREATED,
            title:     'New booking request',
            message:   "{$owner->f_name} requested a booking for " . ($booking->pet->name ?? 'your service'),
            actionUrl: route('mybookings.index'),
            actorId:   $owner->id,
            meta:      ['booking_id' => $booking->id],
        );
    }

    public static function bookingConfirmed(User $sitter, User $owner, $booking): void
    {
        self::send(
            userId:    $owner->id,
            type:      Notification::TYPE_BOOKING_CONFIRMED,
            title:     'Booking confirmed',
            message:   "{$sitter->f_name} confirmed your booking",
            actionUrl: route('mybookings.index'),
            actorId:   $sitter->id,
            meta:      ['booking_id' => $booking->id],
        );
    }

    public static function bookingCancelled(User $canceller, User $recipient, $booking): void
    {
        self::send(
            userId:    $recipient->id,
            type:      Notification::TYPE_BOOKING_CANCELLED,
            title:     'Booking cancelled',
            message:   "{$canceller->f_name} cancelled the booking",
            actionUrl: route('mybookings.index'),
            actorId:   $canceller->id,
            meta:      ['booking_id' => $booking->id],
        );
    }

    public static function bookingCompleted(User $sitter, User $owner, $booking): void
    {
        self::send(
            userId:    $owner->id,
            type:      Notification::TYPE_BOOKING_COMPLETED,
            title:     'Booking completed',
            message:   "{$sitter->f_name} marked the booking as completed",
            actionUrl: route('mybookings.index'),
            actorId:   $sitter->id,
            meta:      ['booking_id' => $booking->id],
        );
    }

    // ==========================================
    // DOMAIN EVENTS — Visits (PUSH RA / NO EMAIL)
    // ==========================================

    public static function visitCheckIn(User $sitter, User $owner, $visit): void
    {
        self::send(
            userId:    $owner->id,
            type:      'visit_checked_in',
            title:     'Sitter checked in',
            message:   "{$sitter->f_name} started Visit #{$visit->visit_number} for " . ($visit->booking->pet->name ?? 'your pet') . ".",
            actionUrl: route('task.monitor.visit', $visit->id),
            actorId:   $sitter->id,
            meta:      ['visit_id' => $visit->id, 'booking_id' => $visit->booking_id],
            sendEmail: false, // <--- DISABLE EMAIL
        );
    }

    public static function visitCheckOut(User $sitter, User $owner, $visit): void
    {
        $duration = $visit->duration_minutes ? " ({$visit->duration_minutes} min)" : '';
        self::send(
            userId:    $owner->id,
            type:      'visit_checked_out',
            title:     'Sitter checked out',
            message:   "{$sitter->f_name} finished Visit #{$visit->visit_number} for " . ($visit->booking->pet->name ?? 'your pet') . "{$duration}.",
            actionUrl: route('task.monitor.visit', $visit->id),
            actorId:   $sitter->id,
            meta:      ['visit_id' => $visit->id, 'booking_id' => $visit->booking_id],
            sendEmail: false, // <--- DISABLE EMAIL
        );
    }

    public static function visitCompleted(User $sitter, User $owner, $visit): void
    {
        self::send(
            userId:    $owner->id,
            type:      'visit_completed',
            title:     'Visit completed',
            message:   "{$sitter->f_name} completed Visit #{$visit->visit_number} for " . ($visit->booking->pet->name ?? 'your pet') . ".",
            actionUrl: route('task.monitor.visit', $visit->id),
            actorId:   $sitter->id,
            meta:      ['visit_id' => $visit->id, 'booking_id' => $visit->booking_id],
            sendEmail: false, // <--- DISABLE EMAIL
        );
    }

    public static function bookingReadyForReview(User $owner, $booking, User $sitter): void
    {
        self::send(
            userId:    $owner->id,
            type:      'booking_ready_for_review',
            title:     'Ready to rate your sitter ⭐',
            message:   "Tanang visits ni {$sitter->f_name} sa booking {$booking->booking_reference} kay complete na. Leave a review!",
            actionUrl: route('tasks.monitor'),
            actorId:   $sitter->id,
            meta:      ['booking_id' => $booking->id, 'sitter_id' => $sitter->id],
            sendEmail: true, // Pwede nimo i-true kay importante ni para sa owner
        );
    }

    // ==========================================
    // DOMAIN EVENTS — Messaging (PUSH RA / NO EMAIL)
    // ==========================================

    public static function newMessage(User $recipient, User $sender, string $preview): void
    {
        self::send(
            userId:    $recipient->id,
            type:      Notification::TYPE_NEW_MESSAGE,
            title:     "New message from {$sender->f_name}",
            message:   mb_strimwidth($preview, 0, 80, '…'),
            actionUrl: route('messages.index'),
            actorId:   $sender->id,
            sendEmail: false, // <--- DISABLE EMAIL (Spam prevention)
        );
    }

    // ==========================================
    // DOMAIN EVENTS — Reviews & Comments
    // ==========================================

    public static function reviewReceived(User $sitter, User $reviewer, float $rating, string $comment = ''): void
    {
        self::send(
            userId:    $sitter->id,
            type:      Notification::TYPE_REVIEW_RECEIVED,
            title:     "New review from {$reviewer->f_name}",
            message:   "{$rating}★ — " . mb_strimwidth($comment, 0, 80, '…'),
            actionUrl: route('public.profile', ['id' => $sitter->id]),
            actorId:   $reviewer->id,
            meta:      ['rating' => $rating],
            sendEmail: true,
        );
    }

    public static function commentReceived(User $recipient, User $commenter, string $comment, int $sitterId): void
    {
        self::send(
            userId:    $recipient->id,
            type:      Notification::TYPE_COMMENT_RECEIVED,
            title:     "New comment from {$commenter->f_name}",
            message:   mb_strimwidth($comment, 0, 80, '…'),
            actionUrl: route('owner.sitter.profile', ['id' => $sitterId]),
            actorId:   $commenter->id,
            sendEmail: false, // <--- DISABLE EMAIL
        );
    }

    // ==========================================
    // DOMAIN EVENTS — Applications & Verification (NAA EMAIL)
    // ==========================================

    public static function sitterApplicationSubmitted(User $applicant): void
    {
        self::notifyAdmins(
            type:      Notification::TYPE_SITTER_APPLIED,
            title:     'New sitter application',
            message:   "{$applicant->f_name} applied to be a pet sitter",
            actionUrl: route('admin.verification.sitter'),
            meta:      ['applicant_id' => $applicant->id],
        );
    }

    public static function sitterApproved(User $applicant): void
    {
        self::send(
            userId:    $applicant->id,
            type:      Notification::TYPE_SITTER_APPROVED,
            title:     'You are now a Pet Sitter!',
            message:   'Your application has been approved. You can now accept bookings.',
            actionUrl: route('sitter.dashboard'),
            sendEmail: true,
        );
    }

    public static function sitterRejected(User $applicant, string $reason = ''): void
    {
        self::send(
            userId:    $applicant->id,
            type:      Notification::TYPE_SITTER_REJECTED,
            title:     'Application rejected',
            message:   $reason ?: 'Your application was rejected. Please review and reapply.',
            actionUrl: route('sitter.application'),
            sendEmail: true,
        );
    }

    public static function idVerificationSubmitted(User $applicant): void
    {
        self::notifyAdmins(
            type:      Notification::TYPE_SITTER_APPLIED,
            title:     'ID verification submitted',
            message:   "{$applicant->f_name} submitted ID for verification",
            actionUrl: route('admin.verification.id'),
            meta:      ['applicant_id' => $applicant->id],
        );
    }

    public static function idVerified(User $user): void
    {
        self::send(
            userId:    $user->id,
            type:      Notification::TYPE_ID_VERIFIED,
            title:     'Identity Verified ✅',
            message:   'Your ID has been verified. You can now post and apply for bookings.',
            actionUrl: route('profile.edit'),
            sendEmail: true,
        );
    }

    public static function idRejected(User $user, string $reason = ''): void
    {
        self::send(
            userId:    $user->id,
            type:      Notification::TYPE_ID_REJECTED,
            title:     'ID verification failed',
            message:   $reason ?: 'Please upload a clear selfie holding your valid ID.',
            actionUrl: route('profile.edit'),
            sendEmail: true,
        );
    }

    // ==========================================
    // DOMAIN EVENTS — Complaints & Claims
    // ==========================================

    public static function complaintFiled(User $respondent, User $complainant, string $type): void
    {
        self::send(
            userId:    $respondent->id,
            type:      Notification::TYPE_COMPLAINT_FILED,
            title:     'New complaint filed',
            message:   "{$complainant->f_name} filed a complaint ({$type})",
            actionUrl: route('complaints.index'),
            actorId:   $complainant->id,
            meta:      ['complaint_type' => $type],
            sendEmail: true,
        );
    }

    public static function complaintResolved(User $user, string $resolution): void
    {
        self::send(
            userId:    $user->id,
            type:      Notification::TYPE_COMPLAINT_RESOLVED,
            title:     'Complaint resolved',
            message:   $resolution,
            actionUrl: route('complaints.index'),
            sendEmail: true,
        );
    }

    // public static function claimFiled(User $claimant, string $type): void
    // {
    //     self::notifyAdmins(
    //         type:      Notification::TYPE_CLAIM_FILED,
    //         title:     'New protection claim',
    //         message:   "{$claimant->f_name} submitted a claim ({$type})",
    //         actionUrl: route('admin.claims'),
    //     );
    // }

    // public static function claimApproved(User $claimant, float $amount): void
    // {
    //     self::send(
    //         userId:    $claimant->id,
    //         type:      Notification::TYPE_CLAIM_APPROVED,
    //         title:     'Claim approved ✅',
    //         message:   'Your protection claim was approved for ₱' . number_format($amount, 2),
    //         actionUrl: route('complaints.index'),
    //         meta:      ['amount' => $amount],
    //         sendEmail: true,
    //     );
    // }

    // public static function claimRejected(User $claimant, string $reason = ''): void
    // {
    //     self::send(
    //         userId:    $claimant->id,
    //         type:      Notification::TYPE_CLAIM_REJECTED,
    //         title:     'Claim rejected',
    //         message:   $reason ?: 'Your protection claim was rejected.',
    //         actionUrl: route('complaints.index'),
    //         sendEmail: true,
    //     );
    // }

    // ==========================================
    // SYSTEM
    // ==========================================

    public static function system(int $userId, string $title, string $message, ?string $actionUrl = null): void
    {
        self::send($userId, Notification::TYPE_SYSTEM, $title, $message, $actionUrl);
    }

    // ==========================================
    // DOMAIN EVENTS — Help & Support Messages
    // ==========================================

    public static function supportMessageReplied(User $recipient, User $admin, $supportMessage): void
    {
        $isResolved = $supportMessage->status === 'resolved';

        self::send(
            userId:    $recipient->id,
            type:      Notification::TYPE_SUPPORT_REPLIED,
            title:     $isResolved ? 'Support ticket resolved ✅' : 'Support team replied',
            message:   "{$admin->f_name} replied to your message: \"{$supportMessage->subject}\"",
            actionUrl: route('help_support.index'),
            actorId:   $admin->id,
            meta:      [
                'message_code' => $supportMessage->message_code,
                'support_id'   => $supportMessage->id,
                'status'       => $supportMessage->status,
            ],
            sendEmail: true,
        );
    }

    public static function supportMessageSubmitted(User $user, $supportMessage): void
    {
        $typeLabel = $supportMessage->type === 'report' ? 'Problem Report' : 'Contact Message';

        self::notifyAdmins(
            type:      Notification::TYPE_SUPPORT_SUBMITTED,
            title:     "New {$typeLabel}",
            message:   "{$user->f_name} submitted: \"{$supportMessage->subject}\"",
            actionUrl: route('admin.messages.show', $supportMessage->id),
            meta:      [
                'message_code' => $supportMessage->message_code,
                'support_id'   => $supportMessage->id,
                'type'         => $supportMessage->type,
                'sender_id'    => $user->id,
            ],
        );
    }
}
