<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

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
        array $meta = []
    ): Notification {
        return Notification::create([
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
    // DOMAIN EVENTS — Booking
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
    // DOMAIN EVENTS — Visits (BAG-O)
    // ==========================================

    /**
     * Sitter checked in for a visit → notify owner
     */
    public static function visitCheckIn(User $sitter, User $owner, $visit): void
    {
        self::send(
            userId:    $owner->id,
            type:      'visit_checked_in',
            title:     'Sitter checked in',
            message:   "{$sitter->f_name} started Visit #{$visit->visit_number} for " . ($visit->booking->pet->name ?? 'your pet') . ".",
            actionUrl: route('task.monitor.visit', $visit->id),
            actorId:   $sitter->id,
            meta:      [
                'visit_id'   => $visit->id,
                'booking_id' => $visit->booking_id,
            ],
        );
    }

    /**
     * Sitter checked out for a visit → notify owner
     */
    public static function visitCheckOut(User $sitter, User $owner, $visit): void
    {
        $duration = $visit->duration_minutes
            ? " ({$visit->duration_minutes} min)"
            : '';

        self::send(
            userId:    $owner->id,
            type:      'visit_checked_out',
            title:     'Sitter checked out',
            message:   "{$sitter->f_name} finished Visit #{$visit->visit_number} for " . ($visit->booking->pet->name ?? 'your pet') . "{$duration}.",
            actionUrl: route('task.monitor.visit', $visit->id),
            actorId:   $sitter->id,
            meta:      [
                'visit_id'   => $visit->id,
                'booking_id' => $visit->booking_id,
            ],
        );
    }

    /**
     * Visit completed → notify owner
     */
    public static function visitCompleted(User $sitter, User $owner, $visit): void
    {
        self::send(
            userId:    $owner->id,
            type:      'visit_completed',
            title:     'Visit completed',
            message:   "{$sitter->f_name} completed Visit #{$visit->visit_number} for " . ($visit->booking->pet->name ?? 'your pet') . ".",
            actionUrl: route('task.monitor.visit', $visit->id),
            actorId:   $sitter->id,
            meta:      [
                'visit_id'   => $visit->id,
                'booking_id' => $visit->booking_id,
            ],
        );
    }

    /**
     * All visits completed → owner is ready to rate the sitter
     */
    public static function bookingReadyForReview(User $owner, $booking, User $sitter): void
    {
        self::send(
            userId:    $owner->id,
            type:      'booking_ready_for_review',
            title:     'Ready to rate your sitter ⭐',
            message:   "Tanang visits ni {$sitter->f_name} sa booking {$booking->booking_reference} kay complete na. Leave a review!",
            actionUrl: route('tasks.monitor'),
            actorId:   $sitter->id,
            meta:      [
                'booking_id' => $booking->id,
                'sitter_id'  => $sitter->id,
            ],
        );
    }

    // ==========================================
    // DOMAIN EVENTS — Messaging
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
        );
    }

    // ==========================================
    // DOMAIN EVENTS — Applications & Verification
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
        );
    }

    // ==========================================
    // DOMAIN EVENTS — Payments
    // ==========================================

    public static function paymentPaid(User $sitter, User $owner, float $amount): void
    {
        self::send(
            userId:    $sitter->id,
            type:      Notification::TYPE_PAYMENT_PAID,
            title:     'Payment received',
            message:   "{$owner->f_name} paid ₱" . number_format($amount, 2),
            actionUrl: route('payments.index'),
            actorId:   $owner->id,
            meta:      ['amount' => $amount],
        );
    }

    public static function paymentReleased(User $sitter, float $amount): void
    {
        self::send(
            userId:    $sitter->id,
            type:      Notification::TYPE_PAYMENT_RELEASED,
            title:     'Payout released',
            message:   '₱' . number_format($amount, 2) . ' has been released to you.',
            actionUrl: route('payments.index'),
            meta:      ['amount' => $amount],
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
        );
    }

    public static function claimFiled(User $claimant, string $type): void
    {
        self::notifyAdmins(
            type:      Notification::TYPE_CLAIM_FILED,
            title:     'New protection claim',
            message:   "{$claimant->f_name} submitted a claim ({$type})",
            actionUrl: route('admin.claims'),
        );
    }

    public static function claimApproved(User $claimant, float $amount): void
    {
        self::send(
            userId:    $claimant->id,
            type:      Notification::TYPE_CLAIM_APPROVED,
            title:     'Claim approved ✅',
            message:   'Your protection claim was approved for ₱' . number_format($amount, 2),
            actionUrl: route('complaints.index'),
            meta:      ['amount' => $amount],
        );
    }

    public static function claimRejected(User $claimant, string $reason = ''): void
    {
        self::send(
            userId:    $claimant->id,
            type:      Notification::TYPE_CLAIM_REJECTED,
            title:     'Claim rejected',
            message:   $reason ?: 'Your protection claim was rejected.',
            actionUrl: route('complaints.index'),
        );
    }

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

    /**
     * Admin replied to a user's support message.
     * Called from Admin\SupportInboxController@reply
     */
    public static function supportMessageReplied(
        User $recipient,
        User $admin,
        $supportMessage
    ): void {
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
        );
    }

    /**
     * User submitted a new support message (contact or report).
     * Called from UserHelpSupportController@storeContact / storeReport
     */
    public static function supportMessageSubmitted(
        User $user,
        $supportMessage
    ): void {
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