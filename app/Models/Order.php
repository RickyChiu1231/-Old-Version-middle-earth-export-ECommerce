<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => 'Pending',
        self::REFUND_STATUS_APPLIED    => 'Applied',
        self::REFUND_STATUS_PROCESSING => 'Processing',
        self::REFUND_STATUS_SUCCESS    => 'Success',
        self::REFUND_STATUS_FAILED     => 'Failed',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => 'Pending',
        self::SHIP_STATUS_DELIVERED => 'Delivered',
        self::SHIP_STATUS_RECEIVED  => 'Received',
    ];

    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();
        // Listen for model creation events, triggered before writing to the database
        static::creating(function ($model) {
            // If the model's no field is empty
            if (!$model->no) {
                // Call findAvailableNo to generate the order serial number
                $model->no = static::findAvailableNo();
                // If the build fails, the creation of the order is terminated.
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function findAvailableNo()
    {
        // Order serial number prefix
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // Randomly generate 6 digits
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // Determine if it already exists
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }

    public static function getAvailableRefundNo()
    {
        do {
            // Uuid class can be used to generate strings with large probability of not repeating
            $no = Uuid::uuid4()->getHex();
            // In order to avoid duplication, need to look in the database after the build to see if the same refund order number already exists.
        } while (self::query()->where('refund_no', $no)->exists());

        return $no;
    }
}
