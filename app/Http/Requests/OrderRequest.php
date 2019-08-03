<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class OrderRequest extends Request
{
    public function rules()
    {
        return [
            // Determine whether the address ID submitted by the user exists in the database and belongs to the current user.

            'address_id'     => [
                'required',
                Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id),
            ],
            'items'  => ['required', 'array'],
            'items.*.sku_id' => [ // Check the sku_id parameter of each subarray under the items array
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        return $fail('Product no exist');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('Product no on sale');
                    }
                    if ($sku->stock === 0) {
                        return $fail('Product sold out');
                    }
                    // Get current index
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index = $m[1];
                    // Find the number of purchases submitted by the user based on the index
                    $amount = $this->input('items')[$index]['amount'];
                    if ($amount > 0 && $amount > $sku->stock) {
                        return $fail('Insufficient inventory');
                    }
                },
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ];
    }
}
