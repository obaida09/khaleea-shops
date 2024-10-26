<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function taggables()
    {
        return $this->morphToMany(Product::class, 'taggable');
    }
}
