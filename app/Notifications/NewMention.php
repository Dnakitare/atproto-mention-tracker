<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMention extends Notification implements ShouldQueue
{
    use Queueable;

    private array $mentionData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $mentionData)
    {
        $this->mentionData = $mentionData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->mentionData['title'])
            ->line($this->mentionData['message'])
            ->action('View Post', $this->mentionData['link'])
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->mentionData['title'],
            'message' => $this->mentionData['message'],
            'link' => $this->mentionData['link'],
            'mention' => $this->mentionData['mention'],
        ];
    }
}
