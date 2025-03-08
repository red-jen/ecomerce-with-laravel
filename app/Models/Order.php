<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'shipping_cost',
        'total',
        'payment_method',
        'payment_status',
        'payment_id',
        'shipping_name',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zipcode',
        'shipping_phone',
        'shipping_email',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    // Generate a unique order number
    public static function generateOrderNumber()
    {
        $orderNumber = 'ORD-' . strtoupper(uniqid());
        
        // Make sure it's unique
        while (self::where('order_number', $orderNumber)->exists()) {
            $orderNumber = 'ORD-' . strtoupper(uniqid());
        }
        
        return $orderNumber;
    }
}