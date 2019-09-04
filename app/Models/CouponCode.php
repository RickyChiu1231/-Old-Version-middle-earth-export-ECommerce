<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCode extends Model
{
    // Define the supported coupon types in a constant way
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED   => 'fixed amount',
        self::TYPE_PERCENT => 'percent',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];
    protected $casts = [
        'enabled' => 'boolean',
    ];
    // Indicate that these two fields are date types
    protected $dates = ['not_before', 'not_after'];


    public static function findAvailableCode($length = 16)
    {
        do {
            // Generate a random string of the specified length and convert it to uppercase
            $code = strtoupper(Str::random($length));
        // Continue to loop if the generated code already exists
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    protected $appends = ['description'];

    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount > 0) {
            $str = '满'.str_replace('.00', '', $this->min_amount);
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str.'优惠'.str_replace('.00', '', $this->value).'%';
        }

        return $str.'减'.str_replace('.00', '', $this->value);
    }
}
