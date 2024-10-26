<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price', 'status'];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
