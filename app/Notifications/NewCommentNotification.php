<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewCommentNotification extends Notification
{
    use Queueable;

    protected $comment;
    /**
     * Create a new notification instance.
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
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
            'comment_id' => $this->comment->id,
            'content' => $this->comment->content,
            'user_id' => $this->comment->user_id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'comment_id' => $this->comment->id,
            'content' => $this->comment->content,
            'user_id' => $this->comment->user_id,
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
