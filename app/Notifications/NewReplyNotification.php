<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewReplyNotification extends Notification
{
    use Queueable;

    protected $reply;
    /**
     * Create a new notification instance.
     */
    public function __construct($reply)
    {
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'reply_id' => $this->reply->id,
            'reply_body' => $this->reply->body,
            'comment_id' => $this->reply->comment_id,
            'user_id' => $this->reply->user_id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'reply_id' => $this->reply->id,
            'reply_body' => $this->reply->body,
            'comment_id' => $this->reply->comment_id,
            'user_id' => $this->reply->user_id,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
