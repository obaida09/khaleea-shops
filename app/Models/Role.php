<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Model;

class Role extends SpatieRole
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
}
