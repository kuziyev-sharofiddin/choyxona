<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'reservation_id',
        'customer_id',
        'waiter_id',
        'subtotal',
        'tax_amount',
        'waiter_commission',
        'discount_amount',
        'total_amount',
        'status',
        'notes',
        'order_time',
        'served_time'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'waiter_commission' => 'decimal:2', // Add this
        'total_amount' => 'decimal:2',
        'order_time' => 'datetime',
        'served_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $order->order_time = now();
        });
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculateTotal()
    {
        $this->subtotal = $this->items()->sum('total_price');
        $this->tax_amount = 0; // No tax

        // Use config for commission rate
        $commissionRate = config('choyxona.waiter_commission_rate', 0.10);
        $this->waiter_commission = $this->subtotal * $commissionRate;

        $this->total_amount = $this->subtotal + $this->waiter_commission - $this->discount_amount;
        $this->save();
    }
}
