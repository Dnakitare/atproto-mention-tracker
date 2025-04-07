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

        if (!$settings || !$settings->daily_digest) {
            return;
        }

        $yesterday = now()->subDay();
        $mentions = $user->mentions()
            ->whereDate('post_indexed_at', $yesterday)
            ->orderBy('post_indexed_at', 'desc')
            ->get();

        $emailData = [
            'user' => $user,
            'mentions' => $mentions,
            'date' => $yesterday->format('F j, Y'),
        ];

        Mail::to($user->email)
            ->queue(new \App\Mail\DailyDigest($emailData));
    }

    /**
     * Send a notification to a user
     */
    public function sendNotification(User $user, string $type, array $data): void
    {
        // Get user's notification settings
        $settings = $user->notificationSetting;
        
        if (!$settings) {
            return;
        }
        
        // Check if notification type is enabled
        if (!isset($settings->notification_preferences[$type]) || !$settings->notification_preferences[$type]) {
            return;
        }
        
        // Send notification based on type
        switch ($type) {
            case 'new_mention':
                $user->notify(new NewMention($data['mention']));
                break;
            case 'alert':
                $user->notify(new MentionAlert($data['alert'], $data['mentions']));
                break;
            case 'digest':
                $user->notify(new MentionDigest($data['mentions']));
                break;
        }
    }
    
    /**
     * Send a digest notification to a user
     */
    public function sendDigest(User $user): void
    {
        // Get user's notification settings
        $settings = $user->notificationSetting;
        
        if (!$settings || !$settings->email_notifications || $settings->email_frequency !== 'digest') {
            return;
        }
        
        // Get mentions since last digest
        $mentions = $user->mentions()
            ->where('created_at', '>', $settings->last_digest_sent_at ?? now()->subDay())
            ->get();
        
        if ($mentions->isEmpty()) {
            return;
        }
        
        // Send digest notification
        $this->sendNotification($user, 'digest', [
            'mentions' => $mentions,
        ]);
        
        // Update last digest sent at
        $settings->update([
            'last_digest_sent_at' => now(),
        ]);
    }
} 