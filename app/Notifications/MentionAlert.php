<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * Notification class for alerting users about mentions.
 * 
 * This notification can be sent through multiple channels including email, Slack, and in-app notifications.
 * It formats the alert information appropriately for each channel.
 */
class MentionAlert extends Notification implements ShouldQueue
{
    use Queueable;

    private Alert $alert;
    private Collection $mentions;

    /**
     * Create a new notification instance.
     *
     * @param Alert $alert The alert that triggered this notification
     * @param Collection $mentions Collection of mentions that triggered the alert
     */
    public function __construct(Alert $alert, Collection $mentions)
    {
        $this->alert = $alert;
        $this->mentions = $mentions;
    }

    /**
     * Get the notification's delivery channels.
     * 
     * Determines which channels to use based on the user's notification settings
     * and the alert's configured notification channels.
     *
     * @param mixed $notifiable The entity receiving the notification
     * @return array Array of notification channels
     */
    public function via($notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->notificationSetting?->email_notifications) {
            $channels[] = 'mail';
        }
        
        if ($notifiable->notificationSetting?->slack_webhook_url && 
            in_array('slack', $this->alert->notification_channels)) {
            $channels[] = 'slack';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     * 
     * Formats the alert information for email delivery.
     *
     * @param mixed $notifiable The entity receiving the notification
     * @return MailMessage The formatted email message
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Mention Alert')
            ->line('You have a new mention alert.')
            ->line('Alert Type: ' . $this->alert->type)
            ->line('Number of Mentions: ' . $this->mentions->count())
            ->action('View Mentions', url('/mentions'));
    }

    /**
     * Get the Slack representation of the notification.
     * 
     * Formats the alert information for Slack delivery with rich formatting.
     *
     * @param mixed $notifiable The entity receiving the notification
     * @return SlackMessage The formatted Slack message
     */
    public function toSlack($notifiable): SlackMessage
    {
        $mentionCount = $this->mentions->count();
        $mentionText = $mentionCount === 1 ? 'mention' : 'mentions';
        
        return (new SlackMessage)
            ->content("New Alert: {$this->alert->name}")
            ->attachment(function ($attachment) use ($mentionCount, $mentionText) {
                $attachment
                    ->title('Alert Details')
                    ->fields([
                        'Alert Type' => $this->alert->type,
                        'Number of Mentions' => "{$mentionCount} {$mentionText}",
                        'Description' => $this->alert->description,
                    ]);
            });
    }

    /**
     * Get the array representation of the notification.
     * 
     * Used for database storage of the notification.
     *
     * @param mixed $notifiable The entity receiving the notification
     * @return array Array representation of the notification
     */
    public function toArray($notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'alert_type' => $this->alert->type,
            'mention_count' => $this->mentions->count(),
        ];
    }
} 