<?php

use App\Models\CouponCode;
use Faker\Generator as Faker;

$factory->define(CouponCode::class, function (Faker $faker) {
    // Randomly get a type
    $type  = $faker->randomElement(array_keys(CouponCode::$typeMap));
    // Generate a corresponding discount based on the type obtained
    $value = $type === CouponCode::TYPE_FIXED ? random_int(1, 200) : random_int(1, 50);

    // If it is a fixed amount, the minimum order amount must be higher than the discount amount of $0.01.
    if ($type === CouponCode::TYPE_FIXED) {
        $minAmount = $value + 0.01;
    } else {
        // If it is a percentage discount, there is a 50% probability that a minimum order amount is not required
        if (random_int(0, 100) < 50) {
            $minAmount = 0;
        } else {
            $minAmount = random_int(100, 1000);
        }
    }

    return [
        'name'       => join(' ', $faker->words), // Randomly generated name
        'code'       => CouponCode::findAvailableCode(), // Call the coupon generation method
        'type'       => $type,
        'value'      => $value,
        'total'      => 1000,
        'used'       => 0,
        'min_amount' => $minAmount,
        'not_before' => null,
        'not_after'  => null,
        'enabled'    => true,
    ];
});
