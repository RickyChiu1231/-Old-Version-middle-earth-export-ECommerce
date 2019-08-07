<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\OrderPaidNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// Implements ShouldQueue represents an asynchronous listener
class SendOrderPaidMail implements ShouldQueue
{
    public function handle(OrderPaid $event)
    {
        // Take the corresponding order from the event object
        $order = $event->getOrder();
        // Call the notify method to send a notification
        $order->user->notify(new OrderPaidNotification($order));
    }
}
