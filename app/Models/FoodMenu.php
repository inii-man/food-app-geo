<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodMenu extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'merchant_id',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the merchant that owns the food menu.
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the order items for the food menu.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
