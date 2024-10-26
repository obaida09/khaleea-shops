<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $status;
    /**
     * Create a new notification instance.
     */
    public function __construct($order, $status)
    {
        $this->order = $order;
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
            'order_id' => $this->order->id,
            'status' => $this->status,
            'order_total' => $this->order->total_price,
            'updated_at' => now(),
        ];
    }

    // Real-time notification broadcasting
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'status' => $this->status,
            'order_total' => $this->order->total_price,
            'updated_at' => now(),
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
