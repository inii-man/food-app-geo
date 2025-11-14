<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'merchant_id',
        'total_price',
        'status',
        'address',
        'delivery_latitude',
        'delivery_longitude',
        'distance_km',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
        'distance_km' => 'decimal:2',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the merchant for the order.
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
