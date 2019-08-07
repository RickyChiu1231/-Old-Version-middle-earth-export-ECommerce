<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OrderItem;
//  implements ShouldQueue means that this listener is executed asynchronously
class UpdateProductSoldCount implements ShouldQueue
{
    // Laravel will execute the listener's handle method by default, and the triggered event will be used as the parameter of the handle method.
    public function handle(OrderPaid $event)
    {
        // Take the corresponding order from the event object
        $order = $event->getOrder();
        // Preloaded product data
        $order->load('items.product');
        // Loop through the items of the order
        foreach ($order->items as $item) {
            $product   = $item->product;
            // Calculate sales of corresponding products
            $soldCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) {
                    $query->whereNotNull('paid_at');  // associated order status is paid
                })->sum('amount');
            // Update product sales
            $product->update([
                'sold_count' => $soldCount,
            ]);
        }
    }
}
