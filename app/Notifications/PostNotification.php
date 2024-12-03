<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PostNotification extends Notification
{
    use Queueable;

    protected $post;
    protected $status;
    /**
     * Create a new notification instance.
     */
    public function __construct($post, $status)
    {
        $this->post = $post;
        $this->status = $status;
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

    // Data to store in the database
    public function toDatabase($notifiable)
    {
        return [
            'post_id' => $this->post->id,
            'status' => $this->status,
        ];
    }

    // Real-time notification broadcasting
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            // 'post_id' => $this->post->id,
            // 'status' => $this->status,
            'message' => 'This is a test broadcast notification.',
        ]);
    }

}
