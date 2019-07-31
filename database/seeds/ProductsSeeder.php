<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 30 product
        $products = factory(\App\Models\Product::class, 30)->create();
        foreach ($products as $product) {
            // Create 3 SKU
            $skus = factory(\App\Models\ProductSku::class, 3)->create(['product_id' => $product->id]);
            // Find the SKU with the lowest price and set the product with this price
            $product->update(['price' => $skus->min('price')]);
        }
    }
}
