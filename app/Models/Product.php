<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_category_id',
        'image',
        'name',
        'description',
        'price',
        'rating',
        'is_popular',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if(Auth::user()->role !== 'admin'){
                $model->user_id = Auth::user()->id;
            }

        });

        static::updating(function ($model) {
            if(Auth::user()->role !== 'admin'){
                $model->user_id = Auth::user()->id;
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that the product belongs to.
     */
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /**
     * Get the ingredients associated with the product.
     */
    public function productIngredient()
    {
        return $this->hasMany(ProductIngredient::class);
    }

    /**
     * Get the transaction details associated with the product.
     */
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
