<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class OrderPaidNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Order pay successful')  // email title
                    ->greeting($this->order->user->name.'Hi there：')
                    ->line('Your order place at '.$this->order->created_at->format('m-d H:i').' has been paid successful') // email content
                    ->action('Check order detail', route('orders.show', [$this->order->id])) // URL with the btn
                    ->success(); // btn colour
    }
}
