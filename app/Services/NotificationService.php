<?php

namespace App\Services;

use App\Models\Mention;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send notification for a new mention
     */
    public function sendMentionNotification(User $user, Mention $mention): void
    {
        $settings = $user->notificationSetting;

        if (!$settings) {
            return;
        }

        if ($settings->in_app_notifications) {
            $this->sendInAppNotification($user, $mention);
        }

        if ($settings->email_notifications) {
            $this->sendEmailNotification($user, $mention);
        }
    }

    /**
     * Send in-app notification
     */
    private function sendInAppNotification(User $user, Mention $mention): void
    {
        $notification = [
            'title' => 'New Mention on Bluesky',
            'message' => "You were mentioned by @{$mention->author_handle}",
            'link' => "https://bsky.app/profile/{$mention->author_handle}/post/{$mention->post_id}",
            'mention' => $mention,
        ];

        Notification::send($user, new \App\Notifications\NewMention($notification));
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(User $user, Mention $mention): void
    {
        $emailData = [
            'user' => $user,
            'mention' => $mention,
            'postUrl' => "https://bsky.app/profile/{$mention->author_handle}/post/{$mention->post_id}",
        ];

        Mail::to($user->email)
            ->queue(new \App\Mail\NewMention($emailData));
    }

    /**
     * Send daily digest email
     */
    public function sendDailyDigest(User $user): void
    {
        $settings = $user->notificationSetting;

        if (!$settings || !$settings->email_notifications) {
            return;
        }

        $recentMentions = $user->mentions()
            ->where('created_at', '>=', now()->subDay())
            ->get();

        if ($recentMentions->isEmpty()) {
            return;
        }

        $emailData = [
            'user' => $user,
            'mentions' => $recentMentions,
        ];

        Mail::to($user->email)
            ->queue(new \App\Mail\DailyDigest($emailData));
    }
} 