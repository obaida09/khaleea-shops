<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount',
        'used',
        'discount_type',
        'usage_limit',
        'valid_from',
        'valid_until'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isValid()
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }
}
