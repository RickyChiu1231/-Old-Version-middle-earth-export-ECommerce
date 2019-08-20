<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{

    protected $table = 'products';

    protected $fillable = [
                    'title', 'description', 'image', 'on_sale',
                    'rating', 'sold_count', 'review_count', 'price'
    ];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale is a boolean
    ];
    // link with product sku
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function getImageUrlAttribute()
    {
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }

        public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
