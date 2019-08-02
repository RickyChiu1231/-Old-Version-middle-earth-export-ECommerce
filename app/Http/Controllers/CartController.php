<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;

class CartController extends Controller
{
    public function add(AddCartRequest $request)
    {
        $user   = $request->user();
        $skuId  = $request->input('sku_id');
        $amount = $request->input('amount');

        // Check from the database whether the item is already in the shopping cart.
        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {

            // If there is one, directly stack the number of items
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        } else {

            // Otherwise create a new shopping cart record
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return [];
    }
}
