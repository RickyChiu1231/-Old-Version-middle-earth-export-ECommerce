<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use App\Events\OrderPaid;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order, Request $request)
    {
        // Determine if the order belongs to the current user
        $this->authorize('own', $order);
        // Order has been paid or closed
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('Order status is incorrect');
        }

        // Call the Alipay's web payment
        return app('alipay')->web([
            'out_trade_no' => $order->no, // order No
            'total_amount' => $order->total_amount, // Order amount
            'subject'      => 'Middle-Earth Shop payment ：'.$order->no, // Order title
        ]);
    }

    // Frontend callback page
    public function alipayReturn()
    {
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => 'data incorrect']);
        }

        return view('pages.success', ['msg' => 'Payment Successful']);
    }

    // Server callback
    public function alipayNotify()
    {
        // Verify input parameters
        $data  = app('alipay')->verify();
        // If the order status is not successful or ends, then the subsequent logic is not taken.
        // All transaction status
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no get the order serial number and query it in the database
        $order = Order::where('no', $data->out_trade_no)->first();

        if (!$order) {
            return 'fail';
        }
        // If the status of this order is already paid
        if ($order->paid_at) {
            // Return data to Alipay
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(), // Payment time
            'payment_method' => 'alipay', // Payment method
            'payment_no'     => $data->trade_no, // Alipay order No
        ]);

        $this->afterPaid($order);

        return app('alipay')->success();

    }

    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }
}
