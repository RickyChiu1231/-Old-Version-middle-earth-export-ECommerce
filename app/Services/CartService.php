<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

class CartService
{
    public function get()
    {
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    public function add($skuId, $amount)
    {
        $user = Auth::user();
        // Check from the database whether the item is already in the shopping cart.
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            // If there is one, directly stack the number of items
            $item->update([
                'amount' => $item->amount + $amount,
            ]);
        } else {
            // Otherwise create a new shopping cart record
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }

        return $item;
    }

    public function remove($skuIds)
    {
        // Can either pass a single ID or pass an ID array
        if (!is_array($skuIds)) {
            $skuIds = [$skuIds];
        }
        Auth::user()->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
    }
}
