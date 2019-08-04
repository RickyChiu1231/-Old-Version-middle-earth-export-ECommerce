<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Exceptions\InvalidRequestException;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Jobs\CloseOrder;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
        $user  = $request->user();
        // Start a database transaction
        $order = \DB::transaction(function () use ($user, $request) {
            $address = UserAddress::find($request->input('address_id'));
            // Update the last usage time of this address
            $address->update(['last_used_at' => Carbon::now()]);
            // Crate an order
            $order   = new Order([
                'address'      => [ // add the address information into the order
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $request->input('remark'),
                'total_amount' => 0,
            ]);
            // Link order to the current user
            $order->user()->associate($user);
            // save to the database
            $order->save();

            $totalAmount = 0;
            $items       = $request->input('items');

            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                // Create an OrderItem and associate directly with the current order
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
        throw new InvalidRequestException('该商品库存不足');
            }
        }

            // Update the total amount of the order
            $order->update(['total_amount' => $totalAmount]);

            // Remove the ordered item from the shopping cart
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;
        });

        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }

    public function index(Request $request)
    {
        $orders = Order::query()

            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order, Request $request)
    {
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }
}
