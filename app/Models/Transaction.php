<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                // Kalau user login & bukan admin → isi user_id
                if (Auth::user()->role !== 'admin') {
                    $model->user_id = Auth::id();
                }
            }
        });

        static::updating(function ($model) {
            if(Auth::user()->role !== 'admin'){
                $model->user_id = Auth::user()->id;
            }
        });
    }

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'phone_number',
        'table_number',
        'payment_method',
        'total_price',
        'status',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction details associated with the transaction.
     */
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // /**
    //  * Get the product associated with the transaction.
    //  */
    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }
}
