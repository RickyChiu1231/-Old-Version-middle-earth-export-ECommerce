<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        // Set the delay time, the parameters of the delay() method represent how many seconds to execute
        $this->delay($delay);
    }

    // Define the specific execution logic of this task class
    // The handle() method is called when the queue handler takes the task out of the queue.
    public function handle()
    {
        // Determine if the corresponding order has been paid
        // If order have been paid, tehn do not need to close the order and exit directly.
        if ($this->order->paid_at) {
            return;
        }
        // Execute sql through transaction
        \DB::transaction(function() {
            // Mark the closing field of the order as true, ie close the order
            $this->order->update(['closed' => true]);
            // Loop through the item SKUs in the order and add the quantity in the order back to the SKU's inventory
            foreach ($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
            }
        });
    }
}
