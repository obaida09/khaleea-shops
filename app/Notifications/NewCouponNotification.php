<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCouponNotification extends Notification
{
    use Queueable;

    protected $coupon;

    /**
     * Create a new notification instance.
     */
    public function __construct($coupon)
    {
        $this->coupon = $coupon;
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
            'coupon_id' => $this->coupon->id,
            'code' => $this->coupon->code,
            'discount' => $this->coupon->discount,
            'discount_type' => $this->coupon->discount_type,
            'usage_limit' => $this->coupon->usage_limit,
            'valid_from' => $this->coupon->valid_from,
            'valid_until' => $this->coupon->valid_until,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'coupon_id' => $this->coupon->id,
            'description' => $this->coupon->description,
        ];
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
