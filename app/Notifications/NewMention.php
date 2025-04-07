<?php

namespace App\Notifications;

use App\Models\Mention;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class NewMention extends Notification implements ShouldQueue
{
    use Queueable;

    private array $notification;
    private Mention $mention;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $notification, Mention $mention)
    {
        $this->notification = $notification;
        $this->mention = $mention;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->notification;
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->notification['title'],
            'message' => $this->notification['message'],
            'link' => $this->notification['link'],
        ]);
    }
}
