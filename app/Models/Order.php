<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function applyCoupon(Coupon $coupon)
    {
        if (!$coupon->isValid()) {
            return;
        }

        if ($coupon->discount_type === 'fixed') {
            $this->discounted_price = max($this->total_price - $coupon->discount, 0);
        } elseif ($coupon->discount_type === 'percentage') {
            $this->discounted_price = max($this->total_price * ((100 - $coupon->discount) / 100), 0);
        }

        $this->coupon_id = $coupon->id;
    }
}
