<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'end_date',
        'is_active',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if(Auth::user()->role !== 'admin'){
                $model->user_id = Auth::user()->id;
                $model->end_date = now()->addMonth(); // Set default end date to one month from now
            }
        });

        
    }

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product associated with the subscription.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the subscription payments associated with the subscription.
     */
    public function subscriptionPayments()
    {
        return $this->hasMany(SubscriptionPayment::class);  
    }
}
