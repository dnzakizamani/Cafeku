<?php

namespace App\Models;

// use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class ProductCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'icon',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if(Auth::user()->role !== 'admin'){
                $model->user_id = Auth::user()->id;
            }

            $model->slug = Str::slug($model->name);
        });

        static::updating(function ($model) {
            if(Auth::user()->role !== 'admin'){
                $model->user_id = Auth::user()->id;
            }
            $model->slug = Str::slug($model->name);
        });
    }

    /**
     * Get the user that owns the product category.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the product category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    
}
