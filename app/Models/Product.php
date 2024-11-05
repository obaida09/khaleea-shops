<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
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
            $model->slug = Str::slug($model->name);
        });
    }

    public function discount()
    {
        return $this->hasOne(ProductDiscount::class)->whereNull('product_id')
            ->orWhere('product_id', $this->id)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            });
    }

    public function getDiscountedPriceAttribute()
    {
        $discount = $this->discount;
        $price = $this->price;

        if ($discount) {
            $price -= ($price * $discount->percentage) / 100;
        }

        return max($price, 0);
    }

    public function getProductsByPreferredSeasons()
    {
        $products = Product::join('shops', 'products.shop_id', '=', 'shops.id')
            ->where(function ($query) {
                $query->whereColumn('products.season', 'shops.season')
                    ->orWhere('products.season', 'all')
                    ->orWhere('shops.season', 'all');
            })
            ->select('products.*')
            ->get();

        return response()->json($products);
    }

    public function status()
    {
        return $this->status ? 1 : 0;
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product', 'product_id', 'order_id');
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'product_user')->withTimestamps();
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }

    // Method to calculate the average rating
    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }
}
